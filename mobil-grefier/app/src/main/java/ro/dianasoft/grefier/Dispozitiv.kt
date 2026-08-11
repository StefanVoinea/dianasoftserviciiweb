package ro.dianasoft.grefier

import android.content.Context
import android.os.Build
import android.util.Log
import com.google.firebase.messaging.FirebaseMessaging
import org.json.JSONObject
import java.io.BufferedReader
import java.net.HttpURLConnection
import java.net.URL

/**
 * Telefonul, anuntat la server ca sa poata primi alerte.
 *
 * Sunt doua jetoane cu totul deosebite, si de aceea usor de incurcat:
 *
 *   - jetonul aplicatiei web — cu el se dovedeste serverului cine e omul;
 *   - jetonul Firebase — adresa la care Google stie sa trezeasca acest telefon.
 *
 * Amandoua se schimba pe parcurs, fiecare din motivul lui: primul la conectare
 * si deconectare, al doilea la reinstalare sau cand Google hotaraste asa. De
 * aceea nu se anunta o singura data la pornire, ci ori de cate ori se schimba
 * vreunul — si se tine minte ce s-a anuntat ultima oara, ca sa nu se bata la
 * usa serverului la fiecare douazeci de secunde degeaba.
 */
object Dispozitiv {

    private const val DOSAR = "grefier"

    private const val JETON_APP = "jeton_app"
    private const val SOCIETATE = "societate"
    private const val JETON_FCM = "jeton_fcm"
    private const val ANUNTAT = "anuntat"

    private const val JURNAL = "GrefierDispozitiv"

    /** Jetonul citit din aplicatia web. Lipsa lui inseamna om deconectat. */
    fun tineMinteJetonulAplicatiei(context: Context, jeton: String?, societate: String?) {
        val carnet = carnet(context)

        if (jeton == null) {
            // S-a deconectat: telefonul nu mai are de ce sa primeasca alertele lui.
            if (carnet.getString(JETON_APP, null) != null) {
                uita(context)
            }

            return
        }

        val schimbat = jeton != carnet.getString(JETON_APP, null) ||
            societate != carnet.getString(SOCIETATE, null)

        if (!schimbat) {
            return
        }

        carnet.edit()
            .putString(JETON_APP, jeton)
            .putString(SOCIETATE, societate)
            .apply()

        anunta(context)
    }

    fun tineMinteJetonulFirebase(context: Context, jeton: String) {
        carnet(context).edit().putString(JETON_FCM, jeton).apply()

        anunta(context)
    }

    /**
     * Jetonul Firebase, cerut de la Google.
     *
     * Fara fisierul proiectului Firebase, pornirea lui esueaza — si atunci
     * aplicatia merge mai departe fara alerte instantanee, nu se opreste.
     */
    fun cereJetonulFirebase(context: Context) {
        if (!BuildConfig.ARE_FIREBASE) {
            return
        }

        try {
            FirebaseMessaging.getInstance().token
                .addOnSuccessListener { jeton -> tineMinteJetonulFirebase(context, jeton) }
                .addOnFailureListener { e -> Log.w(JURNAL, "Firebase n-a dat jetonul: " + e.message) }
        } catch (e: Throwable) {
            Log.w(JURNAL, "Firebase nu e pornit: " + e.message)
        }
    }

    /** Se anunta la server, dar numai daca e ceva nou de spus. */
    fun anunta(context: Context) {
        val carnet = carnet(context)

        val jetonApp = carnet.getString(JETON_APP, null) ?: return
        val jetonFcm = carnet.getString(JETON_FCM, null) ?: return
        val societate = carnet.getString(SOCIETATE, null)

        val acum = jetonApp.hashCode().toString() + "|" + jetonFcm + "|" + societate

        if (acum == carnet.getString(ANUNTAT, null)) {
            return
        }

        Thread {
            val corp = JSONObject()
            corp.put("token", jetonFcm)
            corp.put("platforma", "android")
            corp.put("model", Build.MANUFACTURER + " " + Build.MODEL)

            val raspuns = trimite("POST", "/api/dispozitive", corp, jetonApp, societate)

            if (raspuns in 200..299) {
                carnet.edit().putString(ANUNTAT, acum).apply()

                Log.i(JURNAL, "Telefonul s-a anuntat la server.")
            } else {
                Log.w(JURNAL, "Serverul n-a primit anuntul: " + raspuns)
            }
        }.start()
    }

    /** La deconectare: telefonul nu mai are ce cauta in lista de alertat. */
    fun uita(context: Context) {
        val carnet = carnet(context)

        val jetonApp = carnet.getString(JETON_APP, null)
        val jetonFcm = carnet.getString(JETON_FCM, null)

        /*
         * Se sterge intai din carnet, si abia pe urma se spune serverului: daca
         * stergerea de pe server nu izbuteste (telefonul e fara semnal, tocmai
         * cand omul se deconecteaza), macar aplicatia nu mai crede ca e anuntata
         * si va anunta din nou la urmatoarea conectare.
         */
        carnet.edit()
            .remove(JETON_APP)
            .remove(SOCIETATE)
            .remove(ANUNTAT)
            .apply()

        if (jetonApp == null || jetonFcm == null) {
            return
        }

        Thread {
            val corp = JSONObject()
            corp.put("token", jetonFcm)

            trimite("DELETE", "/api/dispozitive", corp, jetonApp, null)
        }.start()
    }

    private fun trimite(
        metoda: String,
        cale: String,
        corp: JSONObject,
        jetonApp: String,
        societate: String?
    ): Int {
        var legatura: HttpURLConnection? = null

        try {
            legatura = URL(BuildConfig.ADRESA + cale).openConnection() as HttpURLConnection

            legatura.requestMethod = metoda
            legatura.connectTimeout = 20_000
            legatura.readTimeout = 20_000
            legatura.doOutput = true

            legatura.setRequestProperty("Content-Type", "application/json")
            legatura.setRequestProperty("Accept", "application/json")
            legatura.setRequestProperty("Authorization", "Bearer " + jetonApp)

            // Antetul prin care aplicatia web spune la ce societate se uita omul.
            if (societate != null) {
                legatura.setRequestProperty("AuthorizationHeader", societate)
            }

            legatura.outputStream.use { it.write(corp.toString().toByteArray(Charsets.UTF_8)) }

            val cod = legatura.responseCode

            if (cod !in 200..299) {
                val plangerea = legatura.errorStream
                    ?.bufferedReader()
                    ?.use(BufferedReader::readText)
                    .orEmpty()
                    .take(300)

                Log.w(JURNAL, "Serverul a raspuns " + cod + ": " + plangerea)
            }

            return cod
        } catch (e: Exception) {
            Log.w(JURNAL, "Cererea n-a ajuns: " + e.message)

            return -1
        } finally {
            legatura?.disconnect()
        }
    }

    private fun carnet(context: Context) =
        context.getSharedPreferences(DOSAR, Context.MODE_PRIVATE)
}
