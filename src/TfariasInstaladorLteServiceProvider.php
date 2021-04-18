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
            __DIR__ . '/publishes/buttons' => base_path('resources/views/partials/buttons/lte'),
            __DIR__ . '/publishes/layout/lte' => base_path('resources/views/layouts/lte'),
            __DIR__ . '/publishes/partials/assets/lte' => base_path('resources/views/partials/assets/lte'),
            __DIR__ . '/publishes/partials/lte' => base_path('resources/views/partials/lte'),
            __DIR__ . '/publishes/dist' => base_path('public/lte/dist'),
            __DIR__ . '/publishes/plugins' => base_path('public/lte/plugins'),
            __DIR__ . '/publishes/permissoes.json' => base_path('database/seeds/data'),
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
