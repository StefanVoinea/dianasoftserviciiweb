<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AbonamentClient;
use App\Mail\AccesRespinsEmail;
use App\Models\User;
use App\Services\AccesIp;
use App\Services\Anaf\Jurnal;
use App\Support\ContextCompanie;
use App\Support\ContextUtilizator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;


class AuthController extends Controller
{
   
    public function user(Request $request){
        $user=User::where('id',$request->user()->id)->with(['companies','dianasoftmenuoptions','permissions','gestiuni'])->get()->first();

        /*
         * Interfata are nevoie sa stie ce poate arata: zona de administrare a
         * clientilor, fila de utilizatori din firma si modulele cumparate.
         * Fara ele ar afisa butoane care oricum ar fi respinse de server.
         */
        $user->super_admin = ContextUtilizator::esteSuperAdministrator();

        /*
         * Ruta asta nu trece prin filtrul de societate, deci clientul se ia din
         * antetul trimis de aplicatie si se fixeaza doar cat tine raspunsul.
         */
        $companie = $request->header('AuthorizationHeader') ?: ContextCompanie::curenta();

        ContextCompanie::pentru($companie, function () use ($user) {
            $user->administrator_client = ContextUtilizator::esteAdministratorClient();
            $user->poate_semna = ContextUtilizator::poateSemna();
            $user->poate_depune = ContextUtilizator::poateDepune();
            $user->abonament = $this->abonamentul(ContextCompanie::curenta());
        });

        return $user;
    }

    /**
     * Opreste autentificarea daca adresa nu e trecuta pe cont.
     *
     * Intoarce raspunsul de refuz sau null cand se poate merge mai departe.
     * Parola se verifica intai: un refuz dat inainte de ea ar spune oricui ca
     * un cont exista si ar trimite instiintari la fiecare incercare oarba.
     */
    protected function opresteDacaAdresaNuEPermisa(Request $request, string $email, string $password)
    {
        $user = User::where('email', $email)->first();

        if (!$user || !AccesIp::esteLimitat($user) || !Hash::check($password, $user->password)) {
            return null;
        }

        $ip = AccesIp::adresaCererii($request);

        if (AccesIp::arePermisiune($user, $ip)) {
            return null;
        }

        $this->anuntaAccesRespins($user, $ip, $request->userAgent());

        return response()->json([
            'error' => 'ip_nepermis',
            'message' => 'Autentificarea de la această adresă nu este permisă pentru contul dumneavoastră. '
                . 'Adresa folosită: ' . ($ip ?: 'necunoscută') . '.',
        ], 403);
    }

    /** Instiinteaza administratorul aplicatiei si scrie incercarea in jurnal. */
    protected function anuntaAccesRespins(User $user, ?string $ip, ?string $agent): void
    {
        Jurnal::esec(
            'autentificare_respinsa',
            'Încercare de autentificare a contului ' . $user->email . ' de la adresa nepermisă ' . ($ip ?: 'necunoscută'),
            ['ip' => $ip, 'permise' => $user->ip_permise]
        );

        $destinatar = config('app.super_admin');

        if (!$destinatar) {
            return;
        }

        try {
            Mail::to($destinatar)->send(
                new AccesRespinsEmail($user->email, $user->name, $ip, $user->ip_permise, $agent)
            );
        } catch (\Exception $e) {
            // Un email picat nu are voie sa lase autentificarea sa treaca.
            Log::error('Înștiințarea de acces respins nu a plecat: ' . $e->getMessage());
        }
    }

