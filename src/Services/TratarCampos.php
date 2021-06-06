<?php


namespace Tfarias\InstaladorTfariasLte\Services;


class TratarCampos
{
    public static function tratar_input_form($c,$schema){
        switch ($c){
            case (strpos($c->Type, 'text') !== false) || $c->Type == 'text':
                return $schema->nlt(1) . '$this->add("' . $c->Field . '","textarea",[';
            case (strpos($c->Type, 'tinyint') !== false) || $c->Type == 'tinyint':
                return $schema->nlt(1) . '$this->add("' . $c->Field . '","checkbox",[';
            case (strpos($c->Type, 'int') !== false) || $c->Type == 'int':
                return $schema->nlt(1) . '$this->add("' . $c->Field . '","number",[';
            case (strpos($c->Type, 'date') !== false) || $c->Type == 'date' || $c->Type == 'datetime':
                return $schema->nlt(1) . '$this->add("' . $c->Field . '","datetime",[';
            case $c->Field == 'email':
                return $schema->nlt(1) . '$this->add("' . $c->Field . '","email",[';
            default:
                return $schema->nlt(1) . '$this->add("' . $c->Field . '","text",[';
        }
    }

    public static function tratar_label($label){
        $field = str_replace('_',' ',$label);
        return ucwords($field);
    }

    public static function tratar_field($fi){
        $field = str_replace('_',' ',$fi);
        $field = ucwords($field);
        return str_replace(' ','',$field);
    }
}
