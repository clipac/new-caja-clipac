<?php

namespace App\Models\Central;

use Illuminate\Database\Eloquent\Model;

class Cajero extends Model
{
    protected $connection = 'central';
    protected $table = "pa_cajeros";
    protected $primaryKey = "nro_dni";
    protected $keyType = "string";
    public $incrementing = false;

    public function getNameFullAttribute(){
        return $this->attributes['ape_cajero'].', '.$this->attributes['nom_cajero'];
    }
}
