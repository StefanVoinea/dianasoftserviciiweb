<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
foreach (File::allFiles(__DIR__ . '/api_routes') as $route_file) {
  require $route_file->getPathname();
}


/*
 * Autentificarea nu mai trece prin lista globala „ipautorizat".
 *
 * Aceea era o lista de adrese ale unui singur birou: cine intra de altundeva
 * era oprit inainte de a-si scrie parola, oricine ar fi fost — inclusiv
 * administratorul aplicatiei. Intr-un serviciu cu clienti in toata tara, ea
 * inchidea afara pe toata lumea in afara catorva adrese.
 *
 * Adresele permise se tin acum pe fiecare cont in parte („IP permise", gol
 * inseamna de oriunde) si se verifica si la autentificare, si la fiecare cerere
 * de mai departe — vezi App\Services\AccesIp.
 */
Route::post('/login','Api\AuthController@login')->name('login')->middleware(['throttle:60,1']);
Route::post('/registerAPI','Api\AuthController@register');
// Reinnoirea tokenului pentru aplicatiile care nu pot pastra datele clientului OAuth (cea mobila)
Route::post('/refresh','Api\AuthController@refresh')->middleware('throttle:60,1');
Route::get("/efacturaparams", "Api\EfacturaparamsController@index"); 
Route::post("/gettoken", "Api\EfacturatokensController@gettoken");
Route::get("/callback", "Api\EfacturatokensController@callback");
/*
 * Întoarcerea de la ANAF după autorizarea OAuth2: rută publică, pentru că ANAF
 * redirecționează browserul aici. Cererea este legitimată prin „state”, semnat.
 */
Route::get('/anaf-oauth/callback', 'Api\AnafOauthController@callback');

/*
 * Puntea către programele locale aflate în spatele unui router.
 *
 * Nu cer „auth:api": cererile nu vin de la un om cu sesiune, ci de la serverul
 * însuși (cu jeton semnat) și de la agentul clientului (cu codul lui de
 * instalare). Fiecare rută își verifică singură cine bate la ușă.
 */
Route::post('/punte/agent/inrolare', 'Api\PunteController@inrolare');
Route::post('/punte/agent/asteapta', 'Api\PunteController@asteapta');
Route::get('/punte/agent/corp/{comanda}', 'Api\PunteController@corp');
Route::post('/punte/agent/rezultat/{comanda}', 'Api\PunteController@rezultat');
// Innoirea programului de la client: pachet semnat, verificat acolo
Route::get('/punte/agent/actualizare', 'Api\PunteController@actualizare');
// Licenta ceruta de programul de la client, indata dupa instalare
Route::post('/punte/agent/licenta', 'Api\PunteController@licenta');

// Fața dinspre aplicație: se poartă întocmai ca programul local.
Route::any('/punte/{certificat}/{cale?}', 'Api\PunteController@proxy')
    ->where('cale', '.*');

/*
 * Modulul ANAF/SPV — toate rutele cer autentificare și lucrează în contextul
 * clientului selectat: middleware-ul „companie.anaf” verifică apartenența
 * utilizatorului la client și limitează datele la acesta.
 */
/*
 * Administrarea clientilor aplicatiei: firme, conturi, module, abonamente.
 * Rezervata contului din config('app.super_admin') — nu se lucreaza pe un
 * anumit client, ci peste toti, deci nu trece prin filtrul de societate.
 */
Route::middleware(['auth:api', 'administrator.serviciu'])->group(function () {
    Route::get('/administrare/clienti', 'Api\AdministrareController@index');
    Route::post('/administrare/clienti', 'Api\AdministrareController@creeazaClient');
    Route::post('/administrare/clienti/{client}/utilizatori', 'Api\AdministrareController@creeazaUtilizator');
    Route::put('/administrare/clienti/{client}/abonament', 'Api\AdministrareController@salveazaAbonament');
    Route::put('/administrare/utilizatori/{utilizator}', 'Api\AdministrareController@actualizeazaUtilizator');
    Route::post('/administrare/utilizatori/{utilizator}/deconectare', 'Api\AdministrareController@deconecteaza');

    // Instiintari catre utilizatori, in aplicatie si pe email
    Route::post('/administrare/notificari', 'Api\NotificariController@trimite');
    Route::get('/administrare/notificari', 'Api\NotificariController@istoric');
});

