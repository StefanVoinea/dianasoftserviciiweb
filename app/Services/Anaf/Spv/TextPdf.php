<?php

namespace App\Services\Anaf\Spv;

use Smalot\PdfParser\Parser;

/**
 * Scoate textul dintr-un PDF, aici pe server.
 *
 * Locul lui de drept e calculatorul clientului: programul local citeste
 * documentul acolo unde sta si trimite incoace doar textul. Aici se ajunge doar
 * cand documentul a trecut totusi prin server — la o instalare mai veche, care
 * nu stie sa citeasca singura, sau la documentele ramase de dinainte.
 */
class TextPdf
{
    /** Textul unui document primit in memorie, fara sa fie scris pe disc. */
    public static function dinContinut(string $octeti): string
    {
        try {
            return (new Parser())->parseContent($octeti)->getText();
        } catch (\Exception $e) {
            throw new SpvException('Nu s-a putut citi documentul: ' . $e->getMessage());
        }
    }

    /** Textul unui document aflat pe discul serverului. */
    public static function dinCale(string $cale): string
    {
        try {
            return (new Parser())->parseFile($cale)->getText();
        } catch (\Exception $e) {
            throw new SpvException('Nu s-a putut citi PDF-ul: ' . $e->getMessage());
        }
    }
}
