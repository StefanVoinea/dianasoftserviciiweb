<?php

namespace App\Imports;

use App\Models\MarketingContact;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

/**
 * Lista firmelor de contabilitate, adusa dintr-un fisier Excel.
 *
 * Coloanele sunt cele din listele CECCAR: judet, denumire, CUI, telefoane,
 * emailuri, viza, membru din, tip. Numele lor se citesc cu diacritice si fara,
 * fiindca acelasi export vine cand asa, cand altfel.
 *
 * Randurile fara email se lasa deoparte: lista aceasta e pentru scris, iar un
 * rand fara adresa n-are ce cauta in ea. Cate au fost, se spune la sfarsit.
 *
 * Firmele care exista deja isi pastreaza starea de abonare. Cine s-a dezabonat
 * o data ramane dezabonat, oricat de des s-ar reincarca lista — altfel un import
 * nou ar sterge o hotarare a omului, si asta nu se face.
 */
class FirmeContabilitateImport implements ToCollection, WithHeadingRow
{
    /** Din ce fisier a venit lista; se scrie pe fiecare contact. */
    protected $sursa;

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

    public function collection(Collection $randuri)
    {
        foreach ($randuri as $rand) {
            $this->unRand($rand->toArray());
        }
    }

    protected function unRand(array $rand): void
    {
        $email = $this->curata($this->camp($rand, ['email_principal', 'email']));

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

        $denumire = $this->camp($rand, ['denumire_firma', 'denumire', 'firma']);

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
            'tip' => $this->camp($rand, ['tip']),
            'viza' => $this->camp($rand, ['viza_ceccar', 'viza']),
            'membru_din' => $this->camp($rand, ['membru_ceccar_din', 'membru_din']),
            'sursa' => $this->sursa,
        ];

        $contact = MarketingContact::where('email', $email)->first();

        if ($contact) {
            /*
             * Starea de abonare nu se atinge la reincarcare: ea e hotararea
             * omului, nu a fisierului.
             */
            $contact->update($date);
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
