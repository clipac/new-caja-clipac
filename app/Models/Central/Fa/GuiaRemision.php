<?php

namespace App\Models\Central\Fa;

use App\Models\Central\Pa\ActoMedico;
use Illuminate\Database\Eloquent\Model;

class GuiaRemision extends Model
{
    protected $connection = 'central';
    protected $table='fa_guia_remision';
    public $timestamps = false;
    protected $dates = ['fecha_crea'];

    public function detalle() {
        return $this->hasMany(DetalleGuiaRemision::class, 'gr_id', 'id');
    }

    public function punto_venta(){
        return $this->belongsTo('App\Models\Central\PuntoVenta','id_puntoventa','IdPuntoVenta');
    }

    public function actomedico() {
        return $this->belongsTo(ActoMedico::class,'nro_acto','nro_acto');
    }

    public function getNameMotivoAttribute()
    {
        switch ($this->attributes['motivo']) {
            case 1:
                $name_motivo = 'VENTA';
                break;
            case 3:
                $name_motivo = 'COMPRA';
                break;
            case 5:
                $name_motivo = 'DEVOLUCION';
                break;
            case 6:
                $name_motivo = 'TRASLADO ENTRE EL MISMO ESTABLECIMIENTO';
                break;
            case 7:
                $name_motivo = 'OTROS';
                break;
            default:
                $name_motivo = '------';
                break;
        }

        return $name_motivo;
    }

    public function getTipoProductoAttribute()
    {
        switch ($this->attributes['cod_tipo']) {
            case 'FA':
                $tipo_producto = 'AFECTO';
                break;
            case 'FI':
                $tipo_producto = 'INAFECTO';
                break;
            case 'IP':
                $tipo_producto = 'IMPLANTES';
                break;
            default:
                $tipo_producto = '------';
                break;
        }

        return $tipo_producto;
    }

    
}