<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class App\BaseModel
 * @package App\
 *
 */
class BaseModel extends Model
{

    protected $guarded = ['id'];

    protected $primaryKey = 'id';
}