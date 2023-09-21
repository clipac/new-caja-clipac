<?php

namespace App\Impresiones;

use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;

class BaseTicket
{
    protected $printer;
    protected static $linea;
    public function __construct($impresora)
    {
        $conector= new WindowsPrintConnector($impresora);
        $this->printer=new Printer($conector);
        self::$linea=str_repeat('-',48);
    }
    public function cierra(){
        $this->printer->close();
    }
}
