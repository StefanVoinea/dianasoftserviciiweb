<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * [2026-09-01] Genereaza / revoca token-ul de acces MCP pentru un utilizator.
 *
 * Token-ul e un Personal Access Token Passport, deci:
 *   - fiecare modificare e atribuita utilizatorului real si apare in `activities`
 *     cu numele lui;
 *   - se poate revoca oricand, fara sa afecteze pe altcineva;
 *   - da acces DOAR la ce permite `config/mcp.php`. Nu deschide restul API-ului
 *     mai mult decat il are deja userul respectiv.
 */
class TokenMcp extends Command
{
    protected $signature = 'mcp:token
                            {email : E-mailul utilizatorului}
                            {--revoca : Revoca token-urile MCP existente ale utilizatorului}
                            {--zile=180 : Cate zile e valabil token-ul nou}';

    protected $description = 'Genereaza (sau revoca) token-ul de acces MCP pentru un utilizator';

    public function handle()
    {
        $user = User::where("email", $this->argument("email"))->first();
        if (!$user) {
            $this->error("Nu exista niciun utilizator cu e-mailul " . $this->argument("email") . ".");
            return 1;
        }

        $numeToken = "MCP DianaSoft";

        if ($this->option("revoca")) {
            $n = DB::table("oauth_access_tokens")
                ->where("user_id", $user->id)->where("name", $numeToken)->where("revoked", 0)
                ->update(["revoked" => 1]);
            $this->info("Revocate: " . $n . " token-uri MCP pentru " . $user->name . ".");
            return 0;
        }

        // un singur token MCP activ per utilizator: cel vechi se revoca
        $vechi = DB::table("oauth_access_tokens")
            ->where("user_id", $user->id)->where("name", $numeToken)->where("revoked", 0)
            ->update(["revoked" => 1]);
        if ($vechi) {
            $this->line("  (am revocat " . $vechi . " token MCP anterior)");
        }

        $rezultat = $user->createToken($numeToken);
        $rezultat->token->expires_at = now()->addDays((int) $this->option("zile"));
        $rezultat->token->save();

        $this->info("Token MCP pentru " . $user->name . " <" . $user->email . ">");
        $this->line("valabil pana la " . $rezultat->token->expires_at->format("d.m.Y"));
        $this->newLine();
        $this->line($rezultat->accessToken);
        $this->newLine();
        $this->warn("Se arata O SINGURA DATA. Trimite-l pe un canal privat.");
        $this->line("Revocare: php artisan mcp:token " . $user->email . " --revoca");

        return 0;
    }
}
