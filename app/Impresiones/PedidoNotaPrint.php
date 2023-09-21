<?php

namespace App\Impresiones;

use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use Illuminate\Support\Str;


class PedidoNotaPrint
{
	public $data;
    protected $printer;
    protected static $linea;

    public function __construct($impresora, $data)
    {
        $this->data = $data;
        $this->nro_secuencia = trim($data[0]->nro_secuencia);
        $this->paciente = trim($data[0]->paciente);
        $this->razon_social = trim($data[0]->empresa_referencial);
        $this->ruc = trim($data[0]->ruc_empresa);
        $this->direccion = trim($data[0]->dir_referencial);
        $this->nro_acto = trim($data[0]->nro_acto);
        $this->condicion_pago = ($data[0]->condicion_pago == 0) ? 'CONTADO':'CRÉDITO';
        $this->tipo_doc = trim($data[0]->tipodoc_genera);

        $this->fecha_crea = $data[0]->fecha_crea;
        $this->crea_user  = $data[0]->crea_user;

        $conector= new WindowsPrintConnector($impresora);
        $this->printer=new Printer($conector);
        self::$linea=str_repeat('-',42);
    }

    public function imprimir(){
        $this->printHeader();
        $this->printContenido();
        $this->printDetalle();
        $this->printFooter();
    }

    protected function printHeader()
    {
        $this->printer->setJustification(Printer::JUSTIFY_CENTER);
        $this->printer->setTextSize(1, 1);
        $this->printer->text("*** NOTA DE PEDIDO ***");
        $this->printer->text("\n");
        $this->printer->text("\n");
        $this->printer->setJustification(Printer::JUSTIFY_LEFT);
        $this->printer->text(str_pad('N° PEDIDO', 12, ' ').": ".$this->nro_secuencia."\n");
        $this->printer->text(self::$linea."\n");
    }

    protected function printContenido()
    {
        if(strlen($this->paciente) > 28) {
            $this->printer->text(str_pad("PACIENTE", 11, ' ').": ".Str::substr($this->paciente, 0, 28)."\n");
            $lineas=ceil(strlen($this->paciente)/28);
            for($i=1;$i<$lineas;$i++){
                $maxlong=28*($i+1);
                if(strlen($this->paciente)>$maxlong){
                    $this->printer->text(str_repeat(' ',13).trim(Str::substr($this->paciente,(28*$i),28))."\n");
                }else{
                    $this->printer->text(str_repeat(' ',13).trim(Str::substr($this->paciente,28*$i))."\n");
                }
            }
        }else {
            $this->printer->text(str_pad("PACIENTE", 11, ' ').": ".$this->paciente."\n");
        }
    
        if(strlen($this->razon_social) > 28) {
            $this->printer->text(str_pad("RAZÓN S.", 12, ' ').": ".Str::substr($this->razon_social, 0, 28)."\n");
            $lineas=ceil(strlen($this->razon_social)/28);
            for($i=1;$i<$lineas;$i++){
                $maxlong=28*($i+1);
                if(strlen($this->razon_social)>$maxlong){
                    $this->printer->text(str_repeat(' ',13).trim(Str::substr($this->razon_social,(28*$i),28))."\n");
                }else{
                    $this->printer->text(str_repeat(' ',13).trim(Str::substr($this->razon_social,28*$i))."\n");
                }
            }
        }else {
            $this->printer->text(str_pad("RAZÓN  S.", 12, ' ').": ".$this->razon_social."\n");
        }
    
        $this->printer->text(str_pad('R.U.C.', 11, ' ').": ".$this->ruc."\n");
    
        if(strlen($this->direccion) > 28) {
            $this->printer->text(str_pad("DIRECCIÓN", 12, ' ').": ".Str::substr($this->direccion, 0, 28)."\n");
            $lineas=ceil(strlen($this->direccion)/28);
            for($i=1;$i<$lineas;$i++){
                $maxlong=28*($i+1);
                if(strlen($this->direccion)>$maxlong){
                    $this->printer->text(str_repeat(' ',13).trim(Str::substr($this->direccion,(28*$i),28))."\n");
                }else{
                    $this->printer->text(str_repeat(' ',13).trim(Str::substr($this->direccion,28*$i))."\n");
                }
            }
        }else {
            $this->printer->text(str_pad("RAZÓN  S.", 12, ' ').": ".$this->direccion."\n");
        }
    
        $this->printer->text(str_pad('A. MÉDICO', 12, ' ').": ".$this->nro_acto."\n");
    
        $this->printer->text(str_pad('FECHA', 11, ' ').": ".$this->fecha_crea."\n");
    
        $this->printer->text("CONDICIÓN DE PAGO: ".$this->condicion_pago);
        $this->printer->text("\n");
        $this->printer->text("DOC.GENERAR: ".$this->tipo_doc);
        $this->printer->text("\n");
        $this->printer->text(str_pad('USUARIO', 11, ' ').": ".$this->crea_user."\n");
        $this->printer->text(self::$linea."\n");
    }

    protected function printDetalle()
    {
        $this->printer->text(str_pad('CANT.',6,' ').str_pad('UNID.',6,' ').str_pad('DESCRIPCION',30,' ')."\n");
        $this->printer->text(self::$linea."\n");
        $contador = 0;
        $laboratorio = "";
        foreach($this->data as $index => $item) {
            if($index == 0) {
                $laboratorio = trim($item->deslab);
                $this->printer->text(str_repeat(' ',12).$laboratorio."\n");
            }else {
                if($laboratorio != trim($item->deslab)) {
                    $laboratorio = trim($item->deslab);
                    $this->printer->text("\n");
                    $this->printer->text(str_repeat(' ',12).$laboratorio."\n");
                }
            }
            $detalle = trim($item->despro);
            $this->printer->text(str_pad($item->cant_pedido,4,' ',STR_PAD_LEFT)." ");
            $this->printer->text(str_pad(trim($item->cod_unidad),4,' ',STR_PAD_LEFT)."   ");
            if(strlen($detalle) > 30) {
                $this->printer->text(Str::substr($detalle, 0, 30).' ');
                $lineas=ceil(strlen($detalle)/30);
                for($i=1;$i<$lineas;$i++){
                    $maxlong=30*($i+1);
                    if(strlen($detalle)>$maxlong){
                        $this->printer->text(str_repeat(' ',15).trim(Str::substr($detalle,(30*$i),30))."\n");
                    }else{
                        $this->printer->text(str_repeat(' ',17).trim(Str::substr($detalle,30*$i))."\n");
                    }
                }
            }else {
                $this->printer->text(str_pad($detalle, 32, ' ').' ');
            }
            $this->printer->text("\n");
            $this->printer->pulse();
        }
    }

    protected function printFooter() {
        $this->printer->feed(2);
        $this->printer->cut();
        $this->printer->pulse();
    }

    public function cierra() {
        $this->printer->close();
    }
}
?>