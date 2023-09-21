<?php

namespace App\Impresiones;

use App\Impresiones\Interfaces\PrintTicket;
use Mike42\Escpos\Printer;
use Illuminate\Support\Str;

class TicketPrint extends BaseTicket implements PrintTicket
{
    public $documento;
    public function __construct($impresora,$documento)
    {
        $this->documento=$documento;
        parent::__construct($impresora);
    }
    public function imprimir(){
        $this->printHeader();
        $this->printContenido();
        $this->printDetalle();
        $this->printResumen();
        $this->printQr();
        $this->printFooter();
    }
    protected function printHeader(){
        $this->printer->setJustification(Printer::JUSTIFY_CENTER);
        $this->printer->text("CLÍNICA DEL PACÍFICO S.A.\n");
        $this->printer->text("SEDE - ".$this->documento->punto_venta->nomPuntoVenta."\n");
        $this->printer->text($this->documento->punto_venta->Direccion."\n");
        $this->printer->text("LAMBAYEQUE - CHICLAYO - CHICLAYO\n");
        $this->printer->text("RUC: 20103269319\n");
        $this->printer->text("TELÉFONO: ".$this->documento->punto_venta->Telefono."\n");
        $this->printer->text(self::$linea."\n");
    }
    protected function printContenido()
    {
        $extras = $this->documento->extras;
        $this->printer->text($this->documento->nom_tipo_doc_sunat . "\n");
        $this->printer->text($this->documento->tb_venta_ser . '-' . $this->documento->tb_venta_num . "\n");
        $this->printer->text(self::$linea . "\n");
        $this->printer->setJustification();
        $this->printer->text(str_pad('CAJA', 8) . ": " .
            // $extras['caja'] . " - " . $extras['nom_cajero'] . " - " . $extras['dni_cajero'] . "\n");
            $extras['caja'] . " - " . $extras['nom_cajero'] ."\n");
        $this->printer->text(str_pad('FECHA', 8) . ": " .
            str_pad($this->documento->tb_venta_reg->format('d/m/Y'), 15) . "HORA    : " .
            $this->documento->tb_venta_reg->format('h:i:s A') . "\n");
        if($this->documento->cs_tipodocumento_id == 7){
            $this->printer->text("AFECTO  : ".$this->documento->nom_tipo_doc_afecto.' '.
                $this->documento->tb_notacredeb_numdoc."\n");
        }
        $this->printer->text("CLIENTE : ".Str::substr($this->documento->tb_cliente_nom,0,38)."\n");
        $this->printer->text("TIPO DOC: ".
            str_pad($this->documento->doc_cliente_sunat,15) .
            'N° DOC  : '.$this->documento->tb_cliente_doc."\n");
        $this->printer->text("DIR.    : ".Str::substr($this->documento->tb_cliente_dir,0,38)."\n");
        $pago_documento="F. PAGO : ".str_pad($this->documento->forma_pago,16,' ');
        if($this->documento->es_credito){
            $fecha_vence=$this->documento->tb_venta_reg->addDays($this->documento->credito_dias);
            $pago_documento.=' VENCE : '.$fecha_vence->format('d/m/Y');
        }
        $this->printer->text($pago_documento."\n");
        if($extras['mostrar_paciente']){
            $this->printer->text(self::$linea."\n");
            $info_acto=str_pad('AM',8).": ".str_pad($extras['nro_acto'],15);
            $info_acto.="HISTORIA: ".$extras['nro_historia'];
            $this->printer->text($info_acto."\n");
            $nompaciente=$this->documento->tb_paciente_nom;
            if(strlen($nompaciente)>38){
                $this->printer->text("PACIENTE: ".Str::substr($nompaciente,0,38)."\n");
            }else{
                $this->printer->text("PACIENTE: ".$nompaciente."\n");
            }
        }elseif($extras['nro_acto']!='S/N'){
            $this->printer->text(str_pad('AM',8).": ".str_pad($extras['nro_acto'],15)."\n");
        }
        if(floatval($extras['coaseguro'])>0){
            $this->printer->text(self::$linea."\n");
            $this->printer->setJustification(Printer::JUSTIFY_CENTER);
            $this->printer->text(Str::substr($extras['aseguradora'],0,48)."\n");
            $this->printer->text(Str::substr($extras['dir_asegu'],0,48)."\n");
            $this->printer->setJustification();
        }
        if($extras['pedido'] > 0) {
            $this->printer->text(str_pad('PEDIDO',8).": ".$extras['pedido']."\n");
        }
    }
    protected function printDetalle(){
        $this->printer->text(self::$linea . "\n");
        $this->printer->text(str_pad('CANT',6).
            str_pad('ITEM',32).
            str_pad('SUBTOTAL',10)."\n");
        $this->printer->text(self::$linea . "\n");
        foreach ($this->documento->items as $item){
            $this->printer->text(str_pad($item->tb_ventadetalle_can,4,' ',STR_PAD_LEFT).'  ');
            $detalle=trim($item->tb_ventadetalle_nom);
            if($item->cs_tipoafectacionigv_id==30){
                $subtotal=$item->tb_ventadetalle_valven;
            }else{
                if($item->tb_ventadetalle_preuni>0){
                    $subtotal=$item->tb_ventadetalle_can*$item->tb_ventadetalle_preuni;
                }else{
                    $subtotal=$item->tb_ventadetalle_valven+$item->tb_ventadetalle_igv;
                }
            }
            if(strlen($detalle)>30){
                $this->printer->text(Str::substr($detalle,0,30).'  ');
                $this->printer->text(str_pad(number_format($subtotal,2,'.',','),10,' ',STR_PAD_LEFT));
                $this->printer->text("\n");
                $lineas=ceil(strlen($detalle)/30);
                for($i=1;$i<$lineas;$i++){
                    $maxlong=30*($i+1);
                    if(strlen($detalle)>$maxlong){
                        $this->printer->text(str_repeat(' ',6).trim(Str::substr($detalle,(30*$i),30))."\n");
                    }else{
                        $this->printer->text(str_repeat(' ',6).trim(Str::substr($detalle,30*$i)));
                    }
                }

            }else{
                $this->printer->text(str_pad($detalle,30).'  ');
                $this->printer->text(str_pad(number_format($subtotal,2,'.',','),10,' ',STR_PAD_LEFT));
            }
            $this->printer->text("\n");
        }
    }

