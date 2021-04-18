<?php

namespace Tfarias\InstaladorTfariasLte\Services\Crud;

use File;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Route;

class CriarController
{

    protected $template = __DIR__.'/CreateCrud/controller.txt';
    protected $modelo_web = __DIR__.'/CreateCrud/web.txt';
    protected $modelo_menu = __DIR__.'/CreateCrud/menu.txt';
    protected $web = 'routes/web.php';
    protected $menu = 'resources/views/partials/lte/nav_bar_left.blade.php';
    protected $templateRotas = __DIR__.'/CreateCrud/rotas.json';

    private $schema;
    /**
     * Cria o model do CRUD.
     *
     * @param string $tabela
     * @param string $titulo
     * @param string $routeAs
     */
    public function criar($tabela, $titulo)
    {
        $this->schema = new Schema($tabela);
        // Nome da classe em CamelCase
        $classe = ucfirst(Str::camel($tabela));

        $controller = File::get($this->template);
        $controller = str_replace('[{tabela}]', $tabela, $controller);
        $controller = str_replace('[{var}]', $tabela, $controller);
        $controller = str_replace('[{tabela_model}]', $classe, $controller);
        $controller = str_replace('[{titulo}]', $titulo, $controller);
        $controller = str_replace('[{namespace}]', Container::getInstance()->getNamespace(), $controller);
        File::put(base_path('app/Http/Controllers/' . $classe . 'Controller.php'), $controller);


        $this->atualizarRotas($tabela, $classe);
        $this->atualizarMenu($titulo, $tabela);
        $this->atualizarJsonRotas($titulo, $tabela, $classe);
    }

    /**
     * Atualiza o arquivo web.php com as novas ações.
     *
     * @param $titulo
     * @param $routeAs
     */
    public function atualizarRotas($routeAs, $classe)
    {
        if (!Route::has($routeAs . '.index')) {
            $web = File::get(base_path($this->web));
            $modelo = File::get($this->modelo_web);
            $modelo = str_replace('[{prefix}]', $routeAs, $modelo);
            $modelo = str_replace('[{route_as}]', $routeAs, $modelo);
            $modelo = str_replace('[{classe}]', $classe, $modelo);
            $web .= $this->schema->nlt(1);
            $web .= $modelo;
            File::put(base_path('routes/web.php'), $web);
        }
    }
    /**
     * Atualiza o arquivo menu.blade.php com as novas ações.
     *
     * @param $titulo
     * @param $routeAs
     */
    public function atualizarMenu($titulo, $routeAs)
    {
        if (!Route::has($routeAs . '.index')) {
            $menu = File::get(base_path($this->menu));
            $modelo = File::get($this->modelo_menu);
            $modelo = str_replace('[{route_as}]', $routeAs, $modelo);
            $modelo = str_replace('[{titulo}]', $titulo, $modelo);
            $web = str_replace('{{--menu--}}', $modelo, $menu);
            File::put(resource_path('views/partials/lte/nav_bar_left.blade.php'), $web);
        }
    }

    public function atualizarJsonRotas($titulo, $routeAs, $titulo_rota)
    {

        if (!Route::has($routeAs . '.index')) {
            $json = \Illuminate\Support\Facades\File::get(base_path('database/seeds/data/permissoes.json'));
            $rotas = json_decode($json, true);
            $modelo = File::get($this->templateRotas);
            $modelo = str_replace('[{titulo_rota}]', $titulo_rota, $modelo);
            $modelo = str_replace('[{titulo}]', $titulo, $modelo);
            $modelo = str_replace('[{route_as}]', $routeAs, $modelo);
            $modelo = json_decode($modelo);
            $rotas = array_merge($rotas, $modelo);
            File::put(base_path('database/seeds/data/permissoes.json'), json_encode($rotas, JSON_UNESCAPED_UNICODE));
        }

    
    }

}
