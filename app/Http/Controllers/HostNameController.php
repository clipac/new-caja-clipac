<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Computer\{
    Information
};

class HostNameController extends Controller
{
    public function __construct()
    {
        //
    }

    public function getinfo($ocupacional = false)
    {
        $port=request()->server('SERVER_PORT');
        $url_base=request()->getBaseUrl();
        $hostname = gethostname();
        $info=Information::where('ocupacional',$ocupacional ? 1 : 0)->first();
        if(!$info){
            $info= new Information();
            $info->hostname=$hostname;
            if($ocupacional) $info->ocupacional = 1;
            $info->baseurl='http://localhost'.($port=='80'?'':':'.$port).$url_base;
            $info->save();
        }
        return [
            'info'=>[
                'hostname' => gethostname(),
                'baseurl' => $info->baseurl,
                'printers' => $this->printers(),
                'warehouse' => $info->printer_warehouse,
                'nro_caja'=>$info->nro_caja
            ]
        ];
    }

    protected function get_client_ip_server()
    {
        $ruta_powershell = 'C:\Windows\System32\WindowsPowerShell\v1.0\powershell.exe';
        $opciones_para_ejecutar_comando = "-c";
        $espacio = " ";
        $comillas = '"';
        $comando = '((ipconfig | findstr [0-9].\.)[0]).Split()[-1]';
        exec(
            $ruta_powershell
            . $espacio
            . $opciones_para_ejecutar_comando
            . $espacio
            . $comillas
            . $comando
            . $comillas,
            $resultado,
            $codigo_salida
        );
        if ($codigo_salida === 0) {
            return $resultado[0];
        } else {
            return "Error al ejecutar el comando.";
        }
    }

    protected function printers()
    {
        $ruta_powershell = 'c:\Windows\System32\WindowsPowerShell\v1.0\powershell.exe';
        $opciones_para_ejecutar_comando = "-c";
        $espacio = " ";
        $comillas = '"';
        $comando = 'get-WmiObject -class Win32_printer |ft name'; #lista de impresoras
        $impresoras = array(); #impresoras
        exec(
            $ruta_powershell
            . $espacio
            . $opciones_para_ejecutar_comando
            . $espacio
            . $comillas
            . $comando
            . $comillas,
            $resultado,
            $codigo_salida
        );

        if ($codigo_salida === 0)
        {
            if (is_array($resultado)) {
                for($x = 3; $x < count($resultado); $x++){
                    $impresora = trim($resultado[$x]);
                    if (strlen($impresora) > 0) # Ignorar espacios blanco o líneas vacías
                        array_push($impresoras, $impresora);
                }
            }
            return $impresoras;
        } else {
            return "Error al ejecutar el comando.";
        }
    }

    public function asignarCaja(Request $request, $ocupacional = false){
        $info= Information::where('ocupacional',$ocupacional ? 1 : 0)->first();
        if($info){
            if(!$info->nro_caja){
                $info->nro_caja=$request->input('nro_caja');
                $info->printer=$request->input('impresora');
                $info->save();
                return [
                    'result'=>true,
                    'message'=>'Equipo Asignado a Caja',
                ];
            }
            return [
                'result'=>false,
                'message'=>'Equipo ya se encuentra asignado a una caja',
            ];
        }
    }
}
