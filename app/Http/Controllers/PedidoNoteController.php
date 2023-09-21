<?php 

namespace App\Http\Controllers;

use App\Impresiones\PedidoNotaPrint;
use App\Models\Central\{
	CajaCentral,
	NotaPedido
};
use App\Models\Computer\Information;

class PedidoNoteController extends Controller
{
    public function __construct()
    {
        //
    }

    public function printNote($nro_secuencia, $id_puntoventa, $idcaja)
    {
    	$objImpresion = new NotaPedido();
    	$data = $objImpresion->documento((int)$nro_secuencia, (int)$id_puntoventa);

		if(count($data))
		{
			// Check cash
	        $printer = null;
	        $cajaCentral = CajaCentral::where('nro_caja',trim($idcaja))->first();
	        $cajaLocal = Information::where('hostname',trim($cajaCentral->host_name))->first();

	        if($cajaLocal->printer && strlen($cajaLocal->printer)>0){
	            $printer = trim($cajaLocal->printer);
	        }else{
	            $cajaLocal->printer = $cajaCentral->impresora;
	            $cajaLocal->save();
	            $printer = trim($cajaCentral->impresora);
	        }

	    	$note=new PedidoNotaPrint('EPSON TM', $data);
	        $note->imprimir();
	        $note->cierra();
	        return response()->json([
	            'mensaje'=>'Ticket impreso correctamente!'
	        ],200);
	    }else{
	    	return response()->json([
                'mensaje'=>'Ticket inválido y/o No registrado!'
            ],401);
	    }
    }

	public function printNotaPedido($nro_pedido, $id_puntoventa){
    	$objImpresion = new NotaPedido();
    	$data = $objImpresion->documento((int)$nro_pedido, (int)$id_puntoventa);
		if(count($data))
		{
			// Check cash
	        $printer = null;
	        // $cajaCentral = CajaCentral::where('nro_caja',trim($idcaja))->first();
	        $cajaLocal = Information::first();

	        // if($cajaLocal->printer && strlen($cajaLocal->printer)>0){
	        $printer = trim($cajaLocal->printer);
	        // }else{
	        //     $cajaLocal->printer = $cajaCentral->impresora;
	        //     $cajaLocal->save();
	        //     $printer = trim($cajaCentral->impresora);
	        // }

	    	$note=new PedidoNotaPrint('EPSON TM', $data);
	        $note->imprimir();
	        $note->cierra();
	        return response()->json([
	            'mensaje'=>'Ticket impreso correctamente!'
	        ],200);
	    }else{
	    	return response()->json([
                'mensaje'=>'Ticket inválido y/o No registrado!'
            ],401);
	    }
	}
}
?>