    protected function printResumen(){
        $extras = $this->documento->extras;
        $this->printer->text(self::$linea . "\n");
        if($extras['redondeo']){
            $this->printer->text(str_pad('REDONDEO',15,' ').':'.
                str_pad(number_format($extras['redondeo'],2,'.',','),
                    32,' ',STR_PAD_LEFT)."\n");
        }
        if(floatval($extras['coaseguro'])>0){
            $this->printer->text(str_pad('COASEGURO',15).':'.
                str_pad(number_format($extras['coaseguro'],0,'.',',').'%',
                    32,' ',STR_PAD_LEFT)."\n");
        }
        $this->printer->text(str_pad('OP. GRAVADAS',15,' ').':'.
            str_pad(number_format($this->documento->tb_venta_gra,2,'.',','),
                32,' ',STR_PAD_LEFT)."\n");
        if(floatval($this->documento->tb_venta_ina)>0){
            $this->printer->text(str_pad('OP. INAFECTAS',15,' ').':'.
                str_pad(number_format($this->documento->tb_venta_ina,2,'.',','),
                    32,' ',STR_PAD_LEFT)."\n");
        }
        $this->printer->text(str_pad('IGV',15,' ').':'.
            str_pad(number_format($this->documento->tb_venta_igv,2,'.',','),
                32,' ',STR_PAD_LEFT)."\n");
        if(floatval($this->documento->monto_icbper)>0){
            $this->printer->text(str_pad('ICBPER',15,' ').':'.
                str_pad(number_format($this->documento->monto_icbper,2,'.',','),
                    32,' ',STR_PAD_LEFT)."\n");
        }
        $this->printer->text(str_pad('TOTAL S/',15,' ').':'.
            str_pad(number_format($this->documento->tb_venta_tot,2,'.',','),
                32,' ',STR_PAD_LEFT)."\n");
        $this->printer->feed(1);
    }
    protected function printQr(){
        $dataqr = "20103269319|".str_pad($this->documento->cs_tipodocumento_id,2,'0',STR_PAD_LEFT).
            "|".$this->documento->tb_venta_ser."|".$this->documento->tb_venta_num.
            "|".number_format($this->documento->tb_venta_igv,2,'.','').
            "|".number_format($this->documento->tb_venta_tot,2,'.','').
            "|".$this->documento->tb_venta_reg->format('Y-m-d').
            "|".$this->documento->tb_cliente_tip."|".($this->documento->tb_cliente_doc??'00000000')."|".
            $this->documento->tb_venta_digval;
        $this->printer->setJustification(Printer::JUSTIFY_CENTER);
        $this->printer->qrCode($dataqr, Printer::QR_ECLEVEL_L, 6);
        $this->printer->text($this->documento->tb_venta_digval."\n");
        $this->printer->text("\n\n");
    }
    protected function printFooter(){
        $this->printer->text(self::$linea . "\n");
        $this->printer->setJustification(Printer::JUSTIFY_CENTER);
        $this->printer->text("Autorizado mediante resolución\n");
        $this->printer->text("N° 072-005-0000068/SUNAT\n");
        $this->printer->text("Consulte su documento en nuestra página\n www.clinicadelpacifico.com.pe/ecomprobantes");
        $this->printer->feed(2);
        $this->printer->text('"No se permite canjear este comprobante por otro tipo de documento electrónico después de haber sido emitido"');
        $this->printer->text("\n");

        $this->printer->feed(3);
        $this->printer->cut();
        $this->printer->pulse();
    }

}
