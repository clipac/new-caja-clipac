<?php

namespace App\Models\Central;

use Illuminate\Database\Eloquent\Model;

class CajaCentral extends Model
{
    protected $connection = 'central';
    protected $table = 'pa_cajas';
    protected $primaryKey = 'nro_caja';
    public $timestamps = false;
}