/*
 * Notificarile primite. Sunt personale, deci nu tin de societatea selectata.
 */
Route::middleware('auth:api')->group(function () {
    Route::get('/notificari', 'Api\NotificariController@aleMele');
    Route::post('/notificari/citeste-tot', 'Api\NotificariController@marcheazaToate');
    Route::post('/notificari/{notificare}/citita', 'Api\NotificariController@marcheazaCitita');
});

/*
 * Conturile din firma clientului, gestionate de administratorul firmei.
 */
// Ce are voie sa arate interfata pentru clientul selectat
Route::middleware(['auth:api', 'companie'])->group(function () {
    Route::get('/context', 'Api\AuthController@context');
});

Route::middleware(['auth:api', 'companie', 'administrator.client'])->group(function () {
    Route::get('/client/utilizatori', 'Api\UtilizatoriClientController@index');
    Route::post('/client/utilizatori', 'Api\UtilizatoriClientController@store');
    Route::put('/client/utilizatori/{utilizator}', 'Api\UtilizatoriClientController@update');
    Route::post('/client/utilizatori/{utilizator}/deconectare', 'Api\UtilizatoriClientController@deconecteaza');
});

Route::middleware(['auth:api', 'companie.anaf', 'modul:spv'])->group(function () {
    /*
     * Drepturile pe operațiuni sunt momentan dezactivate: accesul se acordă doar
     * pe baza apartenenței la societate. Pentru reactivare, se adaugă middleware-ul
     * cu numele dreptului pe grupul respectiv, de exemplu:
     *   Route::middleware('companie.anaf:verificareMesajeSpv')->group(function () { ... });
     * Drepturile disponibile: verificareMesajeSpv, creareSolicitariSpv,
     * incarcareDeclaratiiAnaf, vizualizareJurnalAnaf.
     */
    Route::get('/spv', 'Api\SpvController@index');
    // Mesajele deja stocate, fara sa se intrebe ANAF
    Route::get('/spv/stocate', 'Api\SpvController@stocate');
    // Urmatorul lot de documente lipsa: fila il cere pana nu mai ramane nimic
    Route::get('/spv/descarca-lipsa', 'Api\SpvController@descarcaLipsa');
    // Aceleasi documente, aduse cu numaratoarea la vedere (flux NDJSON)
    Route::get('/spv/descarca-lipsa/flux', 'Api\SpvController@descarcaLipsaFlux');
    Route::get('/spv/history', 'Api\SpvHistoryController@index');
    Route::get('/spv/download', 'Api\SpvFileController@download');
    Route::get('/spv/fisier', 'Api\SpvFileController@open');

    // Solicitari de documente din SPV (vector fiscal, fisa rol, situatie sintetica etc.)
    Route::get('/spv/solicitari', 'Api\SpvSolicitariController@index');
    Route::get('/spv/solicitari/{solicitare}/fisier', 'Api\SpvSolicitariController@fisier');
    Route::post('/spv/solicitari', 'Api\SpvSolicitariController@store');
    Route::post('/spv/solicitari/preia', 'Api\SpvSolicitariController@preia');
    // Aceeasi preluare, spusa pas cu pas cat timp se lucreaza
    Route::get('/spv/solicitari/preia/flux', 'Api\SpvSolicitariController@preiaFlux');
    Route::post('/spv/solicitari/tipareste', 'Api\SpvSolicitariController@tipareste');
    Route::delete('/spv/solicitari/{solicitare}', 'Api\SpvSolicitariController@destroy');

    // Declaratii: validare (DUKIntegrator), semnare (token), depunere, recipise
    Route::get('/declaratii', 'Api\DeclaratiiController@index');
    Route::post('/declaratii', 'Api\DeclaratiiController@store');
    Route::post('/declaratii/recipise', 'Api\DeclaratiiController@verificaRecipise');
    // Aceeasi verificare, spusa pas cu pas cat timp se lucreaza
    Route::get('/declaratii/recipise/flux', 'Api\DeclaratiiController@verificaRecipiseFlux');
    // Un singur PDF cu declaratiile semnate, pentru tiparire
    Route::post('/declaratii/concateneaza', 'Api\DeclaratiiController@concateneaza');
    Route::post('/declaratii/{declaratie}/valideaza', 'Api\DeclaratiiController@valideazaDeclaratie');
    Route::post('/declaratii/{declaratie}/semneaza', 'Api\DeclaratiiController@semneaza');
    Route::post('/declaratii/{declaratie}/depune', 'Api\DeclaratiiController@depune');
    Route::get('/declaratii/{declaratie}/fisier/{tip}', 'Api\DeclaratiiController@fisier');
    // Erorile validatorului ANAF, explicate pe intelesul oricui
    Route::get('/declaratii/{declaratie}/erori', 'Api\DeclaratiiController@explicaErori');
    Route::delete('/declaratii/{declaratie}', 'Api\DeclaratiiController@destroy');

    // Instiintari pe email cand intra in SPV un anumit fel de document
    Route::get('/spv-alerte', 'Api\AlerteMesajeController@index');
    Route::post('/spv-alerte', 'Api\AlerteMesajeController@store');
    Route::put('/spv-alerte/{alerta}', 'Api\AlerteMesajeController@update');
    Route::delete('/spv-alerte/{alerta}', 'Api\AlerteMesajeController@destroy');

    // Jurnalul de activitate al modulului (cine ce a facut)
    Route::get('/anaf-jurnal', 'Api\JurnalAnafController@index');
    Route::get('/anaf-jurnal/export', 'Api\JurnalAnafController@export');

    // Certificatele digitale folosite si abonatii la avertizarea de expirare
    Route::get('/anaf-certificate', 'Api\CertificateController@index');
    Route::post('/anaf-certificate/sincronizeaza', 'Api\CertificateController@sincronizeaza');
    Route::post('/anaf-certificate/descopera', 'Api\CertificateController@descopera');
    Route::get('/anaf-certificate/kit', 'Api\CertificateController@kit');
    Route::get('/anaf-certificate/{certificat}/foldere', 'Api\CertificateController@foldere');
    Route::get('/anaf-certificate/{certificat}/imprimante', 'Api\CertificateController@imprimante');
    // Reînnoirea licenței programului local, cerută de om, nu așteptând noaptea
    Route::post('/anaf-certificate/{certificat}/licenta', 'Api\CertificateController@reinnoiesteLicenta');
    // Scoaterea din uz a certificatului cu care clientul nu lucrează în SPV
    Route::post('/anaf-certificate/{certificat}/activare', 'Api\CertificateController@comutaActiv');
    Route::put('/anaf-certificate/{certificat}', 'Api\CertificateController@update');
    Route::post('/anaf-certificate/abonare', 'Api\CertificateController@abonare');
    Route::delete('/anaf-certificate/abonare/{abonat}', 'Api\CertificateController@dezabonare');
    Route::post('/anaf-certificate/{certificat}/utilizatori', 'Api\CertificateController@atasareUtilizator');
    Route::delete('/anaf-certificate/utilizatori/{utilizator}', 'Api\CertificateController@detasareUtilizator');

    // Societatile pentru care certificatul digital are drept de semnatura
    Route::get('/anaf-societati', 'Api\AnafSocietatiController@index');
    Route::post('/anaf-societati/sincronizeaza', 'Api\AnafSocietatiController@sincronizeaza');
    Route::post('/anaf-societati/solicita', 'Api\AnafSocietatiController@solicita');
    Route::put('/anaf-societati/{societate}', 'Api\AnafSocietatiController@update');

    // Vector fiscal: asteptat (editabil) vs. citit din SPV, plus situatia pe perioada
    Route::get('/vector-fiscal', 'Api\VectorFiscalController@index');
    Route::post('/vector-fiscal', 'Api\VectorFiscalController@store');
    Route::get('/vector-fiscal/spv', 'Api\VectorFiscalController@spv');
    Route::get('/vector-fiscal/situatie', 'Api\VectorFiscalController@situatie');
    Route::put('/vector-fiscal/{vector}', 'Api\VectorFiscalController@update');
    Route::delete('/vector-fiscal/{vector}', 'Api\VectorFiscalController@destroy');
});
/*
 * e-Transport — declararea si urmarirea transporturilor de bunuri.
 */
