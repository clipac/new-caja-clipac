<?php

namespace App\Models\Central\Fa;

use Illuminate\Database\Eloquent\Model;

class DetalleGuiaRemision extends Model
{
    protected $connection = 'central';
    protected $table = 'fa_detalle_guia_remision';
    protected $primaryKey = 'nro_secuencia';
    protected $keyType = 'integer';
    public $incrementing = true;
    public $timestamps = false;
    
    public function producto(){
        return $this->belongsTo(Producto::class,'codpro');
    }
}