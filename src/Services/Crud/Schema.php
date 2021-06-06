<?php

namespace Tfarias\InstaladorTfariasLte\Services\Crud;

use Illuminate\Support\Facades\DB;

class Schema{

    protected $tabela;
    protected $dbschema;

    public function __construct($tabela)
    {
        $this->tabela = $tabela;
    }

    public function getSizeColum($colum){

        switch (env('DB_CONNECTION')) {
            case 'mysql':
                $data =  DB::select("SELECT column_name,character_maximum_length as size
                            FROM   information_schema.columns  WHERE  table_schema = '" . env('DB_DATABASE') . "'
                               AND table_name = `{$this->tabela}`
                               AND column_name = `{$colum}` ");
                break;
            case 'pgsql':
                $data = DB::select("select column_name as Field,column_default as Default,data_type as Type, character_maximum_length as size
        from INFORMATION_SCHEMA.COLUMNS where table_name = `{$this->tabela}` AND column_name = `{$colum}`   ");
                break;
            default;
                $data = [];
                break;
        }


        return $data;
    }
    public function getDbSchema() {
        switch (env('DB_CONNECTION')) {
            case 'mysql':
                $data = DB::select("SELECT table_name AS 'table',  column_name AS  'fk',
            referenced_table_name AS 'reftable', referenced_column_name  AS 'refpk'
            FROM information_schema.key_column_usage
            WHERE referenced_table_name IS NOT NULL
            AND TABLE_SCHEMA='" . env('DB_DATABASE') . "' ");
                break;
            case 'pgsql':
                $data = DB::select("SELECT distinct tc.table_name as table, kcu.column_name as fk, ccu.table_name AS reftable, ccu.column_name AS refpk
                        FROM
                            information_schema.table_constraints AS tc
                            JOIN information_schema.key_column_usage AS kcu
                              ON tc.constraint_name = kcu.constraint_name
                            JOIN information_schema.constraint_column_usage AS ccu
                              ON ccu.constraint_name = tc.constraint_name

                              where (tc.table_name = `{$this->tabela}` or ccu.table_name = `{$this->tabela}`)

                       ");
                break;
            default;
                $data = [];
                break;
        }



        $this->dbschema = $data;

        return $this->dbschema;
    }
      public function getTableReference($key) {

          switch (env('DB_CONNECTION')) {
              case 'mysql':
                  $data = DB::select("SELECT table_name AS 'table',  column_name AS  'fk',
                    referenced_table_name AS 'reftable', referenced_column_name  AS 'refpk'
                    FROM information_schema.key_column_usage
                    WHERE referenced_table_name IS NOT NULL
                    AND TABLE_SCHEMA='" . env('DB_DATABASE') . "' AND table_name = `{$this->tabela}` AND column_name = `{$key}` limit 1;");
                  break;
              case 'pgsql':
                  $data = DB::select("SELECT tc.table_name as table, kcu.column_name as fk, ccu.table_name AS reftable, ccu.column_name AS refpk
                        FROM
                            information_schema.table_constraints AS tc
                            JOIN information_schema.key_column_usage AS kcu
                              ON tc.constraint_name = kcu.constraint_name
                            JOIN information_schema.constraint_column_usage AS ccu
                              ON ccu.constraint_name = tc.constraint_name
                        WHERE constraint_type = 'FOREIGN KEY' AND tc.table_name=`{$this->tabela}` AND kcu.column_name = `{$key}` limit 1;");
                  break;
              default;
                  $data = [];
                  break;
          }

        return (array)$data;
    }



    public function getTableSchema() {
        switch (env('DB_CONNECTION')){
            case 'mysql':
                return DB::select("SHOW FULL COLUMNS FROM `{$this->tabela}`");
            break;
            case 'pgsql':
                return $this->describle_postgress();
                break;
        }
    }


    public function describle_postgress(){
        $query = DB::select("select
                                    isc.column_name as Field,
                                    isc.column_default as Default,
                                    isc.column_default as Key,
                                    isc.data_type as Type,
                                    isc.character_maximum_length as size,
                                    pg_catalog.col_description(format('%s.%s',isc.table_schema,isc.table_name)::regclass::oid,isc.ordinal_position) as Comment
                                    from INFORMATION_SCHEMA.COLUMNS isc where table_name = `{$this->tabela}`");
        $res = [];
        if(!empty($query)){
            foreach ($query as $q) {
                $key = $this->check_constrain($q->field);


                if(!empty($key)){
                    if($key[0]->constraint_type=='PRIMARY KEY'){
                        $res_key = 'PRI';
                    }else if($key[0]->constraint_type=='FOREIGN KEY'){
                        $res_key ='MUL';
                    }
                }else{
                    $res_key= null;
                }
                $res[]=[
                    'Key'=> $res_key,
                    'Field' => $q->field,
                    'Default' => $q->default,
                    'Type' =>$q->type,
                    'size' => $q->size,
                    'Null' => $this->check_isnullable($q->field)[0]->is_nullable,
                    'Comment' => $q->comment

                ];
            }
        }
        return (object)$res;
    }

    function check_constrain($coluna){
        return DB::select("SELECT t.constraint_type
                    FROM information_schema.key_column_usage AS c
                    LEFT JOIN information_schema.table_constraints AS t
                    ON t.constraint_name = c.constraint_name
                    WHERE t.table_name = `{$this->tabela}` AND c.column_name = `{$coluna}` limit 1");
    }

    function check_isnullable($coluna){
        return DB::select(" SELECT column_name, is_nullable FROM information_schema.columns WHERE table_name = `{$this->tabela}` and column_name = `{$coluna}` limit 1");
    }
    public function nlt($n) {
        $r = "\n";
        for ($i = 0; $i < $n; $i++)
            $r = $r . "    ";
        return $r;
    }

    public function getPlural($nome) {
        if (substr($nome, -1) == "s")
            return $nome;
        if (substr($nome, -1) == "r")
            return $nome . "es";
        if (substr($nome, -1) == "m")
            return substr($nome, 0, -1) . "ns";
        if (substr($nome, -1) == "l")
            return substr($nome, 0, -1) . "is";
        return $nome . "s";
    }

    public function getTabelaAssociacao() {
        switch (env('DB_CONNECTION')) {
            case 'mysql':
                $data = DB::select("SELECT table_name AS 'table',  column_name AS  'fk',
                    referenced_table_name AS 'reftable', referenced_column_name  AS 'refpk'
                    FROM information_schema.key_column_usage
                    WHERE referenced_table_name IS NOT NULL
                    AND TABLE_SCHEMA='" . env('DB_DATABASE') . "' AND table_name = `{$this->tabela}`");
                break;
            case 'pgsql':
                $data = DB::select("SELECT tc.table_name as table, kcu.column_name as fk, ccu.table_name AS reftable, ccu.column_name AS refpk
                        FROM
                            information_schema.table_constraints AS tc
                            JOIN information_schema.key_column_usage AS kcu
                              ON tc.constraint_name = kcu.constraint_name
                            JOIN information_schema.constraint_column_usage AS ccu
                              ON ccu.constraint_name = tc.constraint_name
                        WHERE constraint_type = 'FOREIGN KEY' AND tc.table_name=`{$this->tabela}`;");
                break;
                default;
                $data = [];
                break;
        }


        $this->dbschema = $data;

        return $this->dbschema;
    }


    public function get_property(){
        $result = $this->getTableSchema();
        $res = null;
        foreach ($result as $item) {
            $item = (object)$item;
            if($item->Field == 'nome'){
                return 'nome';
            }elseif($item->Field == 'descricao'){
                return 'descricao';
            }
        }
        return 'id';
    }
}
