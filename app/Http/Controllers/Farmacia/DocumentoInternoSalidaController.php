<?php

namespace App\Http\Controllers\Farmacia;

use App\Http\Controllers\Controller;
use App\Impresiones\DocumentoInternoSalidaTicket;
use App\Models\Central\Fa\GuiaRemision;

class DocumentoInternoSalidaController extends Controller
{
    public function print($id){
        $guia = GuiaRemision::find($id);
        if(!$guia){
            return response()->json([
                'mensaje'=>'Guia de Remision Inexistente'
            ],401);
        }
        $ticket = new DocumentoInternoSalidaTicket('EPSON TM',$guia);
        for($i=1; $i<=env('N_COPIAS',2);$i++){
            $ticket->imprimir();
        }
        $ticket->cierra();
        return response()->json([
            'mensaje'=>'Documento impreso correctamente'
        ]);
    }
}