    /**
     * Ce are voie sa arate interfata: zona de administrare, fila de utilizatori
     * din firma si modulele cumparate. E un raspuns mic, cerut la deschiderea
     * paginilor, ca sa nu se lucreze cu date vechi din localStorage.
     */
    public function context(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => [
                'super_admin' => ContextUtilizator::esteSuperAdministrator(),
                'administrator_client' => ContextUtilizator::esteAdministratorClient(),
                'poate_semna' => ContextUtilizator::poateSemna(),
                'poate_depune' => ContextUtilizator::poateDepune(),
                'abonament' => $this->abonamentul(ContextCompanie::curenta()),
                /*
                 * Adresa pe care o vede serverul acum. E si unealta de verificare:
                 * daca aici apare adresa unui proxy in loc de a omului, filtrarea
                 * pe IP ar bloca pe toata lumea, iar asta se vede din prima.
                 */
                'ip_curent' => AccesIp::adresaCererii($request),
            ],
        ]);
    }

    /** Abonamentul clientului selectat acum, pe intelesul interfetei. */
    protected function abonamentul($companie): ?array
    {
        $abonament = AbonamentClient::alClientului($companie);

        if (!$abonament) {
            return null;
        }

        return [
            'activ' => $abonament->activ(),
            'in_proba' => $abonament->inProba(),
            'zile_ramase' => $abonament->zileRamase(),
            'motiv' => $abonament->motiv(),
            'tarif_lunar' => $abonament->tarif_lunar,
            'module' => [
                'spv' => $abonament->modul_spv,
                'etransport' => $abonament->modul_etransport,
                'portal_just' => $abonament->modul_portal_just,
            ],
        ];
    }
    public function login(Request $request)
    {
        try {
            $email = $request->email ?? $request->username;
            $password = $request->password;
            
            if (!$email || !$password) {
                return response()->json([
                    'error' => 'invalid_request',
                    'message' => 'Email and password are required'
                ], 400);
            }

            /*
             * Adresele permise se verifica inainte de a cere tokenul: altfel
             * contul ar primi un token valabil si abia apoi ar fi oprit.
             *
             * Se verifica doar daca parola e buna. Altfel, oricine ar putea afla
             * dupa mesajul de eroare ca un cont exista si are adrese trecute —
             * si ne-ar umple si casuta de email cu instiintari degeaba.
             */
            $raspunsIp = $this->opresteDacaAdresaNuEPermisa($request, $email, $password);

            if ($raspunsIp) {
                return $raspunsIp;
            }
            
            // Internal request to OAuth token endpoint
            $oauthRequest = Request::create('/oauth/token', 'POST', [
                'grant_type'    => 'password',
                'client_id'     => config('services.passport.client_id'),
                'client_secret' => config('services.passport.client_secret'),
                'username'      => $email,
                'password'      => $password,
                'scope'         => '',
            ]);
            
            $response = app()->handle($oauthRequest);
            return $response;
    	
    	} catch (\Exception $e) {
            Log::error('AuthController login error: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'trace' => $e->getTraceAsString()
            ]);
    		return response()->json([
                'error' => 'authentication_error',
                'message' => $e->getMessage()
            ], 500);
    	}
    
    // }  catch (\GuzzleHttp\Exception\BadResponseException $e) 
    //     {
    // 		if($e->getCode()==400) {
    // 			return response()->json('Invalid Request, Please enter a username or a password.',$e->getCode());
    // 		} else if ($e->getCode()==401) {
    // 			return response()->json('Your credentials are incorrect. Please try again',$e->getCode());
    // 		}
           
    // 		return response()->json('Something went wrong on the server.', $e->getCode() );
    // 	};
    
    }
    /**
     * Reinnoieste tokenul folosind refresh_token-ul.
     *
     * Aplicatiile care nu pot pastra datele clientului OAuth (cea mobila, de
     * exemplu) nu pot apela direct /oauth/token; aici ele sunt adaugate pe
     * server, ca la autentificare. Tokenul de acces expira in 30 de zile, cel
     * de reimprospatare in 90.
     */
    public function refresh(Request $request)
    {
        $refreshToken = $request->input('refresh_token');

        if (!$refreshToken) {
            return response()->json([
                'error' => 'invalid_request',
                'message' => 'refresh_token is required',
            ], 400);
        }

        try {
            $oauthRequest = Request::create('/oauth/token', 'POST', [
                'grant_type' => 'refresh_token',
                'refresh_token' => $refreshToken,
                'client_id' => config('services.passport.client_id'),
                'client_secret' => config('services.passport.client_secret'),
                'scope' => '',
            ]);

            return app()->handle($oauthRequest);
        } catch (\Exception $e) {
            Log::error('AuthController refresh error: ' . $e->getMessage());

            return response()->json([
                'error' => 'authentication_error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function register(Request $request)
    {
        $request->validate( [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8']

        ]);
         return User::create([
            'name' => $request->name,
            'email' => $request->email,
            'user_type'=>'user',
            'password' => Hash::make($request->password),
        ]);
    
    }
    public function logout()
    {
        auth()->user()->tokens->each(function($token,$key){
            $token->delete();
        });
        return response()->json('Logged out successfully',200);
    }
}
