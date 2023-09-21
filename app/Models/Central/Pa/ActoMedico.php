<?php

namespace App\Models\Central\Pa;

use App\Models\Pa\Doctor;
use Illuminate\Database\Eloquent\Model;

class ActoMedico extends Model
{
    protected $connection = 'central';
    protected $table='pa_acto_medico';
    protected $primaryKey = "nro_acto";
    protected $keyType = "integer";
    //protected $with = ["especialidad","paciente","doctor","consulta_recetas"];
    protected $dateFormat = 'd/m/Y H:i:s';
    protected $dates = ['fec_crea','hora_crea', 'per_ate_fecha', 'fecha_crea', 'hara_final'];
    public $timestamps=false;

    public function punto_venta(){
        return $this->belongsTo('App\Models\Central\PuntoVenta','id_puntoventa','IdPuntoVenta');
    }

    public function procedencia(){
        return $this->belongsTo('App\Models\Central\Pa\Procedencia','id_procede');
    }

    public function paciente(){
        return $this->belongsTo('App\Models\Central\Pa\Paciente','nro_historia');
    }

    public function aseguradora(){
        return $this->belongsTo('App\Models\Central\Pa\Aseguradora','cod_aseg');
    }

    public function empleadora(){
        return $this->belongsTo('App\Models\Central\Pa\Empleadora','cod_emp');
    }

    public function doctor(){
        return $this->belongsTo('App\Models\Central\Pa\Doctor','cod_doc');
    }

    public function especialidad(){
        return $this->belongsTo("App\Models\Central\Pa\Especialidad", "id_especi", "id_especi");
    }

    public function servicio(){
        return $this->belongsTo("App\Models\Central\Pa\SubOrigenPlan","codsub_origen");
    }

}
