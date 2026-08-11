package ro.dianasoft.grefier

import android.annotation.SuppressLint
import android.app.Activity
import android.content.ActivityNotFoundException
import android.content.ContentValues
import android.content.Context
import android.content.Intent
import android.content.pm.PackageManager
import android.net.Uri
import android.os.Build
import android.os.Bundle
import android.os.Environment
import android.os.Handler
import android.os.Looper
import android.provider.MediaStore
import android.util.Base64
import android.view.ViewGroup
import android.webkit.CookieManager
import android.webkit.JavascriptInterface
import android.webkit.ValueCallback
import android.webkit.WebChromeClient
import android.webkit.WebResourceError
import android.webkit.WebResourceRequest
import android.webkit.WebView
import android.webkit.WebViewClient
import android.widget.Toast
import org.json.JSONObject
import org.json.JSONTokener
import java.io.File
import java.io.FileOutputStream

/**
 * Fereastra aplicatiei: aplicatia web, imbracata in haine de telefon.
 *
 * Nu se scrie aici a doua oara ce exista deja pe app.dianasoft.ro. Ce aduce in
 * plus programul de fata sunt tocmai lucrurile pe care o pagina web nu le poate
 * face singura pe un telefon: sa fie trezita cand vine o modificare la un dosar,
 * sa stea pe ecranul de start ca orice aplicatie, si sa salveze documentele in
 * dosarul de descarcari al telefonului.
 *
 * Legatura cu aplicatia web se face intr-un singur loc: din cand in cand se
 * citeste jetonul pastrat de ea in „localStorage” si, cand se schimba, telefonul
 * se anunta la server. Aplicatia web nu stie de existenta acestui program si nu
 * trebuie schimbata cu nimic pentru el.
 */
class Fereastra : Activity() {

    private lateinit var pagina: WebView
    private var alegereaFisierului: ValueCallback<Array<Uri>>? = null

    private val ceas = Handler(Looper.getMainLooper())

    /** Din cand in cand se intreaba pagina daca jetonul ei s-a schimbat. */
    private val urmaresteJetonul = object : Runnable {
        override fun run() {
            citesteJetonul()
            ceas.postDelayed(this, LA_CATE)
        }
    }

    companion object {
        const val TINTA = "tinta"

        private const val COD_FISIER = 1
        private const val COD_NOTIFICARI = 2
        private const val LA_CATE = 20_000L
    }

    override fun onCreate(stare: Bundle?) {
        super.onCreate(stare)

        Notificari.pregatesteCanalul(this)
        cereVoieLaNotificari()

        pagina = WebView(this)
        setContentView(
            pagina,
            ViewGroup.LayoutParams(
                ViewGroup.LayoutParams.MATCH_PARENT,
                ViewGroup.LayoutParams.MATCH_PARENT
            )
        )

        pregatestePagina()

        if (stare != null) {
            pagina.restoreState(stare)
        } else {
            pagina.loadUrl(unde(intent))
        }
    }

    override fun onResume() {
        super.onResume()

        Dispozitiv.cereJetonulFirebase(this)
        ceas.postDelayed(urmaresteJetonul, LA_CATE)
    }

    override fun onPause() {
        super.onPause()

        ceas.removeCallbacks(urmaresteJetonul)
    }

    /** Alerta apasata cand aplicatia era deja deschisa. */
    override fun onNewIntent(intentie: Intent?) {
        super.onNewIntent(intentie)

        intentie ?: return

        if (intentie.hasExtra(TINTA)) {
            pagina.loadUrl(unde(intentie))
        }
    }

    override fun onSaveInstanceState(stare: Bundle) {
        super.onSaveInstanceState(stare)

        pagina.saveState(stare)
    }

    /** Butonul de intoarcere merge inapoi in aplicatie, nu inchide fereastra. */
    @Suppress("DEPRECATION")
    override fun onBackPressed() {
        if (pagina.canGoBack()) {
            pagina.goBack()

            return
        }

        super.onBackPressed()
    }

