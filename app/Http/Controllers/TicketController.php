<?php

namespace App\Http\Controllers;
use App\Impresiones\TicketPrint;
use App\Models\Central\{
    CajaCentral,
    DocumentoElectronico
};
use App\Models\Computer\Information;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TicketController extends Controller
{

    public function __construct()
    {
        //
    }

    public function imprimir($tipo,$doc){
        $tipo_doc=intval($tipo);
        $serie= Str::substr($doc,0,4);
        $correlativo=Str::substr($doc,4);
        $documento=DocumentoElectronico::where([
            'cs_tipodocumento_id'=>$tipo_doc,
            'tb_venta_ser'=>$serie,
            'tb_venta_num'=>$correlativo,
            'facturador'=>'CLIPASOFT'
        ])->first();
        if(!$documento){
            $rs = $query=DB::connection('central')->select('exec FacturacionElectronicaFinal_2 ?,?',[
                '0'.$tipo_doc,
                $doc
            ]);
            foreach ($rs[0] as $value){
                $result=$value;
            }
            if($result) {
                $documento=DocumentoElectronico::find($result);
            }else{
                return response()->json([
                    'mensaje'=>'Documento no registrado en tabla de facturación'
                ],401);
            }
        }
        $this->validateDocument($documento);
    }

    public function print($id){
        $documento = DocumentoElectronico::find($id);
        $this->validateDocument($documento);
    }

    private function validateDocument($documento)
    {
        if(!$documento){
            return response()->json([
                'mensaje'=>'Documento no registrado en tabla de facturación'
            ],401);
        }

        if($documento->procedencia==2){
            return response()->json([
                'mensaje'=>'Documento corresponde al Area de Facturación'
            ],401);
        }
        $documento->load('punto_venta');
        $ticket=new TicketPrint('EPSON TM',$documento);
        $n_copias=env('N_COPIAS',2);
        for($i=0; $i<$n_copias;$i++){
            $ticket->imprimir();
        }
        $ticket->cierra();
        return response()->json([
            'mensaje'=>'Documento impreso correctamente'
        ]);
    }

    public function sendPrint($idventa, $idcaja)
    {
        $documento=DocumentoElectronico::where('tb_venta_id',trim($idventa))->first();

        if(!$documento){
            return response()->json([
                'mensaje'=>'Documento no registrado en tabla de facturación'
            ],401);
        }
        if($documento->procedencia==2){
            return response()->json([
                'mensaje'=>'Documento corresponde al Area de Facturación'
            ],401);
        }

        // Check cash
        $printer = null;
        $cajaCentral = CajaCentral::where('nro_caja',trim($idcaja))->first();
        $cajaLocal = Information::where('hostname',trim($cajaCentral->host_name))->first();

        if($cajaLocal->printer){
            $printer = trim($cajaLocal->printer);
        }else{
            $cajaLocal->printer = $cajaCentral->impresora;
            $cajaLocal->save();
            $printer = trim($cajaCentral->impresora);
        }
        try {
            $ticket=new TicketPrint($printer,$documento);
            $ticket->imprimir();
            $ticket->imprimir();
            $ticket->cierra();
            return response()->json([
                'mensaje'=>'Documento impreso correctamente'
            ]);
        }catch (\Exception $e){
            return response()->json([
                'mensaje'=>$e->getMessage()
            ],401);
        }

    }
}
