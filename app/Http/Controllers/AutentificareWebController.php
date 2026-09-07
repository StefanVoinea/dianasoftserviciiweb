<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * [2026-09-01] Autentificare in BROWSER, cu sesiune.
 *
 * Aplicatia se foloseste prin SPA, care se autentifica prin API si primeste un
 * token — nu exista sesiune web nicaieri. Pasul de autorizare OAuth insa se
 * petrece obligatoriu in browser: `/oauth/authorize` are middleware `web,auth`,
 * deci cere sesiune. Fara pagina asta, conectorul remote se opreste acolo.
 *
 * Rostul ei e strict acesta. Nu e o a doua cale de intrare in aplicatie: dupa
 * autentificare duce la adresa ceruta initial (ecranul de consimtamant OAuth),
 * iar cand cineva ajunge aici fara motiv, il trimite in aplicatie.
 *
 * Precautii, fiind o suprafata de autentificare noua pe un ERP de productie:
 *   - 5 incercari pe minut, numarate pe (e-mail + IP), cu mesaj de asteptare;
 *   - utilizatorii marcati `blocat = Da` nu intra, chiar cu parola buna;
 *   - sesiunea se regenereaza la intrare (impotriva fixarii de sesiune);
 *   - nu exista inregistrare si nu exista resetare de parola pe aici.
 */
class AutentificareWebController extends Controller
{
    const INCERCARI = 5;
    const SECUNDE   = 60;

    public function formular(Request $request)
    {
        if (Auth::check()) {
            return redirect()->intended("/");
        }
        return view("autentificare", [
            "motiv" => $request->session()->get("url.intended")
                && Str::contains($request->session()->get("url.intended"), "oauth/authorize"),
        ]);
    }

    public function intra(Request $request)
    {
        $date = $request->validate([
            "email"    => ["required", "string"],
            "password" => ["required", "string"],
        ], [], ["email" => "e-mailul", "password" => "parola"]);

        $cheie = Str::lower($date["email"]) . "|" . $request->ip();

        if (RateLimiter::tooManyAttempts($cheie, self::INCERCARI)) {
            $secunde = RateLimiter::availableIn($cheie);
            throw ValidationException::withMessages([
                "email" => "Prea multe incercari. Mai asteptati " . $secunde . " secunde.",
            ]);
        }

        if (!Auth::attempt(["email" => $date["email"], "password" => $date["password"]], $request->boolean("tine_minte"))) {
            RateLimiter::hit($cheie, self::SECUNDE);
            throw ValidationException::withMessages(["email" => "E-mail sau parola gresite."]);
        }

        // utilizator blocat: se iese imediat, chiar daca parola era buna
        if (Str::lower((string) Auth::user()->blocat) === "da") {
            Auth::logout();
            $request->session()->invalidate();
            RateLimiter::hit($cheie, self::SECUNDE);
            throw ValidationException::withMessages(["email" => "Contul este blocat. Adresati-va administratorului."]);
        }

        RateLimiter::clear($cheie);
        $request->session()->regenerate();

        return redirect()->intended("/");
    }

    public function iesi(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect("/");
    }
}