    /** Unde se deschide aplicatia: acasa, sau la locul aratat de alerta. */
    private fun unde(intentie: Intent?): String {
        val tinta = intentie?.getStringExtra(TINTA)

        return if (tinta.isNullOrBlank()) BuildConfig.ADRESA else BuildConfig.ADRESA + tinta
    }

    @SuppressLint("SetJavaScriptEnabled")
    private fun pregatestePagina() {
        val setari = pagina.settings

        setari.javaScriptEnabled = true
        setari.domStorageEnabled = true
        setari.loadWithOverviewMode = true
        setari.useWideViewPort = true
        setari.builtInZoomControls = true
        setari.displayZoomControls = false

        // Serverul poate afla astfel ca cererea vine din aplicatia de telefon.
        setari.userAgentString = setari.userAgentString + " GrefierAlert/" + BuildConfig.VERSION_NAME

        CookieManager.getInstance().setAcceptThirdPartyCookies(pagina, true)

        pagina.webViewClient = paznicul()
        pagina.webChromeClient = alegatorulDeFisiere()
        pagina.addJavascriptInterface(Puntea(), "Aplicatia")

        pagina.setDownloadListener { adresa, _, dispozitie, tip, _ ->
            descarca(adresa, dispozitie, tip)
        }
    }

    private fun paznicul(): WebViewClient = object : WebViewClient() {

        /*
         * In fereastra ramane numai aplicatia noastra. Orice alt loc — un link
         * catre portal.just.ro, o adresa de email — pleaca la programul care
         * stie sa-l deschida, ca sa nu ajunga omul intr-un browser fara bara de
         * adresa, din care nu mai stie cum sa se intoarca.
         */
        override fun shouldOverrideUrlLoading(fereastra: WebView, cerere: WebResourceRequest): Boolean {
            val adresa = cerere.url

            if (adresa.host != null && adresa.host == Uri.parse(BuildConfig.ADRESA).host) {
                return false
            }

            deschideInAfara(adresa)

            return true
        }

        override fun onPageFinished(fereastra: WebView, adresa: String) {
            citesteJetonul()
        }

        override fun onReceivedError(
            fereastra: WebView,
            cerere: WebResourceRequest,
            greseala: WebResourceError
        ) {
            // Doar cand cade pagina intreaga; o imagine lipsa nu e o nenorocire.
            if (!cerere.isForMainFrame) {
                return
            }

            arataPaginaDeEroare()
        }
    }

    private fun alegatorulDeFisiere(): WebChromeClient = object : WebChromeClient() {

        override fun onShowFileChooser(
            fereastra: WebView,
            raspuns: ValueCallback<Array<Uri>>,
            parametri: FileChooserParams
        ): Boolean {
            // O alegere lasata neterminata trebuie inchisa, altfel pagina asteapta la nesfarsit.
            alegereaFisierului?.onReceiveValue(null)
            alegereaFisierului = raspuns

            return try {
                startActivityForResult(parametri.createIntent(), COD_FISIER)

                true
            } catch (e: ActivityNotFoundException) {
                alegereaFisierului = null

                false
            }
        }
    }

    @Suppress("DEPRECATION")
    override fun onActivityResult(cod: Int, rezultat: Int, date: Intent?) {
        super.onActivityResult(cod, rezultat, date)

        if (cod != COD_FISIER) {
            return
        }

        alegereaFisierului?.onReceiveValue(
            WebChromeClient.FileChooserParams.parseResult(rezultat, date)
        )

        alegereaFisierului = null
    }

    private fun deschideInAfara(adresa: Uri) {
        try {
            startActivity(Intent(Intent.ACTION_VIEW, adresa))
        } catch (e: ActivityNotFoundException) {
            Toast.makeText(this, R.string.fara_program, Toast.LENGTH_SHORT).show()
        }
    }

