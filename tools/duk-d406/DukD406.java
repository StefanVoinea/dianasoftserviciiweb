/*
 * Validarea D406 (SAF-T) cu perioada de raportare, plus PDF-ul oficial.
 *
 * De ce există: DUKIntegrator nu are pe unde primi perioada din linia de
 * comandă, iar fără ea validatorul ANAF nu alege versiunea de nomenclatoare
 * potrivită. Pe o declarație pe iunie 2026 asta înseamnă peste o mie de erori
 * inventate („valoarea '310344' nu se află în listă"), pentru că se compară cu
 * lista din 2019. Fereastra DUKIntegrator nu cere nici ea perioada — ea o are
 * din contextul în care e apelată.
 *
 * Aici se cheamă direct validatorul ANAF, cu (an, lună, tip perioadă), și apoi
 * generatorul de PDF, cu informațiile scoase de validare. Ambele clase sunt
 * publice în jar-urile ANAF; nu se ocolește nimic și nu se modifică nimic.
 *
 * Apel:
 *   java -jar DukD406.jar <intrare.xml> <erori.txt> <iesire.pdf> <an> <luna> <L|T|A> [atasamente.zip]
 *
 * Ieșire: 0 = declarație validă (în fișierul de erori se scrie „ok", ca la
 * DUKIntegrator), 1 = erori de validare, 2 = apel greșit sau eșec de prelucrare.
 *
 * Se compilează cu jar-urile ANAF la îndemână:
 *   javac --release 8 -cp "DUKIntegrator.jar;lib/*" DukD406.java
 *   jar cfm DukD406.jar manifest.txt DukD406.class
 */
import java.io.File;
import java.io.FileOutputStream;
import java.io.OutputStreamWriter;
import java.io.Writer;

public class DukD406 {

    public static void main(String[] args) {
        if (args.length < 6) {
            System.err.println("Apel: DukD406 <xml> <erori> <pdf> <an> <luna> <L|T|A> [zip]");
            System.exit(2);
        }

        String xml = args[0];
        String erori = args[1];
        String pdf = args[2];
        String tipPerioada = args[5];
        String zip = args.length > 6 ? args[6] : "";

        int an;
        int luna;

        try {
            an = Integer.parseInt(args[3]);
            luna = Integer.parseInt(args[4]);
        } catch (NumberFormatException e) {
            System.err.println("Anul si luna trebuie sa fie numere: " + args[3] + ", " + args[4]);
            System.exit(2);
            return;
        }

        /*
         * Raportarea anuala nu are luna. Validatorul cere totusi un numar intre
         * 1 si 12, altfel raspunde "perioada de raportare eronata", asa ca
         * pentru "A" se trimite decembrie — ultima luna a perioadei raportate.
         */
        if (luna < 1 || luna > 12) {
            luna = 12;
        }

        try {
            validator.Validator vld = new validator.Validator();
            int rezultat = vld.parseDocument("D406", xml, erori, an, luna, tipPerioada);

            if (rezultat != 0) {
                System.out.println("Validare cu erori. Cod: " + rezultat);
                System.exit(1);
            }

            /*
             * DUKIntegrator scrie "ok" in fisierul de erori cand declaratia e
             * valida. Se pastreaza obiceiul, ca partea de PHP sa citeasca la fel
             * rezultatul, indiferent pe ce drum a trecut declaratia.
             */
            scrie(erori, "ok");

            /*
             * Generatorul de PDF isi alege versiunea dupa anul si luna din
             * informatiile validarii, iar validatorul SAF-T nu le completeaza:
             * fara ele raspunde "clasa info nu contine an/luna valide" si nu
             * scrie nimic. Se pun cele cu care tocmai s-a validat, fara sa se
             * atinga ce a completat el.
             */
            dec.Info info = vld.getInfo();

            if (!esteNumar(info._an)) {
                info._an = String.valueOf(an);
            }

            if (!esteNumar(info._luna)) {
                info._luna = String.valueOf(luna);
            }

            if (info._tipPerioada == null || info._tipPerioada.trim().length() == 0) {
                info._tipPerioada = tipPerioada;
            }

            /*
             * Ordinea ceruta de ANAF este (PDF de scris, XML de atasat, ZIP de
             * atasat) — nu cea din numele metodei. Inversata, generatorul scrie
             * PDF-ul peste XML-ul primit si il pierde.
             */
            if (new File(pdf).getAbsolutePath().equals(new File(xml).getAbsolutePath())) {
                System.err.println("PDF-ul si XML-ul nu pot fi acelasi fisier: " + pdf);
                System.exit(2);
            }

            String raspuns = new pdf.PdfSuperCreator().createPdf("D406", info, pdf, xml, zip);

            if (!new File(pdf).isFile()) {
                System.err.println("PDF-ul nu a fost generat: "
                    + (raspuns == null ? "motiv necunoscut" : raspuns)
                    + " (an=" + info._an + ", luna=" + info._luna + ", cif=" + info._cif + ")");
                System.exit(2);
            }

            System.out.println("ok");
            System.exit(0);
        } catch (Throwable e) {
            System.err.println("Prelucrarea a esuat: " + e);
            System.exit(2);
        }
    }

    /** Un an sau o luna scrisa de validator, buna de folosit ca atare. */
    private static boolean esteNumar(String valoare) {
        if (valoare == null) {
            return false;
        }

        try {
            Integer.parseInt(valoare.trim());

            return true;
        } catch (NumberFormatException e) {
            return false;
        }
    }

    private static void scrie(String cale, String continut) {
        Writer scriitor = null;

        try {
            scriitor = new OutputStreamWriter(new FileOutputStream(cale), "UTF-8");
            scriitor.write(continut);
        } catch (Exception e) {
            System.err.println("Fisierul de erori nu a putut fi scris: " + e);
        } finally {
            if (scriitor != null) {
                try {
                    scriitor.close();
                } catch (Exception e) {
                    // inchiderea esuata nu schimba rezultatul validarii
                }
            }
        }
    }
}
