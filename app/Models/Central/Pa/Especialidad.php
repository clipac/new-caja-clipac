<?php

namespace App\Models\Central\Pa;

use Illuminate\Database\Eloquent\Model;

class Especialidad extends Model
{
    protected $connection = 'central';
    protected $table = "pa_especialidades";
    protected $primaryKey = "id_especi";
    protected $keyType = "string";
    public $incrementing = false;
}
