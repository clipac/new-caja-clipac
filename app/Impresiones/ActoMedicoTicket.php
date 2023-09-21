<?php

namespace App\Impresiones;

use App\Extras\HelperApp;
use App\Impresiones\Interfaces\PrintTicket;
use Carbon\Carbon;
use Mike42\Escpos\Printer;
use Illuminate\Support\Str;

class ActoMedicoTicket extends BaseTicket implements PrintTicket
{
    protected $acto_medico;
    protected $acto_anterior;
    protected $usuario;
    public function __construct($impresora,$acto_medico,$acto_anterior,$usuario)
    {
        $this->acto_medico=$acto_medico;
        $this->acto_anterior=$acto_anterior;
        $this->usuario=$usuario;
        parent::__construct($impresora);
    }

    public function imprimir()
    {
        $this->printHeader();
        $this->printContenido();
        $this->printFooter();
    }

    protected function printHeader(){
        $this->printer->setJustification(Printer::JUSTIFY_CENTER);
        $this->printer->text("CLÍNICA DEL PACÍFICO S.A.\n");
        $this->printer->text("SEDE - ".$this->acto_medico->punto_venta->nomPuntoVenta."\n");
        $this->printer->text($this->acto_medico->punto_venta->Direccion."\n");
        $this->printer->text("LAMBAYEQUE - CHICLAYO - CHICLAYO\n");
        $this->printer->text("RUC: 20103269319\n");
        $this->printer->text("TELÉFONO: ".$this->acto_medico->punto_venta->Telefono."\n");
        $this->printer->text("FECHA DE IMP.: ".Carbon::now()->format('d/m/Y h:i A')."\n");
        $this->printer->text(self::$linea."\n");
    }

