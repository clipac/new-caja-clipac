<?php

namespace App\Models\Central\Pa;

use Illuminate\Database\Eloquent\Model;

class Procedencia extends Model
{
    protected $connection = 'central';
    protected $table='pa_procedencia';
    protected $primaryKey = "id_procede";
    protected $keyType = "string";
    public $incrementing = false;

    public function usesTimestamps() : bool{
        return false;
    }
    public function acto_medicos(){
        return $this->hasMany('App\Models\Central\Pa\ActoMedico','id_procede');
    }
}
