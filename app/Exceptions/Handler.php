<?php

namespace App\Exceptions;

use App\Services\Anaf\AlertaEroare;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            if ($this->meritaInstiintare($e)) {
                AlertaEroare::trimite($this->undeS_a_intamplat(), $e);
            }
        });
    }

    /**
     * [2026-09-07] Cererea fara token pe endpointul MCP.
     *
     * Specificatia MCP cere ca un 401 sa poarte antetul `WWW-Authenticate` cu
     * adresa documentului de metadate, ca un client nou (conectorul Claude) sa
     * stie de unde sa porneasca autorizarea. Si raspunsul e mereu JSON, oricare
     * ar fi `Accept`: un client care deschide `GET /mcp` pentru fluxul cu
     * server-sent events nu trebuie trimis la pagina de login.
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->is('mcp')) {
            return response()->json(['message' => 'Unauthenticated.'], 401, [
                'WWW-Authenticate' => 'Bearer resource_metadata="'
                    . url('/.well-known/oauth-protected-resource') . '"',
            ]);
        }

        return parent::unauthenticated($request, $exception);
    }

    /**
     * Care erori merita un email.
     *
     * Nu tot ce se opreste e o defectiune: o pagina care nu exista, un formular
     * completat gresit sau o sesiune expirata sunt viata de zi cu zi. Se
     * instiinteaza numai ce inseamna cu adevarat ca ceva s-a stricat la noi.
     */
    protected function meritaInstiintare(Throwable $e): bool
    {
        if ($e instanceof ValidationException || $e instanceof AuthenticationException) {
            return false;
        }

        if ($e instanceof HttpExceptionInterface) {
            return $e->getStatusCode() >= 500;
        }

        return true;
    }

    /** Unde s-a intamplat, pe intelesul cuiva grabit. */
    protected function undeS_a_intamplat(): string
    {
        if (app()->runningInConsole()) {
            return 'o comandă din planificator';
        }

        $cerere = request();

        return $cerere ? $cerere->method() . ' ' . $cerere->path() : 'aplicație';
    }
}
