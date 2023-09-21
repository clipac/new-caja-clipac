<?php

namespace App\Impresiones;

use App\Extras\HelperApp;
use App\Impresiones\Interfaces\PrintTicket;
use Carbon\Carbon;
use Mike42\Escpos\Printer;
use Illuminate\Support\Str;

class DocumentoInternoSalidaTicket extends BaseTicket implements PrintTicket
{
    protected $guia;

    public function __construct($impresora, $guia)
    {
        $this->guia = $guia;
        parent::__construct($impresora);
    }

    public function imprimir()
    {
        $this->printHeader();
        $this->printContenido();
        $this->printDetalle();
        $this->printFooter();
    }

    protected function printHeader(){
        $this->printer->setJustification(Printer::JUSTIFY_CENTER);
        $this->printer->text("CLÍNICA DEL PACÍFICO S.A.\n");
        $this->printer->text("SEDE - ".$this->guia->punto_venta->nomPuntoVenta."\n");
        $this->printer->text($this->guia->punto_venta->Direccion."\n");
        $this->printer->text("LAMBAYEQUE - CHICLAYO - CHICLAYO\n");
        $this->printer->text("RUC: 20103269319\n");
        $this->printer->text("TELÉFONO: ".$this->guia->punto_venta->Telefono."\n");
        $this->printer->text("FECHA DE IMP.: ".Carbon::now()->format('d/m/Y h:i A')."\n");
        $this->printer->text(self::$linea."\n");
    }

    protected function printContenido(){
        $this->printer->setJustification(Printer::JUSTIFY_CENTER);
        $this->printer->setUnderline(Printer::UNDERLINE_DOUBLE);
        if($this->guia->nro_doc == 'DS'){
            $this->printer->text("DOCUMENTO INTERNO DE SALIDA \n");
        }elseif($this->guia->nro_doc == 'DI'){
            $this->printer->text("DEVOLUCIÓN DE MEDICAMENTO \n");
        }
        $this->printer->setUnderline(Printer::UNDERLINE_NONE);
        $this->printer->setJustification(Printer::JUSTIFY_CENTER);
        $this->printer->text($this->guia->nro_doc." ".Str::substr($this->guia->nro_guia, 0, 4)." - ".Str::substr($this->guia->nro_guia, 4, 10)."\n");
        $this->printer->setUnderline(Printer::UNDERLINE_NONE);
        $this->printer->setJustification(Printer::JUSTIFY_LEFT);
        $this->printer->text(str_pad('FECHA REG.', 12) . ": " .$this->guia->fecha_crea->format('d/m/Y h:i:s A')."\n");
        $this->printer->text(str_pad('ACTO MEDICO',12,' ').': '.$this->guia->nro_acto."\n");
        $this->printer->text(str_pad('PACIENTE',12,' ').': '.$this->guia->actomedico->paciente->full_name."\n");
        $tipo_paciente = $this->guia->actomedico->id_tipo_paci == '01' ? 'PARTICULAR' : 'ASEGURADO';
        $this->printer->text(str_pad('T. PACIENTE',12,' ').': '.$tipo_paciente."\n");
        if($this->guia->actomedico->id_tipo_paci == '02')$this->printer->text(str_pad('ASEGURADORA',12,' ').': '.$this->guia->actomedico->aseguradora->nom_aseg."\n");
        $this->printer->text(str_pad('COASEGURO',12,' ').': '.$this->guia->actomedico->coaseguro." % \n");
        $this->printer->text(str_pad('MEDICO',12,' ').': '.$this->guia->actomedico->doctor->nom_doc."\n");
        if($this->guia->nro_doc == 'DS'){
            $this->printer->text(str_pad('MOTIVO.',12,' ').': '.$this->guia->name_motivo."\n");
            $this->printer->text(self::$linea."\n");
        }elseif($this->guia->nro_doc == 'DI'){
            $this->printer->text(str_pad('T. DE PROD.',12,' ').': '.$this->guia->tipo_producto."\n");
            $this->printer->text(self::$linea."\n");
        }
    }

