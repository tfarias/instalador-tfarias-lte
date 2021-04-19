<?php

namespace Tfarias\InstaladorTfariasLte\Services\Crud;

use Tfarias\InstaladorTfariasLte\Services\TratarCampos;
use File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Config;
use Illuminate\Container\Container;

class CriaForms
{

    protected $template = __DIR__.'/CreateCrud/forms.txt';

    /**
     * Cria o model do CRUD.
     *
     * @param string $tabela
     * @param string $campos
     */
    public function criar($tabela)
    {

        if (!File::isDirectory(base_path('app/Forms'))) {
            File::makeDirectory(base_path('app/Forms'));
        }
        //TODO alterar o crud mapear bancos de dados
        //$banco = env('DB_CONNECTION', 'mysql');
        $schema = new Schema($tabela);
        $campos = $schema->getTableSchema();

        // Nome da classe em CamelCase
        $classe = ucfirst(Str::camel($tabela));
        $inputs ="";
        if (!empty($campos) ) {
            $request = File::get($this->template);

                foreach ($campos as $c) {

                    $c =  (object)$c;

                    if($c->Key=='PRI') {
                        continue;
                    }
                    if($c->Field=='created_at' || $c->Field=='updated_at' || $c->Field=='deleted_at'){
                        continue;
                    }
                    $passou = 0;
                    $aux = "";

                    $referencia = $schema->getTableReference($c->Field);

                    if($c->Null == 'NO') {
                        $aux .= "required";
                        $passou=1;
                    }else{
                        $passou=1;
                        $aux.="nullable";
                    }

                    if($c->Key == 'UNI') {
                        if($passou==1)
                            $aux .= "|unique:{$tabela},{$c->Field}";
                        else{
                            $aux .= "unique:{$tabela},{$c->Field}";
                        }
                        $passou =1;
                    }

                    if($c->Key == 'MUL') {
                        if(!empty($referencia)){
                            if($passou==1)
                                $aux .= "|integer|exists:{$referencia[0]->reftable},id";
                            else{
                                $aux .= "integer|exists:{$referencia[0]->reftable},id";
                            }
                        }
                        $passou =1;
                    }

                    if(!empty($referencia)) {
                        $tab = ucfirst(Str::camel($referencia[0]->reftable));
                        $inputs .= $schema->nlt(1);
                        $inputs .= $schema->nlt(1) . '$this->add("' . $c->Field . '","entity",[';
                        $inputs .= $schema->nlt(1) . '"class"=>\\' . Container::getInstance()->getNamespace() . 'Models\\' . $tab . '::class,';
                        $inputs .= $schema->nlt(1).'"property"=>"'.$schema->get_property().'",';
                        $inputs .= $schema->nlt(1).'"label"=>"'. TratarCampos::tratar_label($c->Field).'",';
                        $inputs .= $schema->nlt(1).'"empty_value"=>"Selecione",';

                        if ($c->Key == 'MUL') {
//                            $inputs .= $schema->nlt(1).'"multiple"=>true,';
//                            $inputs .= $schema->nlt(1).'"attr"=>["name"=>"'.$c->Field.'[]"],';

                        }
                        if($passou==1){
                            $inputs .= $schema->nlt(1).'"rules" => "'.$aux.'",';
                        }

                        $inputs .= $schema->nlt(1).'"wrapper" => ["class" => "form-group"]';

                        $inputs .= $schema->nlt(1).']);';


                    }else{
                        $inputs .= TratarCampos::tratar_input_form($c, $schema);
                        $inputs .= $schema->nlt(1).'"label"=>"'. TratarCampos::tratar_label($c->Field).'",';


                        if ((strpos($c->Type, 'date') !== false) || $c->Type == 'date') {
                            $inputs .= $schema->nlt(1) . '"value" => $this->model ? $this->model->'.$c->Field.'->format("d/m/Y") : "",';
                        }
                        if ((strpos($c->Type, 'datetime') !== false) || $c->Type == 'datetime') {
                            $inputs .= $schema->nlt(1) . '"value" => $this->model ? $this->model->'.$c->Field.'->format("d/m/Y H:i") : "",';
                        }

                        //TODO tratar os campos de forms
                      $is_attr = false;
                        if((strpos($c->Type, 'date') !== false)  || (strpos($c->Type, 'datetime') !== false) ||  ($c->Field=='cpf') || (strpos($c->Type, 'decimal') !== false)) {
                            $is_attr = true;
                            $inputs .= $schema->nlt(1) . '"attr" => [';

                            if ((strpos($c->Type, 'date') !== false) || $c->Type == 'date') {
                                $inputs .= $schema->nlt(1) . '"mask" => "date",';
                            }
                            if ((strpos($c->Type, 'datetime') !== false) || $c->Type == 'datetime') {
                                $inputs .= $schema->nlt(1) . '"mask" => "datetime",';
                            }
                            if ($c->Field == 'cpf') {
                                $inputs .= $schema->nlt(1) . '"mask" => "cpf",';
                                $inputs .= $schema->nlt(1) . '"msg" => "Número de CPF inválido",';
                            }

                            if ((strpos($c->Type, 'decimal') !== false) || $c->Type == 'decimal') {
                                $inputs .= $schema->nlt(1) . '"mask" => "money",';
                            }
                        }

                        if ((strpos($c->Type, 'varchar') !== false) || (strpos($c->Type, 'char') !== false)|| (strpos($c->Type, 'int') !== false)) {
                            $size = $schema->getSizeColum($c->Field);
                            if (isset($size[0]->size)) {
                                if((int)$size[0]->size > 0){
                                    if(!$is_attr){
                                        $is_attr = true;
                                        $inputs .= $schema->nlt(1) . '"attr" => [';
                                    }
                                    $inputs .= $schema->nlt(1) . '"maxlength" => "' . $size[0]->size . '",';
                                }
                            }
                        }

                        if($is_attr){
                            $inputs .= $schema->nlt(1) . '],';
                        }

                        if($passou==1){
                            $inputs .= $schema->nlt(1).'"rules" => "'.$aux.'",';
                        }
                            $inputs .= $schema->nlt(1).'"wrapper" => ["class" => "form-group"]';
                            $inputs .= $schema->nlt(1).']);';

                    }

                }



            $request = str_replace('[{campos}]', $inputs, $request);
            $request = str_replace('[{namespace}]', Container::getInstance()->getNamespace(), $request);
            $request = str_replace('[{tabela_model}]', $classe, $request);
            File::put(base_path('app/Forms/' . $classe . 'Form.php'), $request);
        }


    }


}