Route::middleware(['auth:api', 'companie', 'modul:etransport'])->group(function () {
    Route::get('/anaf-etransport', 'Api\EtransportAnafController@index');
    Route::get('/anaf-etransport/oauth/url', 'Api\EtransportAnafController@oauthUrl');
    Route::get('/anaf-etransport/oauth/stare', 'Api\EtransportAnafController@oauthStare');
    Route::post('/anaf-etransport/sincronizeaza', 'Api\EtransportAnafController@sincronizeaza');
    Route::post('/anaf-etransport/depune', 'Api\EtransportAnafController@depune');
    Route::get('/anaf-etransport/stare', 'Api\EtransportAnafController@stare');
    Route::get('/anaf-etransport/transportator', 'Api\EtransportAnafController@transportator');
});

/*
 * Portal Just — dosare, parti si sedinte din ECRIS. Datele sunt publice si nu se
 * salveaza local, dar modulul se acorda prin abonament, deci se cere si clientul.
 */
Route::middleware(['auth:api', 'companie', 'modul:portal_just'])->group(function () {
    Route::get('/portal-just/institutii', 'Api\PortalJustController@institutii');
    Route::get('/portal-just/dosare', 'Api\PortalJustController@dosare');
    Route::get('/portal-just/sedinte', 'Api\PortalJustController@sedinte');
});

