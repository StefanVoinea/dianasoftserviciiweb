<?php

namespace App\Imports;

use App\Models\MarketingContact;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Events\BeforeSheet;
use Illuminate\Support\Collection;

/**
 * Lista de contacte, adusa dintr-un fisier Excel.
 *
 * Doua feluri de liste intra pe aici, si se recunosc dupa coloane:
 *
 * Listele de firme au „Denumire firma", „CUI", „Email principal". Listele de
 * experti contabili au „Nume" in loc de denumire, n-au CUI deloc, iar adresa
 * sta intr-o coloana numita „Email (probabil)" — ea nu e scrisa de om, ci
 * dedusa, si fisierul spune in „Sursa email" cum anume. Deductia se pastreaza
 * langa contact: cine trimite trebuie sa stie dinainte ca adresa e ghicita, nu
 * dupa ce se intorc scrisorile.
 *
 * Numele coloanelor se citesc cu diacritice si fara, fiindca acelasi export
 * vine cand asa, cand altfel.
 *
 * Randurile fara email se lasa deoparte: lista aceasta e pentru scris, iar un
 * rand fara adresa n-are ce cauta in ea. Cate au fost, se spune la sfarsit.
 *
 * Contactele care exista deja isi pastreaza starea de abonare. Cine s-a
 * dezabonat o data ramane dezabonat, oricat de des s-ar reincarca lista —
 * altfel un import nou ar sterge o hotarare a omului, si asta nu se face.
 *
 * Tot asa, un import nu goleste ce stia dinainte: o lista fara coloana CUI nu
 * are de ce sa stearga CUI-urile scrise de alta. Se scrie doar ce aduce.
 */
class FirmeContabilitateImport implements ToCollection, WithHeadingRow, WithEvents
{
    /** Din ce fisier a venit lista; se scrie pe fiecare contact. */
    protected $sursa;

    /**
     * Foaia din care se citeste acum.
     *
     * Un fisier are mai multe: expertii contabili pe una, consultantii fiscali
     * pe alta, sumarele si notele pe altele. Foile fara adrese se golesc singure
     * — niciun rand al lor n-are email —, iar numele foii ramane langa contact,
     * ca sa se stie de unde vine fiecare.
     *
     * @var string
     */
    protected $foaia = '';

    public $adaugate = 0;
    public $innoite = 0;
    public $fara_email = 0;
    public $repetate = 0;

    /** Adresele intalnite in chiar fisierul acesta, ca sa nu se scrie de doua ori. */
    protected $vazute = [];

    public function __construct(string $sursa)
    {
        $this->sursa = $sursa;
    }

