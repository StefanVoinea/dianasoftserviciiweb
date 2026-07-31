<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Anaf\Jurnal;
use App\Services\Anaf\Oauth\OauthAnaf;
use App\Services\Anaf\Oauth\OauthException;
use Illuminate\Http\Request;

/**
 * Întoarcerea de la ANAF după autorizarea OAuth2.
 *
 * Ruta este publică pentru că ANAF redirecționează browserul aici, fără antetele
 * aplicației. Legătura cu utilizatorul și cu clientul se face prin parametrul
 * „state”, semnat la pornirea autorizării.
 */
class AnafOauthController extends Controller
{
    public function callback(Request $request, OauthAnaf $oauth)
    {
        // ANAF semnalează refuzul autorizării tot prin redirecționare.
        if ($request->filled('error')) {
            return $this->pagina(
                'Autorizare refuzată',
                $request->input('error_description') ?: $request->input('error'),
                false
            );
        }

        if (!$request->filled('code') || !$request->filled('state')) {
            return $this->pagina('Autorizare incompletă', 'Răspunsul de la ANAF nu conține codul de autorizare.', false);
        }

        try {
            $token = $oauth->preiaToken($request->input('code'), $request->input('state'));
        } catch (OauthException $e) {
            Jurnal::esec('etransport_autorizare', 'Autorizarea OAuth2 la ANAF a eșuat: ' . $e->getMessage());

            return $this->pagina('Autorizare eșuată', $e->getMessage(), false);
        }

        Jurnal::scrie(
            'etransport_autorizare',
            'A finalizat autorizarea OAuth2 la ANAF pentru ' . $token->cui,
            ['expira_la' => (string) $token->data_expirare],
            $token->cui
        );

        return $this->pagina(
            'Autorizare reușită',
            'Aplicația poate folosi serviciile ANAF pentru CIF-ul ' . $token->cui
                . '. Puteți închide această fereastră.',
            true
        );
    }

    /** Pagina se deschide într-o filă nouă, deci răspunde cu HTML, nu cu JSON. */
    protected function pagina(string $titlu, string $mesaj, bool $reusit)
    {
        $culoare = $reusit ? '#28a745' : '#dc3545';

        $html = '<!doctype html><html lang="ro"><head><meta charset="utf-8">'
            . '<title>' . e($titlu) . '</title>'
            . '<style>body{font-family:Segoe UI,Arial,sans-serif;background:#f8f8fb;margin:0;'
            . 'display:flex;align-items:center;justify-content:center;height:100vh}'
            . '.c{background:#fff;border:1px solid #e6e6ee;border-radius:8px;padding:28px 32px;max-width:520px}'
            . 'h1{font-size:19px;margin:0 0 10px;color:' . $culoare . '}'
            . 'p{margin:0;color:#5b5b6b;line-height:1.5}</style></head><body>'
            . '<div class="c"><h1>' . e($titlu) . '</h1><p>' . e($mesaj) . '</p></div></body></html>';

        return response($html, $reusit ? 200 : 400)->header('Content-Type', 'text/html; charset=utf-8');
    }
}
