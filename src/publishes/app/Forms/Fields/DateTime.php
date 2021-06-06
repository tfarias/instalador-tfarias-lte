<?php
/**
 * Created by PhpStorm.
 * User: tiagos
 * Date: 24/10/2018
 * Time: 09:48
 */

namespace App\Forms\Fields;


use Kris\LaravelFormBuilder\Fields\FormField;

class DateTime extends FormField
{

    /**
     * Get the template, can be config variable or view path.
     *
     * @return string
     */
    protected function getTemplate()
    {
        return 'fields.datetime';
    }

    public function render(array $options = [], $showLabel = true, $showField = true, $showError = true)
    {
        return parent::render($options, $showLabel, $showField, $showError);
    }
}
