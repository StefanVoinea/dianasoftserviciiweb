package ro.dianasoft.grefier

import com.google.firebase.messaging.FirebaseMessagingService
import com.google.firebase.messaging.RemoteMessage

/**
 * Ce se intampla cand Google trezeste telefonul.
 *
 * Serverul trimite si un titlu, si date. Cand aplicatia e inchisa sau lasata in
 * urma, Android arata singur alerta din titlu, iar aici nu se ajunge; cand omul
 * are aplicatia in fata, alerta trebuie aratata de noi. De aceea se scrie
 * amandoua drumurile: fara al doilea, o modificare venita in timp ce omul se
 * uita in aplicatie ar trece nevazuta.
 */
class ServiciuMesaje : FirebaseMessagingService() {

    /**
     * Jetonul telefonului s-a schimbat — la reinstalare, la stergerea datelor,
     * sau pentru ca asa a hotarat Google. Fara acest anunt, alertele ar continua
     * sa plece catre o adresa care nu mai exista.
     */
    override fun onNewToken(jeton: String) {
        Dispozitiv.tineMinteJetonulFirebase(this, jeton)
    }

    override fun onMessageReceived(mesaj: RemoteMessage) {
        val titlu = mesaj.notification?.title
            ?: mesaj.data["titlu"]
            ?: getString(R.string.app_name)

        val corp = mesaj.notification?.body
            ?: mesaj.data["corp"]
            ?: getString(R.string.modificare_fara_text)

        /*
         * Fiecare dosar isi are alerta lui: daca toate ar purta acelasi numar,
         * a doua ar sterge-o pe prima, si omul cu trei dosare schimbate ar afla
         * doar de unul.
         */
        val numar = mesaj.data["dosar"]?.hashCode()
            ?: mesaj.data["modificare_id"]?.hashCode()
            ?: 1

        Notificari.arata(this, titlu, corp, undeDuce(mesaj), numar)
    }

    /** Locul din aplicatie care se deschide la apasarea alertei. */
    private fun undeDuce(mesaj: RemoteMessage): String =
        when (mesaj.data["tip"]) {
            "modificari_dosare" -> "/portal-just"
            else -> ""
        }
}