    /**
     * Jetonul aplicatiei web, citit de unde il tine ea.
     *
     * Se citeste si societatea aleasa: serverul leaga telefonul de ea, iar fara
     * ea alertele ar veni fara sa se stie ale carui client sunt.
     */
    private fun citesteJetonul() {
        if (!pagina.url.orEmpty().startsWith(BuildConfig.ADRESA)) {
            return
        }

        val intrebarea = "(function(){try{return JSON.stringify({" +
            "jeton:localStorage.getItem('access_token')," +
            "societate:localStorage.getItem('societateaCurenta')" +
            "})}catch(e){return null}})()"

        pagina.evaluateJavascript(intrebarea) { raspuns ->
            val citit = try {
                // Raspunsul vine ca text imbracat in ghilimele; se despacheteaza.
                val dezbracat = JSONTokener(raspuns).nextValue()

                if (dezbracat is String) JSONObject(dezbracat) else null
            } catch (e: Exception) {
                null
            }

            citit ?: return@evaluateJavascript

            val jeton = citit.optString("jeton").takeIf { it.isNotBlank() && it != "null" }
            val societate = citit.optString("societate").takeIf { it.isNotBlank() && it != "null" }

            Dispozitiv.tineMinteJetonulAplicatiei(this, jeton, idulSocietatii(societate))
        }
    }

    /** Societatea e pastrata de aplicatia web ca obiect intreg; de aici doar codul ei. */
    private fun idulSocietatii(societate: String?): String? {
        societate ?: return null

        return try {
            JSONObject(societate).optString("id").takeIf { it.isNotBlank() && it != "null" }
        } catch (e: Exception) {
            null
        }
    }

    private fun cereVoieLaNotificari() {
        if (Build.VERSION.SDK_INT < Build.VERSION_CODES.TIRAMISU) {
            return
        }

        val voie = checkSelfPermission(android.Manifest.permission.POST_NOTIFICATIONS)

        if (voie != PackageManager.PERMISSION_GRANTED) {
            requestPermissions(arrayOf(android.Manifest.permission.POST_NOTIFICATIONS), COD_NOTIFICARI)
        }
    }

    private fun arataPaginaDeEroare() {
        val pagina_ = "<!doctype html><meta charset=\"utf-8\">" +
            "<meta name=\"viewport\" content=\"width=device-width,initial-scale=1\">" +
            "<style>body{font-family:system-ui,sans-serif;margin:0;display:flex;" +
            "min-height:100vh;align-items:center;justify-content:center;background:#f8f8f8;color:#333}" +
            "div{text-align:center;padding:24px}h1{font-size:19px;margin:0 0 8px}" +
            "p{font-size:15px;color:#777;margin:0 0 20px}" +
            "button{font-size:16px;padding:12px 26px;border:0;border-radius:8px;" +
            "background:#7367f0;color:#fff}</style>" +
            "<div><h1>" + getString(R.string.fara_legatura_titlu) + "</h1>" +
            "<p>" + getString(R.string.fara_legatura_lamurire) + "</p>" +
            "<button onclick=\"Aplicatia.reincarca()\">" + getString(R.string.incearca_din_nou) +
            "</button></div>"

        pagina.loadDataWithBaseURL(null, pagina_, "text/html", "utf-8", null)
    }

    /**
     * Documentele pe care le da aplicatia web.
     *
     * Cele mai multe nu sunt fisiere asezate undeva pe server, ci documente
     * facute pe loc in pagina — adrese „blob:”, pe care programul de descarcari
     * al telefonului nu le poate deschide, fiindca traiesc numai inauntrul
     * paginii. De aceea sunt aduse prin pagina insasi si scrise de aici.
     */
    private fun descarca(adresa: String, dispozitie: String?, tip: String?) {
        /*
         * Documentele facute in pagina si cele de pe serverul nostru se aduc
         * amandoua prin pagina: numai ea are dreptul sa citeasca un „blob:”, si
         * tot numai ea poarta jetonul cu care serverul da documentul.
         */
        if (adresa.startsWith("blob:") || adresa.startsWith(BuildConfig.ADRESA)) {
            aduBlobul(adresa, numeleDocumentului(dispozitie, tip))

            return
        }

        // Un fisier de pe alt server nu se poate citi din pagina — il preia
        // programul care stie sa-l deschida.
        deschideInAfara(Uri.parse(adresa))
    }

