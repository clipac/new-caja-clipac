<?php

namespace App\Impresiones;

use App\Models\Central\CajaReingresoFondo;
use Carbon\Carbon;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use Illuminate\Support\Str;

class CierreCajaPrint
{
    public $ticket_data;
    protected $printer;
    protected static $linea;

    public function __construct($impresora,$ticket)
    {
        $this->ticket_data=$ticket;
        $conector= new WindowsPrintConnector($impresora);
        $this->printer=new Printer($conector);
        self::$linea=str_repeat('-',48);
    }
    public function imprimir(){
        $this->printHeader();
        $this->printContenido();
        /*
        $this->printDetalle();
        $this->printResumen();
        $this->printQr();*/
        $this->printFooter();
    }

    private function printHeader()
    {
        $this->printer->setJustification(Printer::JUSTIFY_CENTER);
        $this->printer->text("CLÍNICA DEL PACÍFICO S.A.\n");
        $this->printer->text("AV. JOSÉ LEONARDO ORTIZ N° 420\n");
        $this->printer->text("LAMBAYEQUE - CHICLAYO - CHICLAYO\n");
        $this->printer->text("RUC : 20103269319\n");
        $this->printer->text(self::$linea."\n");
    }

    private function printContenido()
    {
        $this->printer->setJustification(Printer::JUSTIFY_LEFT);
        $this->printer->text(Str::padLeft("N° CAJA",15).' : '.$this->ticket_data->nro_caja."\n");
        $this->printer->text(Str::padLeft("CODIGO",15).' : '.$this->ticket_data->codigo_ingreso."\n");
        $this->printer->text(Str::padLeft("N° DNI",15).' : '.$this->ticket_data->nro_dni."\n");
        $this->printer->text(Str::padLeft("CAJERO",15).' : '.$this->ticket_data->cajero."\n");
        $this->printer->text(Str::padLeft("FECHA INGRESO",15).' : '.
            Carbon::parse($this->ticket_data->fecha_ingreso)->format('d/m/Y')."\n");
        $this->printer->text(Str::padLeft("HORA INGRESO",15).' : '.
            Carbon::parse($this->ticket_data->hora_ingreso)->format('h:i A')."\n");
        $this->printer->text(Str::padLeft("FECHA CIERRE",15).' : '.
            Carbon::parse($this->ticket_data->fecha_cierre)->format('d/m/Y')."\n");
        $this->printer->text(Str::padLeft("HORA CIERRE",15).' : '.
            Carbon::parse($this->ticket_data->hora_cierre)->format('h:i A')."\n");
        $this->printer->text(self::$linea."\n");
        $efectivo=$this->ticket_data->pre_efectivo-$this->ticket_data->egresos-$this->ticket_data->vales;
        $this->printer->text(Str::padRight(" - EFECTIVO",20).' : '.Str::padLeft(
            number_format($efectivo,2,'.',''),15,' ')."\n");
        $this->printer->text(Str::padRight(" - VOUCHER",20).' : '.
            Str::padLeft(number_format($this->ticket_data->voucher,2,'.',''),15,' ')."\n");
        $this->printer->text(Str::padRight(" - EGRESOS",20).' : '.
            Str::padLeft(
                number_format($this->ticket_data->vales,2,'.',''),15,' ')."\n");
        $garantia_aplicada=$this->ticket_data->garantia_total-$this->ticket_data->saldo_favor;
        $this->printer->text(Str::padRight(" - GARANTIA APLICADA",20).' : '.Str::padLeft(
                number_format($garantia_aplicada,2,'.',''),15,' ')."\n");
        $this->printer->text(Str::padRight(" - GARANTIA A FAVOR",20).' : '.Str::padLeft(
                number_format($this->ticket_data->saldo_favor,2,'.',''),15,' ')."\n");
        $total=$this->ticket_data->pre_efectivo-$this->ticket_data->egresos+$this->ticket_data->voucher+$garantia_aplicada+$this->ticket_data->saldo_favor;
        $this->printer->text(self::$linea."\n");
        $this->printer->text(Str::padRight("TOTAL",20).' : '.Str::padLeft(
                number_format($total,2,'.',''),15,' ')."\n");
        $this->printer->feed(1);
        $this->printer->text(Str::padRight("(*) NOTAS DE CREDITO",20).' : '.Str::padLeft(
                number_format($this->ticket_data->egresos,2,'.',''),15,' ')."\n");
    }


    private function printFooter()
    {
        $this->printer->feed(7);
        $this->printer->text(self::$linea."\n");
        $this->printer->setJustification(Printer::JUSTIFY_CENTER);
        $this->printer->text("SELLO Y FIRMA\n");
        $this->printer->feed(5);
        $this->printer->cut();
        $this->printer->pulse();
    }

    public function cierra(){
        $this->printer->close();
    }


}
