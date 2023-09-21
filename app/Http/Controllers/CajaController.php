<?php

namespace App\Http\Controllers;

use App\Impresiones\CierreCajaPrint;
use App\Models\Central\CajaReingresoFondo;
use App\Models\Computer\Information;
use Illuminate\Support\Facades\DB;

class CajaController
{
    public function imprimirCierre($codigo){
        $cajaLocal = Information::first();
        $data=DB::connection('central')->select('exec ticket_cierre_caja ?',[$codigo]);
        if(count($data)==1 && $cajaLocal){
            $printer = $cajaLocal->printer;
            $ticket=new CierreCajaPrint($printer,$data[0]);
            $ticket->imprimir();
            $ticket->cierra();
            return [
                'result'=>true,
                'message'=>'Documento impreso correctamente'
            ];
        }else{
            return [
                'result'=>false,
                'message'=>'No se encontró Codigo de Caja'
            ];
        }

    }
}
