<?php

namespace App\Models\Central\Pa;

use Illuminate\Database\Eloquent\Model;

class Paciente extends Model
{
    protected $connection = 'central';
    protected $table='pa_pacientes';
    protected $primaryKey='nro_historia';
    protected $keyType = 'string';
    public $incrementing = false;
    const CREATED_AT = 'fecha_cre';
    const UPDATED_AT = 'fecha_mod';
    protected $dates = ['fecha_cre','fecha_mod', 'fec_naci', 'fec_vigente'];
    protected $dateFormat = 'd/m/Y H:i:s';
    protected $appends = ['full_name','desc_tipo_paciente'];

    public function getFullNameAttribute(): string
    {
        return trim($this->attributes['ape_paterno']).' '.trim($this->attributes['ape_materno']).
            ' '.trim($this->attributes['nom_paciente']);
    }
    public function getDescTipoPacienteAttribute(): string
    {
        return $this->attributes['id_tipo_paci'] == '02'?"ASEGURADO":"PARTICULAR";
    }


}
