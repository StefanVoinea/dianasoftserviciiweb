<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApplicationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/




/*
 * Dezabonarea de la scrisorile de marketing.
 *
 * Sta inaintea rutei care prinde tot: pusa dupa, ea n-ar fi fost gasita
 * niciodata, iar omul care apasa in scrisoare ar fi ajuns in aplicatie.
 *
 * Fara autentificare si fara nicio piedica: cine primeste o scrisoare trebuie sa
 * poata iesi dintr-o apasare. Si la POST, nu numai la GET, fiindca asa cere
 * antetul „List-Unsubscribe-Post", prin care programele de posta isi arata
 * butonul lor.
 */
Route::match(['get', 'post'], '/dezabonare/{jeton}', [App\Http\Controllers\DezabonareController::class, 'arata'])
    ->name('dezabonare')
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

/*
 * Butonul „Solicita demo" din scrisoare. Tot public, si tot inaintea rutei care
 * prinde tot: cine primeste o scrisoare n-are cont la noi.
 */
Route::get('/demo/{jeton}', [App\Http\Controllers\DemoController::class, 'arata'])->name('demo');
Route::post('/demo/{jeton}', [App\Http\Controllers\DemoController::class, 'primeste']);

Route::get('/{any}', [ApplicationController::class, 'index'])->where('any', '.*');

