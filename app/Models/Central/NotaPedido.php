<?php

namespace App\Models\Central;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class NotaPedido extends Model
{
    protected $connection = 'central';
    protected $table = 'fa_detalle_pedidos_farma';
    protected $primaryKey = 'nro_secuencia';
    public $timestamps = false;

    public function documento($nro_secuencia, $id_puntoventa)
    {
        $rs = DB::connection('central')->select('EXEC web_ticketNotaPedido ?,?', array($nro_secuencia, $id_puntoventa));
        if($rs && count($rs) > 0) {
            return $rs;
        }else {
            return null;
        }
    }
}
