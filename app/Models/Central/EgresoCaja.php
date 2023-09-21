<?php

namespace App\Models\Central;

use Illuminate\Database\Eloquent\Model;

class EgresoCaja extends Model
{
    protected $table='ca_egresos';
    protected $primaryKey='nro_egreso';
    public $timestamps=false;
    protected $dates=['fec_egreso'];


}