    private fun aduBlobul(adresa: String, nume: String) {
        val cererea = "(function(){" +
            "var c=new XMLHttpRequest();" +
            "c.open('GET','" + adresa + "',true);" +
            "c.responseType='blob';" +
            "c.onload=function(){" +
            "var m=new FileReader();" +
            "m.onloadend=function(){Aplicatia.salveaza(" + JSONObject.quote(nume) + ",m.result)};" +
            "m.readAsDataURL(c.response)};" +
            "c.onerror=function(){Aplicatia.nuSAPutut()};" +
            "c.send()})()"

        pagina.evaluateJavascript(cererea, null)
    }

    /** Numele sub care se salveaza, cand serverul nu spune unul. */
    private fun numeleDocumentului(dispozitie: String?, tip: String?): String {
        val dinAntet = dispozitie?.let {
            Regex("filename\\*?=\"?([^\";]+)\"?", RegexOption.IGNORE_CASE).find(it)?.groupValues?.get(1)
        }

        if (!dinAntet.isNullOrBlank()) {
            return dinAntet.substringAfterLast('/').trim()
        }

        val coada = when {
            tip == null -> "bin"
            tip.contains("pdf") -> "pdf"
            tip.contains("spreadsheet") || tip.contains("excel") -> "xlsx"
            tip.contains("xml") -> "xml"
            tip.contains("zip") -> "zip"
            else -> "bin"
        }

        return "document-" + System.currentTimeMillis() + "." + coada
    }

    /** Legatura pe care pagina o poate chema: „Aplicatia.…” */
    private inner class Puntea {

        @JavascriptInterface
        fun reincarca() {
            runOnUiThread { pagina.loadUrl(BuildConfig.ADRESA) }
        }

        /** Aplicatia web poate afla ca ruleaza in telefon, si ce versiune e. */
        @JavascriptInterface
        fun versiune(): String = BuildConfig.VERSION_NAME

        @JavascriptInterface
        fun nuSAPutut() {
            runOnUiThread {
                Toast.makeText(this@Fereastra, R.string.descarcare_esuata, Toast.LENGTH_LONG).show()
            }
        }

        /** Documentul adus de pagina, scris in telefon. */
        @JavascriptInterface
        fun salveaza(nume: String, continut: String) {
            val virgula = continut.indexOf(',')

            if (!continut.startsWith("data:") || virgula < 0) {
                nuSAPutut()

                return
            }

            val octeti = try {
                Base64.decode(continut.substring(virgula + 1), Base64.DEFAULT)
            } catch (e: IllegalArgumentException) {
                nuSAPutut()

                return
            }

            val unde = scrie(nume, octeti)

            runOnUiThread {
                val vorba = if (unde == null) {
                    getString(R.string.descarcare_esuata)
                } else {
                    getString(R.string.descarcat_in, unde)
                }

                Toast.makeText(this@Fereastra, vorba, Toast.LENGTH_LONG).show()
            }
        }
    }

    /**
     * Scrierea documentului, pe unde da voie telefonul.
     *
     * De la Android 10, dosarul de descarcari se atinge numai prin catalogul
     * sistemului, si atunci nu mai trebuie ceruta nicio invoire. Pe telefoanele
     * mai vechi documentul ramane in dosarul aplicatiei — tot fara invoire, si
     * tot de gasit, doar ca prin lista de fisiere a aplicatiei.
     */
    private fun scrie(nume: String, octeti: ByteArray): String? {
        try {
            if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.Q) {
                val date = ContentValues()
                date.put(MediaStore.Downloads.DISPLAY_NAME, nume)
                date.put(MediaStore.Downloads.IS_PENDING, 1)

                val catalog = contentResolver
                val locul = catalog.insert(MediaStore.Downloads.EXTERNAL_CONTENT_URI, date) ?: return null

                catalog.openOutputStream(locul)?.use { it.write(octeti) } ?: return null

                date.clear()
                date.put(MediaStore.Downloads.IS_PENDING, 0)
                catalog.update(locul, date, null, null)

                return getString(R.string.dosarul_de_descarcari)
            }

            val dosar = getExternalFilesDir(Environment.DIRECTORY_DOWNLOADS) ?: return null
            val fisier = File(dosar, nume)

            FileOutputStream(fisier).use { it.write(octeti) }

            return fisier.absolutePath
        } catch (e: Exception) {
            return null
        }
    }
}
