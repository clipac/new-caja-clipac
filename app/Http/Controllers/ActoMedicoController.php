<?php

namespace App\Http\Controllers;

use App\Impresiones\ActoMedicoTicket;
use App\Impresiones\TicketPrint;
use App\Models\Central\Pa\ActoMedico;

class ActoMedicoController extends Controller
{
    public function print($nroacto,$usuario){
        $acto=ActoMedico::find($nroacto);
        if(!$acto){
            return response()->json([
                'mensaje'=>'Acto Medico Inexistente'
            ],401);
        }
        $acto_anterior= ActoMedico::where('nro_historia', $acto->nro_historia)->where('nro_acto', '<', $nroacto)->orderBy('nro_acto', 'DESC')->first();
        $ticket=new ActoMedicoTicket('EPSON TM',$acto,$acto_anterior,$usuario);
        $ticket->imprimir();
        $ticket->cierra();
        return response()->json([
            'mensaje'=>'Documento impreso correctamente'
        ]);
    }
}
