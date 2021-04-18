<?php

namespace Tfarias\InstaladorTfariasLte\Services\Crud;

use File;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CriarRelatorio
{

    protected $template = __DIR__.'/CreateCrud/relatorio.txt';
    protected $dbschema;
    /**
     * Cria o model do CRUD.
     *
     * @param string $titulo
     * @param string $tabela
     * @param string $campos
     */
    public function criar($titulo, $tabela)
    {
        $schema = new Schema($tabela);

        $campos = $schema->getTableSchema();
        // Nome da classe em CamelCase
        $classe = ucfirst(Str::camel($tabela));

        if (!File::isDirectory(base_path('app/Relatorios'))) {
            File::makeDirectory(base_path('app/Relatorios'));
        }

        if(!empty($campos)){
            $relatorio = File::get($this->template);
            $stringCampos = "";
            foreach ($campos as $c){
                $c =  (object)$c;
                if($c->Field=='created_at' || $c->Field=='updated_at' || $c->Field=='deleted_at'){
                    continue;
                }
                if($c->Type=='integer' || $c->Type=='INT' || strstr(strtolower($c->Type),'int')){
                    $stringCampos .= 'if (!empty($filtros["' . $c->Field . '"])) { $dados->where("' . $c->Field . '", "=", $filtros["' . $c->Field . '"] ); }';
                }else{
                    $stringCampos .= 'if (!empty($filtros["' . $c->Field . '"])) { $dados->where("' . $c->Field . '", "LIKE", "%" . $filtros["' . $c->Field . '"] . "%"); }';
                }
            }
            $relatorio = str_replace('[{filtros_if}]', $stringCampos, $relatorio);
            $relatorio = str_replace('[{titulo}]', $titulo, $relatorio);
            $relatorio = str_replace('[{tabela}]', $tabela, $relatorio);
            $relatorio = str_replace('[{tabela_model}]', $classe, $relatorio);
            $relatorio = str_replace('[{namespace}]', Container::getInstance()->getNamespace(), $relatorio);
            File::put(base_path('app/Relatorios/' . $classe . 'Listagem.php'), $relatorio);
        }

    }

}
