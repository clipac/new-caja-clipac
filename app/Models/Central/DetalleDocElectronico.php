<?php

namespace App\Models\Central;

use Illuminate\Database\Eloquent\Model;

class DetalleDocElectronico extends Model
{
    protected $connection = 'central';
    protected $table = 'facturacion_electronicadetalle';
    protected $primaryKey = 'tb_ventadetalle_id';
    public $timestamps=false;
}
