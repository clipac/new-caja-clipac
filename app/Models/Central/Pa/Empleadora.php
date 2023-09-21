<?php

namespace App\Models\Central\Pa;

use Illuminate\Database\Eloquent\Model;

class Empleadora extends Model
{
    protected $connection = 'central';
    protected $table='pa_empleadoras';
    protected $primaryKey='cod_emp';
    const CREATED_AT = 'fecha_crea';
    const UPDATED_AT = 'fecha_actualiza';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $dates = ['fecha_crea','fecha_actualiza'];
    protected $dateFormat = 'd/m/Y H:i:s';
}
