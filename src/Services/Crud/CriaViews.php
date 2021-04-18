<?php

namespace Tfarias\InstaladorTfariasLte\Services\Crud;

use File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CriaViews
{

    protected $routeAs;

    protected $titulo;

    protected $tabela;

    protected $schema;

    protected $camposForm = [];

    protected $colunas = [];

    protected $templates = [
        'index'         => __DIR__.'/CreateCrud/index.txt',
        'create'     => __DIR__.'/CreateCrud/create.txt',
        'edit'       => __DIR__.'/CreateCrud/edit.txt',
        'imprimir'      => __DIR__.'/CreateCrud/imprimir.txt',
        'campo'         => __DIR__.'/CreateCrud/campo.txt',
        'campos_filtro' => __DIR__.'/CreateCrud/campo_filtro.txt',
        'form'          => __DIR__.'/CreateCrud/form.txt',
        'listagem'      => __DIR__.'/CreateCrud/listagem.txt',
    ];

    protected $input = [
        'text' =>'<input type="text" name="[{campo}]" id="[{campo}]" class="form-control" value="{{ request(\'[{campo}]\') }}" placeholder="[{label}]" [{options}]>',
        'hidden' =>'<input type="hidden" name="[{campo}]" id="[{campo}]" class="form-control" value="[{value}]" placeholder="[{label}]" [{options}]>',
        'number' =>'<input type="number" name="[{campo}]" id="[{campo}]" class="form-control" value="{{ request(\'[{campo}]\') }}" placeholder="[{label}]" [{options}]>',
        'select' =>'<select name="[{campo}]" id="[{campo}]" class="form-control" [{options}]></select>',
        'enum' =>'<select name="[{campo}]" id="[{campo}]" class="form-control" placeholder="[{label}]" [{options}]>[{enum}]</select>',
        'checkbox' =>'<input type="checkbox" name="[{campo}]" id="[{campo}]" class="form-control" placeholder="[{label}]" [{options}]>',
        'textarea' =>'<textarea name="[{campo}]" id="[{campo}]" class="form-control" placeholder="[{label}]" [{options}]>{{ isset($[{tabela}]) ? $[{tabela}]->[{campo}] : \'\' }}</textarea>',
    ];

    protected $campo_form = "<div class=\"col-md-6\">
                                <div class=\"form-group\">
                                    <label class=\"control-label\" for=\"[{campo}]\">
                                        [{label}] [{required}]
                                    </label>
                                    [{input}]
                                </div>
                             </div>";

    protected $campo_filtro= " <div class=\"form-group col-lg-3 col-sm-6\">
                            <label for=\"[{campo}]\">[{label}]</label>
                             [{input}]
                        </div>";

    protected $associacoes;
    /**
     * Cria as views para o comando create-crud.
     *
     * @param string $tabela
     * @param string $titulo
     * @param string $routeAs
     * @param string $campos
     * @param string $colunas
     */
    public function criar($tabela, $titulo)
    {
        $this->tabela = $tabela;
        $this->titulo = $titulo;
        $this->routeAs = $tabela;
        $this->schema = new Schema($tabela);

        $table = $this->schema->getTableSchema();


        $this->camposForm = $table;
        $this->colunas = $table;

        $schema = new Schema($tabela);
        $this->associacoes = $schema->getTabelaAssociacao();

        // Cria a pasta
        File::makeDirectory(base_path('resources/views/' . $tabela),0755, true, true);

        $this->criarViewComum('create');
        $this->criarViewComum('edit');
        $this->criarViewComum('imprimir');
        $this->viewIndex();
        $this->viewFormulario();
        $this->viewListagem();

    }

    /**
     * Cria uma view comum.
     *
     * @param string $nome Nome da view.
     */
    public function criarViewComum($nome)
    {
        $view = File::get($this->templates[$nome]);
        $view = str_replace('[{tabela}]', $this->tabela, $view);
        $view = str_replace('[{titulo}]', $this->titulo, $view);
        $view = str_replace('[{route_as}]', $this->routeAs, $view);
        File::put(base_path('resources/views/' . $this->tabela . "/$nome.blade.php"), $view);
    }

    private function associacao($campo){
        $result = null;
        if(!empty($this->associacoes)){
            foreach ($this->associacoes as $associacoe) {
                if($campo==$associacoe->fk){
                    $result = $associacoe;
                }
            }
        }
        return $result;
    }
    /**
     * Cria a view index.
     */
    private function viewIndex()
    {

        if (!empty($this->camposForm)) {
            $index = File::get($this->templates['index']);
            $stringCampos = "";

            foreach ($this->camposForm as $c) {
                $c =  (object)$c;



                if($c->Key=='PRI' ) {
                    continue;
                }
                if($c->Field=='created_at' || $c->Field=='updated_at' || $c->Field=='deleted_at'){
                    continue;
                }

                if($c->Comment!="filter"){
                    continue;
                }
                $stringCampos .= $this->campo_filtro;
                $assoc = $this->associacao($c->Field);
                if(!empty($assoc)){
                    $stringCampos = str_replace('[{input}]', $this->input['select'], $stringCampos);
                }else
                if(strripos($c->Type, 'enum')){
                    $stringCampos = str_replace('[{input}]', $this->input['enum'], $stringCampos);
                }elseif(strripos($c->Type, 'tinyint')){
                    $stringCampos = str_replace('[{input}]', $this->input['checkbox'], $stringCampos);
                }elseif(strripos($c->Type, 'text')){
                    $stringCampos = str_replace('[{input}]', $this->input['textarea'], $stringCampos);
                }else{
                    $stringCampos = str_replace('[{input}]', $this->input['text'], $stringCampos);
                }


                $options = "";
                $options.=(strpos($c->Type, 'date') !== false) ? " mask=\"date\" ":"";
                $options.=(strpos($c->Type, 'datetime') !== false) || ($c->Type == 'datetime') ? " mask=\"datetime\" ":"";
                $options.=($c->Field == 'cpf') ? " mask=\"cpf\" msg=\"Número de CPF inválido\" ":"";
                $options.=(strpos($c->Type, 'decimal') !== false) ? " mask=\"money\" ":"";
                if((strpos($c->Type, 'varchar') !== false)  || (strpos($c->Type, 'char') !== false) ||  (strpos($c->Type, 'int') !== false) || (strpos($c->Type, 'decimal') !== false)){
                    $size = $this->schema->getSizeColum($c->Field);
                    if(!empty($size)){
                        $options.=" maxlength=\"".$size[0]->size."\" ";
                    }
                }



                if(!empty($assoc)){
                    $options.=" data=\"select\" action=\"{{route('".$assoc->reftable.".fill')}}\" ";
                    $options.=" {{!empty(request('".$c->Field."')) ? 'sel=update update='.route('".$assoc->reftable.".getedit',request('".$c->Field."')) : ''}} ";
                        $stringCampos = str_replace('[{label}]', ucfirst(Str::camel($assoc->reftable)), $stringCampos);
                    }else{
                        $stringCampos = str_replace('[{label}]', ucfirst(Str::camel($c->Field)), $stringCampos);
                    }

                    $stringCampos = str_replace('[{campo}]', $c->Field, $stringCampos);
                    $stringCampos = str_replace('[{options}]', $options, $stringCampos);

            }
            $stringCampos = str_replace('[{input}]',$stringCampos, $stringCampos);

            $index = str_replace('[{campos_formulario_filtro}]', $stringCampos, $index);
            $index = str_replace('[{route_as}]', $this->routeAs, $index);
            $index = str_replace('[{tabela}]', $this->tabela, $index);
            $index = str_replace('[{titulo}]', $this->titulo, $index);
            File::put(base_path('resources/views/' . $this->tabela . "/index.blade.php"), $index);
        }
    }

    /**
     * Cria a view do formulario.
     */
    private function viewFormulario()
    {
        $form = File::get($this->templates['form']);
        if (!empty($this->camposForm)) {
            File::put(base_path('resources/views/' . $this->tabela . "/form.blade.php"),$form);
        }


    }

    /**
     * Cria a view de listagem.
     */
    private function viewListagem()
    {
        if (count($this->camposForm) > 0) {
            $listagem = File::get($this->templates['listagem']);
            if (!empty($this->camposForm)) {
                File::put(base_path('resources/views/' . $this->tabela . "/listagem.blade.php"), $listagem);
            }
        }
    }
}