    protected function printDetalle(){
        $this->printer->text(str_pad('CANT',6).str_pad('ITEM',24).str_pad('DSCTO(%)',10).str_pad('P. TOTAL',8));
        $this->printer->text(self::$linea . "\n");
        $dscto = $subtotal = $venta = 0;
        // $importe = 0;
        foreach ($this->guia->detalle as $key => $value) {
            if($this->guia->nro_doc == 'DS'){
                $cant = $value->canti_compra;
            }else{
                $cant = $value->can_dev;
            }
            $this->printer->text(str_pad($cant,4,' ',STR_PAD_LEFT).'  ');
            $despro=trim($value->producto->despro);
            if(strlen($despro) > 18){
                $this->printer->text(Str::substr($despro,0,18).'  ');
                $this->printer->setJustification(Printer::JUSTIFY_RIGHT);
                $this->printer->text(str_pad(number_format($value->descuento,2,'.',''),10,' ',STR_PAD_LEFT));
                $this->printer->text(str_pad(number_format($value->total_sin_descto,2,'.',''),10,' ',STR_PAD_LEFT));
                $this->printer->text("\n");
                $lineas=ceil(strlen($despro)/18);
                for($i=1;$i<$lineas;$i++){
                    $maxlong=18*($i+1);
                    if(strlen($despro)>$maxlong){
                        $this->printer->setJustification(Printer::JUSTIFY_LEFT);
                        $this->printer->text(str_repeat(' ',6).trim(Str::substr($despro,(18*$i),18))."\n");
                    }else{
                        $this->printer->setJustification(Printer::JUSTIFY_LEFT);
                        $this->printer->text(str_repeat(' ',6).trim(Str::substr($despro,18*$i)));
                    }
                }
            }else{
                $this->printer->setJustification(Printer::JUSTIFY_LEFT);
                $this->printer->text(str_pad($despro,18).'  ');
                $this->printer->setJustification(Printer::JUSTIFY_RIGHT);
                $this->printer->text(str_pad(number_format($value->descuento,2,'.',''),10,' ',STR_PAD_LEFT));
                $this->printer->text(str_pad(number_format($value->total_sin_descto,2,'.',''),10,' ',STR_PAD_LEFT));
            }
            $this->printer->text("\n");
            if($this->guia->mon_bot_afe > 0){
                $dscto += ($value->total_sin_descto/1.18)* ($value->descuento/100);
            }else{
                $dscto += $value->total_sin_descto * ($value->descuento/100);
            }
            $subtotal += $value->total_sin_descto;
        }
        $this->printer->text(self::$linea . "\n");

        if($this->guia->mon_bot_afe > 0){
            $monto_afe = $subtotal/1.18;
            $monto_ina = 0;
            $venta = $monto_afe - $dscto;
        }else{
            $monto_afe = 0;
            $monto_ina = $subtotal;
            $venta = $monto_ina - $dscto;
        }
        // $importe = $venta + $this->guia->total_igv;
        $this->printer->text(str_pad('AFECTO',10,' ').':'.str_pad(number_format($monto_afe,2,'.',','),37,' ',STR_PAD_LEFT)."\n");
        $this->printer->text(str_pad('INAFECTO',10,' ').':'.str_pad(number_format($monto_ina,2,'.',','),37,' ',STR_PAD_LEFT)."\n");
        $this->printer->text(str_pad('DSCTO',10,' ').':'.str_pad(number_format($dscto,2,'.',','),37,' ',STR_PAD_LEFT)."\n");
        $this->printer->text(str_pad('V. VENTA',10,' ').':'.str_pad(number_format($venta,2,'.',','),37,' ',STR_PAD_LEFT)."\n");
        $this->printer->text(str_pad('IGV',10,' ').':'.str_pad(number_format($this->guia->total_igv,2,'.',','),37,' ',STR_PAD_LEFT)."\n");
        $this->printer->text(str_pad('TOTAL S/.',10,' ').':'.str_pad(number_format($this->guia->importe,2,'.',','),37,' ',STR_PAD_LEFT)."\n");

    }

    protected function printFooter(){
        $this->printer->text(self::$linea . "\n");
        $this->printer->setJustification(Printer::JUSTIFY_CENTER);
        $this->printer->feed(1);
        $this->printer->text(Str::padLeft("USUARIO : ",20," ") . $this->guia->crea_user. "\n");
        $this->printer->feed(2);
        $this->printer->cut();
        $this->printer->pulse();
    }

}