/*
 * Monitorizarea dosarelor: aici datele apartin clientului (ce urmareste, la ce
 * adresa primeste instiintarile), deci se lucreaza in contextul lui.
 */
Route::middleware(['auth:api', 'companie', 'modul:portal_just'])->group(function () {
    Route::get('/portal-just/monitorizari', 'Api\PortalJustMonitorizareController@index');
    Route::post('/portal-just/monitorizari', 'Api\PortalJustMonitorizareController@store');
    Route::post('/portal-just/monitorizari/import', 'Api\PortalJustMonitorizareController@import');
    Route::post('/portal-just/monitorizari/verifica', 'Api\PortalJustMonitorizareController@verifica');
    Route::put('/portal-just/monitorizari/{monitorizare}', 'Api\PortalJustMonitorizareController@update');
    Route::delete('/portal-just/monitorizari/{monitorizare}', 'Api\PortalJustMonitorizareController@destroy');
    Route::get('/portal-just/modificari', 'Api\PortalJustMonitorizareController@modificari');
});

/*
 * Telefoanele care primesc alerte instantanee. Nu cer contextul clientului:
 * alertele sunt personale, legate de utilizator, nu de societate.
 */
Route::middleware('auth:api')->group(function () {
    Route::post('/dispozitive', 'Api\DispozitiveController@store');
    Route::delete('/dispozitive', 'Api\DispozitiveController@destroy');
});

Route::middleware('auth:api')->group(function () {

    Route::post('/logoutAPI','Api\AuthController@logout');
    Route::get('/user','Api\AuthController@user');

});
/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/
