<?php

namespace App\Models\Central\Pa;

use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    protected $connection = 'central';
    protected $table = "pa_doctores";
    protected $primaryKey = "cod_doc";
    protected $keyType = "string";
    public $incrementing = false;

}
