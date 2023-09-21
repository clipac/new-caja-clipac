<?php

namespace App\Models\Central\Pa;

use Illuminate\Database\Eloquent\Model;

class Aseguradora extends Model
{
    protected $connection = 'central';
    protected $table='pa_aseguradoras';
    protected $primaryKey='cod_aseg';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $dateFormat = 'd/m/Y H:i:s';
    public $timestamps=false;
}
