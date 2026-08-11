package ro.dianasoft.grefier

import android.app.NotificationChannel
import android.app.NotificationManager
import android.app.PendingIntent
import android.content.Context
import android.content.Intent
import androidx.core.app.NotificationManagerCompat
import androidx.core.app.NotificationCompat

/**
 * Alertele asa cum le vede omul pe ecranul telefonului.
 *
 * Canalul se face la fiecare pornire: e fara pagube daca exista deja, iar asa
 * nu se poate intampla sa vina o alerta inainte ca el sa fi fost pregatit — caz
 * in care Android o arunca fara sa spuna nimic.
 */
object Notificari {

    const val CANAL = "modificari_dosare"

    fun pregatesteCanalul(context: Context) {
        val canal = NotificationChannel(
            CANAL,
            context.getString(R.string.canal_nume),
            NotificationManager.IMPORTANCE_HIGH
        )

        canal.description = context.getString(R.string.canal_lamurire)
        canal.enableVibration(true)

        val paznic = context.getSystemService(Context.NOTIFICATION_SERVICE) as NotificationManager

        paznic.createNotificationChannel(canal)
    }

    /**
     * @param tinta locul din aplicatie care se deschide la apasare
     */
    fun arata(context: Context, titlu: String, corp: String, tinta: String?, numar: Int) {
        val deschide = Intent(context, Fereastra::class.java)
        deschide.flags = Intent.FLAG_ACTIVITY_NEW_TASK or Intent.FLAG_ACTIVITY_CLEAR_TOP
        deschide.putExtra(Fereastra.TINTA, tinta)

        val apasarea = PendingIntent.getActivity(
            context,
            numar,
            deschide,
            PendingIntent.FLAG_UPDATE_CURRENT or PendingIntent.FLAG_IMMUTABLE
        )

        val alerta = NotificationCompat.Builder(context, CANAL)
            .setSmallIcon(R.drawable.ic_alerta)
            .setColor(context.getColor(R.color.accent))
            .setContentTitle(titlu)
            .setContentText(corp)
            // Textul poate fi lung: asa se vede intreg cand omul trage de alerta.
            .setStyle(NotificationCompat.BigTextStyle().bigText(corp))
            .setPriority(NotificationCompat.PRIORITY_HIGH)
            .setAutoCancel(true)
            .setContentIntent(apasarea)
            .build()

        try {
            NotificationManagerCompat.from(context).notify(numar, alerta)
        } catch (e: SecurityException) {
            // Omul n-a dat voie la notificari. Nu e nimic de facut, si nici de cazut.
        }
    }
}
