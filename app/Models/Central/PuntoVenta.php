<?php

namespace App\Models\Central;

use Illuminate\Database\Eloquent\Model;

class PuntoVenta extends Model
{
    protected $connection = 'central';
    protected $table='pa_puntoventa';
    protected $primaryKey='IdPuntoVenta';
    public $timestamps= false;



}
