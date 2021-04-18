<?php

namespace Tfarias\InstaladorTfariasLte\Services\Crud;

use File;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CriarCriteria
{

    protected $template = __DIR__.'/CreateCrud/criteria.txt';
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

        if (!File::isDirectory(base_path('app/Criteria'))) {
            File::makeDirectory(base_path('app/Criteria'));
        }

        if(!empty($campos)){
            $criteria = File::get($this->template);
            $stringCampos = "";
            foreach ($campos as $c){
                $c =  (object)$c;
                if($c->Field=='created_at' || $c->Field=='updated_at' || $c->Field=='deleted_at'){
                    continue;
                }
                if($c->Type=='integer' || $c->Type=='INT' || strstr(strtolower($c->Type),'int')){
                    $stringCampos .= 'if(!empty($this->parans["'.$c->Field.'"])){';
                    $stringCampos .= $schema->nlt(1);
                    $stringCampos .= '$filtro->where("'.$c->Field.'",$this->parans["'.$c->Field.'"]);';
                    $stringCampos .= $schema->nlt(1);
                    $stringCampos .= '}';

                }else{
                    $stringCampos .= $schema->nlt(1);
                    $stringCampos .= 'if(!empty($this->parans["'.$c->Field.'"])){';
                    $stringCampos .= $schema->nlt(1);
                    $stringCampos .= '$filtro->where("'.$c->Field.'",\'like\',\'%\'.$this->parans["'.$c->Field.'"].\'%\');';
                    $stringCampos .= $schema->nlt(1);
                    $stringCampos .= '}';
                }
            }
            $criteria = str_replace('[{filtros_if}]', $stringCampos, $criteria);
            $criteria = str_replace('[{titulo}]', $titulo, $criteria);
            $criteria = str_replace('[{tabela_model}]', $classe, $criteria);
            $criteria = str_replace('[{namespace}]', Container::getInstance()->getNamespace(), $criteria);
            File::put(base_path('app/Criteria/' . $classe . 'Criteria.php'), $criteria);
        }

    }

}