    /** @return array<string, callable> */
    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function (BeforeSheet $eveniment) {
                $this->foaia = trim((string) $eveniment->getSheet()->getTitle());
            },
        ];
    }

    /** Fisierul si foaia, scrise impreuna pe contact. */
    protected function deUnde(): string
    {
        return $this->foaia !== '' ? $this->sursa . ' — ' . $this->foaia : $this->sursa;
    }

    public function collection(Collection $randuri)
    {
        foreach ($randuri as $rand) {
            $this->unRand($rand->toArray());
        }
    }

    protected function unRand(array $rand): void
    {
        /*
         * Adresa scrisa in sursa bate adresa dedusa.
         *
         * Se cauta pe nume intreg, fara cautarea dupa inceput de nume: aceea ar
         * lua „Email (probabil)" drept „Email" si n-am mai sti care din doua e.
         */
        $declarata = $this->campExact($rand, ['email_principal', 'email']);
        $probabila = $this->campExact($rand, ['email_probabil']);

        $email = $this->curata($declarata ?: $probabila ?: $this->camp($rand, ['email']));

        if ($email === null || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->fara_email++;

            return;
        }

        $email = mb_strtolower($email);

        if (isset($this->vazute[$email])) {
            $this->repetate++;

            return;
        }

        $this->vazute[$email] = true;

        // „Nume" e ultimul cautat: in listele de firme denumirea are numele ei.
        $denumire = $this->camp($rand, ['denumire_firma', 'denumire', 'firma', 'nume']);

        if ($denumire === null) {
            $this->fara_email++;

            return;
        }

        $date = [
            'denumire' => $denumire,
            'cui' => $this->camp($rand, ['cui']),
            'emailuri' => $this->camp($rand, ['toate_emailurile']),
            'telefon' => $this->camp($rand, ['telefon_principal', 'telefon']),
            'judet' => $this->camp($rand, ['judet']),
            'tip' => $this->camp($rand, ['tip', 'categorie_registru', 'categorie']),
            'viza' => $this->camp($rand, ['viza_ceccar', 'viza']),
            'membru_din' => $this->camp($rand, ['membru_ceccar_din', 'membru_din']),
            'sursa' => $this->deUnde(),
        ];

        /*
         * Cum am aflat adresa: goala cand e scrisa in sursa, plina cand fisierul
         * spune ca a dedus-o — dintr-un telefon comun cu o firma, dintr-un nume
         * potrivit in alt registru. Se scrie si goala, dinadins: o adresa care
         * era ghicita si acum vine declarata nu mai are de ce sa poarte semnul.
         */
        $date['email_dedus'] = $declarata !== null ? null : $this->camp($rand, ['sursa_email']);

        $contact = MarketingContact::where('email', $email)->first();

        if ($contact) {
            /*
             * Starea de abonare nu se atinge la reincarcare: ea e hotararea
             * omului, nu a fisierului.
             *
             * Si nici campurile goale nu se scriu peste cele stiute: lista
             * expertilor n-are coloana CUI, iar scrisa peste o firma adusa din
             * lista de firme i-ar sterge CUI-ul degeaba. Face exceptie tocmai
             * felul in care s-a aflat adresa, care trebuie sa poata fi si sters.
             */
            $deScris = array_filter($date, function ($valoare) {
                return $valoare !== null && $valoare !== '';
            });

            $deScris['email_dedus'] = $date['email_dedus'];

            $contact->update($deScris);
            $this->innoite++;

            return;
        }

        MarketingContact::create($date + ['email' => $email]);
        $this->adaugate++;
    }

    /**
     * Prima coloana gasita dintre numele date.
     *
     * Antetele vin cand cu diacritice, cand fara, iar Maatwebsite le aduce deja
     * cu litere mici si cu liniute de subliniere in loc de spatii. Se cauta deci
     * mai multe scrieri ale aceluiasi lucru.
     */
    protected function camp(array $rand, array $nume): ?string
    {
        foreach ($nume as $cheie) {
            foreach ([$cheie, str_replace(['a', 'i', 's', 't'], ['ă', 'î', 'ș', 'ț'], $cheie)] as $incercare) {
                if (isset($rand[$incercare]) && $this->curata($rand[$incercare]) !== null) {
                    return $this->curata($rand[$incercare]);
                }
            }
        }

        // Ultima incercare: se cauta dupa inceputul numelui, oricum ar fi scris.
        foreach ($rand as $coloana => $valoare) {
            $simplu = $this->faraDiacritice((string) $coloana);

            foreach ($nume as $cheie) {
                if (strpos($simplu, $this->faraDiacritice($cheie)) === 0) {
                    return $this->curata($valoare);
                }
            }
        }

        return null;
    }

    /**
     * Coloana cautata numai pe numele ei intreg.
     *
     * Deosebirea fata de camp(): aceea mai incearca si dupa inceputul numelui,
     * ceea ce e bun cand nu stii cum a fost scris antetul, dar rau cand doua
     * coloane incep la fel — „Email" si „Email (probabil)" — si tocmai
     * deosebirea dintre ele conteaza.
     */
    protected function campExact(array $rand, array $nume): ?string
    {
        foreach ($nume as $cheie) {
            foreach ([$cheie, str_replace(['a', 'i', 's', 't'], ['ă', 'î', 'ș', 'ț'], $cheie)] as $incercare) {
                if (isset($rand[$incercare]) && $this->curata($rand[$incercare]) !== null) {
                    return $this->curata($rand[$incercare]);
                }
            }
        }

        return null;
    }

    protected function faraDiacritice(string $text): string
    {
        return str_replace(
            ['ă', 'â', 'î', 'ș', 'ț', 'Ă', 'Â', 'Î', 'Ș', 'Ț'],
            ['a', 'a', 'i', 's', 't', 'a', 'a', 'i', 's', 't'],
            mb_strtolower($text)
        );
    }

    protected function curata($valoare): ?string
    {
        if ($valoare === null) {
            return null;
        }

        $text = trim((string) $valoare);

        return $text === '' ? null : $text;
    }
}