    protected function printContenido(){
        $this->printer->setJustification(Printer::JUSTIFY_CENTER);
        $this->printer->setUnderline(Printer::UNDERLINE_DOUBLE);
        $this->printer->text("ORDEN DE ATENCION - " . trim($this->acto_medico->procedencia->procedencia). "\n");
        $this->printer->setUnderline(Printer::UNDERLINE_NONE);
        $this->printer->feed(1);
        $this->printer->setJustification();
        $this->printer->text(Str::padLeft("SERVICIO : ",16," ") . $this->acto_medico->servicio->descrip_suborigen. "\n");
        $this->printer->text(Str::padLeft("N° ACTO : ",17," ") . $this->acto_medico->nro_acto. "\n");
        $hora=$this->acto_medico->hora_crea?$this->acto_medico->hora_crea->format('h:i A'):$this->acto_medico->hara_final->format('h:i A');
        $this->printer->text(Str::padLeft("FEC. REGISTRO : ",16," "). $this->acto_medico->fec_crea->format('d/m/Y').' '.$hora."\n");
        $this->printer->text(self::$linea."\n");
        $this->printer->setJustification(Printer::JUSTIFY_CENTER);
        $this->printer->setUnderline(Printer::UNDERLINE_DOUBLE);
        $this->printer->text("DATOS DEL PACIENTE\n");
        $this->printer->setUnderline(Printer::UNDERLINE_NONE);
        $this->printer->feed(1);
        $this->printer->setJustification();
        $this->printer->text(Str::padLeft("N° HIST. : ",17," ") . $this->acto_medico->nro_historia. "\n");
        $this->printer->text(Str::padLeft("F. NACI. : ",16," ") . $this->acto_medico->paciente->fec_naci->format('d/m/Y'). "\n");
        $this->printer->text(Str::padLeft("EDAD : ",16," ") . HelperApp::getDiffDates($this->acto_medico->paciente->fec_naci,$this->acto_medico->fec_crea). "\n");
        $name_paciente=$this->acto_medico->paciente->full_name;
        if(Str::length($name_paciente)>32){
            $this->printer->text(Str::padLeft("PACIENTE : ",16," ") . Str::limit($name_paciente,29,'...'). "\n");
        }else{
            $this->printer->text(Str::padLeft("PACIENTE : ",16," ") . $name_paciente. "\n");
        }
        if($this->acto_medico->id_tipo_paci=='02'){
            $this->printer->text(self::$linea."\n");
            $this->printer->setJustification(Printer::JUSTIFY_CENTER);
            $this->printer->setUnderline(Printer::UNDERLINE_DOUBLE);
            $this->printer->text("DATOS DEL SEGURO\n");
            $this->printer->setUnderline(Printer::UNDERLINE_NONE);
            $this->printer->feed(1);
            $this->printer->setJustification();
            $nom_aseg=$this->acto_medico->aseguradora->nom_aseg;
            if(Str::length($nom_aseg)>32){
                $this->printer->text(Str::padLeft("ASEGURADORA : ",16," ") .Str::limit( $nom_aseg,29,'...'). "\n");
            }else{
                $this->printer->text(Str::padLeft("ASEGURADORA : ",16," ") . $nom_aseg. "\n");
            }
            $nom_empleadora=$this->acto_medico->empleadora->nom_emp;
            if(Str::length($nom_empleadora)>32){
                $this->printer->text(Str::padLeft("ASEGURADORA : ",16," ") .Str::limit( $nom_empleadora,29,'...'). "\n");
            }else{
                $this->printer->text(Str::padLeft("EMPLEADORA : ",16," ") . $nom_empleadora. "\n");
            }
            $this->printer->text(Str::padLeft("COASEGURO : ",16," ") . floatval($this->acto_medico->coaseguro). "% \n");
            $this->printer->text(Str::padLeft("DEDUCIBLE : ",16," ") . $this->acto_medico->deducible. " \n");
        }
        $this->printer->text(self::$linea."\n");
        $this->printer->setJustification(Printer::JUSTIFY_CENTER);
        $this->printer->setUnderline(Printer::UNDERLINE_DOUBLE);
        $this->printer->text("DATOS DEL DOCTOR\n");
        $this->printer->setUnderline(Printer::UNDERLINE_NONE);
        $this->printer->feed(1);
        $this->printer->setJustification();
        $this->printer->text(Str::padLeft("ESPECIALIDAD : ",16," ") . $this->acto_medico->especialidad->des_especi. "\n");
        $this->printer->text(Str::padLeft("CMP : ",16," ") . $this->acto_medico->doctor->num_cole. "\n");
        $nom_doctor=$this->acto_medico->doctor->nom_doc;
        if(Str::length($nom_doctor)>32){
            $this->printer->text(Str::padLeft("MEDICO : ",16," ") . Str::limit($nom_doctor,29,'...'). "\n");
        }else{
            $this->printer->text(Str::padLeft("MEDICO : ",16," ") . $nom_doctor. "\n");
        }

        if($this->acto_anterior){
            $this->printer->text(self::$linea."\n");
            $this->printer->setJustification(Printer::JUSTIFY_CENTER);
            $this->printer->setUnderline(Printer::UNDERLINE_DOUBLE);
            $this->printer->text("ULTIMA CONSULTA\n");
            $this->printer->setUnderline(Printer::UNDERLINE_NONE);
            $this->printer->feed(1);
            $this->printer->setJustification();
            $this->printer->text(Str::padLeft("N° ACTO : ",17," ") . $this->acto_anterior->especialidad->des_especi. "\n");
            $this->printer->text(Str::padLeft("FECHA : ",16," ") . $this->acto_anterior->fec_crea->format('d/m/Y'). "\n");
            $this->printer->text(Str::padLeft("ESPECIALIDAD : ",16," ") . $this->acto_anterior->especialidad->des_especi. "\n");
            $this->printer->text(Str::padLeft("CMP : ",16," ") . $this->acto_anterior->doctor->num_cole. "\n");
            $nom_doctor_ant=$this->acto_anterior->doctor->nom_doc;
            if(Str::length($nom_doctor_ant)>32){
                $this->printer->text(Str::padLeft("MEDICO : ",16," ") .Str::limit( $nom_doctor_ant,29,'...'). "\n");
            }else{
                $this->printer->text(Str::padLeft("MEDICO : ",16," ") . $nom_doctor_ant. "\n");
            }
        }
    }
    protected function printFooter(){
        $this->printer->text(self::$linea . "\n");
        $this->printer->setJustification(Printer::JUSTIFY_CENTER);
        $this->printer->feed(1);
        $this->printer->text(Str::padLeft("USUARIO : ",20," ") . $this->usuario. "\n");
        $this->printer->feed(2);
        $this->printer->cut();
        $this->printer->pulse();
    }

}
