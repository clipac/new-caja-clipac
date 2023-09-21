<?php

namespace App\Models\Central;

use Illuminate\Database\Eloquent\Model;

class CajaReingresoFondo extends Model
{
    protected $connection = 'central';
    protected $table = 'pa_caja_reingreso_fondos';
    protected $primaryKey = 'codigo_ingreso';
    public $incrementing= false;
    public $timestamps = false;
    protected $dates = ['fecha_ingreso','hora_ingreso','fecha_cierre','hora_cierre'];

    public function cajero(){
        return $this->belongsTo(Cajero::class,'nro_dni','nro_dni');
    }
    public function ingresos(){
        return $this->hasMany(DetalleDocArqueo::class,'codigo_ingreso','codigo_ingreso');
    }

    public function egresos(){
        return $this->hasMany(EgresoCaja::class,'codigo_ingreso','codigo_ingreso');
    }
    public function getEfectivoAttribute(){
        $pago1=$this->ingresos->where('forma_pago',1)->where('estado',0)->sum('monto_doc');
        $pago2=$this->ingresos->where('forma_pago',4)->where('estado',0)->sum('monto_efe');
        return $this->ingresos->where('forma_pago',1)->sum('monto_doc');
    }
}
