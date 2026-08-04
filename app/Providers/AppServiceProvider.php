<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Filesystem;
use Spatie\Dropbox\Client as DropboxClient;
use Spatie\FlysystemDropbox\DropboxAdapter;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
         setlocale(LC_TIME, 'ro_RO', 'ro', 'RO',  'ro_RO.UTF-8');
       
        \Carbon\Carbon::setLocale(config('app.locale'));

        /*
         * Emailurile pot pleca si prin API-ul HTTP ZeptoMail (Zoho): serverele
         * din cloud au porturile SMTP blocate de gazduire, iar API-ul merge pe
         * 443. Se alege cu MAIL_MAILER=zeptomail si ZEPTOMAIL_KEY in .env.
         */
        $this->app->make('mail.manager')->extend('zeptomail', function () {
            return new \App\Mail\Transport\ZeptoMailTransport(config('services.zeptomail') ?: []);
        });

        Storage::extend('dropbox', function ($app, $config) {
            $client = new DropboxClient(
                                            $config['accessToken']
            );

            return new Filesystem(new DropboxAdapter($client));
        });
    }
}
