<?php

namespace App\Services\Anaf\Declaratii;

/**
 * Repara XML-urile de declaratie stricate de programul care le-a scris.
 *
 * Programele de contabilitate mai vechi pun in atribute textul asa cum l-a
 * scris omul: un „&" in denumirea firmei ori in adresa („Str Bradului & Nr.13")
 * face fisierul de necitit, iar DUKIntegrator il respinge sec cu „Fisierul nu
 * este un XML valid", fara sa spuna unde e buba. La fel pateste un fisier cu
 * diacritice scrise in codificarea veche Windows desi declara UTF-8, ori unul
 * cu caractere de control ramase de la vreun export.
 *
 * De aceea, inainte de analiza si de validare, continutul trece pe aici:
 *  - un fisier care se citeste ramane neatins, pana la ultimul octet;
 *  - unul de necitit e curatat (codificare, caractere de control, „&" si „<"
 *    neescapate) si pastrat numai daca dupa curatare chiar se citeste —
 *    altfel se lasa cum a venit, ca eroarea aratata omului sa fie cea adevarata.
 *
 * Escaparea nu schimba datele declarate: „&amp;" inseamna tot „&", doar scris
 * cum cere XML-ul.
 */
class CurataXml
{
    public function curata(string $continut): string
    {
        if ($continut === '' || $this->seCiteste($continut)) {
            return $continut;
        }

        $curatat = $this->inCodificareaDeclarata($continut);
        $curatat = $this->faraCaractereDeControl($curatat);
        $curatat = $this->inAfaraCdata($curatat, function (string $bucata) {
            return $this->escapatInAtribute($this->escapatAmpersand($bucata));
        });

        return $this->seCiteste($curatat) ? $curatat : $continut;
    }

    protected function seCiteste(string $continut): bool
    {
        $anterior = libxml_use_internal_errors(true);
        $xml = simplexml_load_string($continut);
        libxml_clear_errors();
        libxml_use_internal_errors($anterior);

        return $xml !== false;
    }

    /**
     * Diacritice scrise in alta codificare decat cea declarata.
     *
     * Fisierul spune (sau lasa sa se inteleaga) ca e UTF-8, dar octetii nu sunt
     * UTF-8: programul care l-a scris a pus diacriticele in codificarea veche
     * Windows (ș = un singur octet, nu doi). Se aduc la UTF-8 din Windows-1250,
     * codificarea folosita de programele romanesti de pe Windows.
     *
     * Un fisier care isi declara cinstit alta codificare se lasa in pace:
     * parserul stie sa o citeasca singur, deci nu de la ea a picat.
     */
    protected function inCodificareaDeclarata(string $continut): string
    {
        if (preg_match('/^<\?xml[^>]*encoding\s*=\s*["\']([^"\']+)["\']/i', $continut, $m)
            && strtoupper(trim($m[1])) !== 'UTF-8') {
            return $continut;
        }

        if (mb_check_encoding($continut, 'UTF-8')) {
            return $continut;
        }

        $convertit = @iconv('CP1250', 'UTF-8//IGNORE', $continut);

        if ($convertit !== false) {
            return $convertit;
        }

        // Fara CP1250 in iconv se merge pe ISO-8859-2: literele romanesti
        // stau pe aceleasi pozitii in amandoua.
        return mb_convert_encoding($continut, 'UTF-8', 'ISO-8859-2');
    }

    /**
     * Caracterele de control interzise in XML 1.0 (in afara de tab si de
     * capetele de rand). Se lucreaza pe octeti: acesti octeti nu apar niciodata
     * in mijlocul unui caracter UTF-8, deci diacriticele nu au de suferit.
     */
    protected function faraCaractereDeControl(string $continut): string
    {
        return preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', $continut);
    }

    /** „&" care nu incepe o entitate scrisa intreaga devine „&amp;". */
    protected function escapatAmpersand(string $continut): string
    {
        return preg_replace(
            '/&(?!(?:amp|lt|gt|quot|apos|#[0-9]+|#x[0-9A-Fa-f]+);)/',
            '&amp;',
            $continut
        );
    }

    /**
     * „<" dintr-o valoare de atribut („den="A < B"") devine „&lt;".
     *
     * Se cauta numai perechile atribut="valoare"; un „<" din textul dintre
     * elemente nu se poate deosebi de inceputul unui element, asa ca acela se
     * lasa parserului. Daca inlocuirea ar strica ceva, poarta finala din
     * curata() arunca oricum rezultatul.
     */
    protected function escapatInAtribute(string $continut): string
    {
        return preg_replace_callback(
            '/=\s*("([^"]*)"|\'([^\']*)\')/',
            function (array $m) {
                $ghilimea = $m[1][0];
                $valoare = $ghilimea === '"' ? $m[2] : $m[3];

                return '=' . $ghilimea . str_replace('<', '&lt;', $valoare) . $ghilimea;
            },
            $continut
        );
    }

    /**
     * Aplica o inlocuire peste tot in afara sectiunilor CDATA.
     *
     * Intr-un CDATA, „&" si „<" sunt litere ca oricare altele: escaparea lor
     * acolo ar schimba chiar datele declarate.
     */
    protected function inAfaraCdata(string $continut, callable $inlocuire): string
    {
        $bucati = preg_split('/(<!\[CDATA\[.*?\]\]>)/s', $continut, -1, PREG_SPLIT_DELIM_CAPTURE);

        foreach ($bucati as $i => $bucata) {
            if (strpos($bucata, '<![CDATA[') !== 0) {
                $bucati[$i] = $inlocuire($bucata);
            }
        }

        return implode('', $bucati);
    }
}
