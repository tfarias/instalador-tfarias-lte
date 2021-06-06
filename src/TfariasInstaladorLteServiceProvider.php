<?php
namespace Tfarias\InstaladorTfariasLte;

use Illuminate\Support\ServiceProvider;
use Tfarias\InstaladorTfariasLte\Commands\CreateCrud;
use Tfarias\InstaladorTfariasLte\Commands\CreateModel;

class TfariasInstaladorLteServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/publishes' => base_path('/')
        ]);

    }
    public function register()
    {
        $this->commands([
            CreateCrud::class,
            CreateModel::class,
        ]);
    }
}
