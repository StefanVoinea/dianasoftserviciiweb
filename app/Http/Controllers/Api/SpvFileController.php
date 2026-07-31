<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SpvMesaj;
use App\Services\Anaf\Arhiva\ArhivaException;
use App\Services\Anaf\Arhiva\ArhivaService;
use App\Services\Anaf\Jurnal;
use App\Services\Anaf\Spv\SpvClient;
use App\Services\Anaf\Spv\SpvException;
use App\Services\Anaf\Spv\SpvStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SpvFileController extends Controller
{
    /**
     * Deschide fisierul deja descarcat. Mesajele SPV stau pe discul privat
     * (storage/app), deci nu sunt accesibile printr-un link direct.
     */
    public function open(Request $request, ArhivaService $arhiva)
    {
        $mesaj = SpvMesaj::where('mesaj_id', $request->query('id'))->first();

        if (!$mesaj) {
            return response()->json(['success' => false, 'message' => 'Fișierul nu a fost găsit.'], 404);
        }

        $peServer = $mesaj->cale_fisier && Storage::exists($mesaj->cale_fisier);

        if (!$peServer && !$mesaj->arhiva_cale) {
            return response()->json(['success' => false, 'message' => 'Fișierul nu a fost găsit.'], 404);
        }

        // Documentul poate sta doar in arhiva de pe calculatorul clientului.
        try {
            $continut = $peServer
                ? Storage::get($mesaj->cale_fisier)
                : $arhiva->ia($mesaj->arhiva_cale);
        } catch (ArhivaException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 502);
        }

        Jurnal::scrie(
            'mesaj_deschidere',
            'A deschis fișierul mesajului ' . $mesaj->tip . ' (' . $mesaj->mesaj_id . ')',
            ['mesaj_id' => $mesaj->mesaj_id],
            $mesaj->cif
        );

        return response($continut, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $mesaj->tip . '_' . $mesaj->mesaj_id . '.pdf"',
        ]);
    }

    public function download(Request $request, SpvClient $spvClient, SpvStorage $storage)
    {
        try {
            $id = $request->query('id');
            $message = SpvMesaj::where('mesaj_id', $id)->first();

            if (!$message) {
                return response()->json(['success' => false, 'message' => 'Mesajul nu a fost găsit'], 404);
            }

            $fisier = $spvClient->descarcare($id);
            $saved = $storage->saveFile($fisier, 'downloads');

            $message->update([
                'cale_fisier' => $saved['path'],
                'hash_fisier' => $saved['hash'],
                'descarcat_la' => now(),
            ]);

            // Documentul ramane la client, in dosarul firmei lui.
            $storage->arhiveazaMesaj($message, $fisier);

            Jurnal::scrie(
                'mesaj_descarcare',
                'A descărcat fișierul mesajului ' . $message->tip . ' (' . $id . ')',
                ['mesaj_id' => $id, 'hash' => $saved['hash']],
                $message->cif
            );

            return response()->json([
                'success' => true,
                'path' => $saved['path'],
                'hash' => $saved['hash'],
            ]);
        } catch (SpvException $e) {
            Jurnal::esec('mesaj_descarcare', 'Descărcarea mesajului ' . $id . ' a eșuat: ' . $e->getMessage());

            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
