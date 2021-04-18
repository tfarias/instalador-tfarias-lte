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
}
