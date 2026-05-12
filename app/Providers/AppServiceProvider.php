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
        Storage::extend('dropbox', function ($app, $config) {
            $client = new DropboxClient(
                                            $config['accessToken']
            );

            return new Filesystem(new DropboxAdapter($client));
        });
    }
}
