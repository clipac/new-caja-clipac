<?php

namespace App\Models\Central;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DocumentoElectronico extends Model
{
    protected $connection = 'central';
    protected $table = 'facturacion_electronica';
    protected $primaryKey = 'tb_venta_id';
    public $timestamps=false;
    protected $dateFormat = 'd/m/Y H:i:s';
    protected $dates=['tb_venta_reg','tb_venta_fec','tb_venta_fecenvsun','fec_vencimiento'];

    public function items(){
        return $this->hasMany('App\Models\Central\DetalleDocElectronico','fe_id','tb_venta_id');
    }

    public function punto_venta(){
        return $this->belongsTo('App\Models\Central\PuntoVenta','puntoventa_id','IdPuntoVenta');
    }

    public function getNomTipoDocSunatAttribute(): string
    {
        $nomTipoDocumento=[
            1=>'FACTURA ELECTRONICA',
            3=>'BOLETA DE VENTA ELECTRONICA',
            7=>'NOTA DE CREDITO'
        ];
        return $nomTipoDocumento[$this->attributes['cs_tipodocumento_id']];
    }
    public function getNomTipoDocAfectoAttribute(): string
    {
        return $this->attributes['tb_notacredeb_tipdoc']==1?'FACTURA':'BOLETA';
    }
    public function getFormaPagoAttribute(){
        return [
            0=>'CONTADO',
            1=>'CREDITO'
        ][$this->attributes['es_credito']];
    }

    public function getDocClienteSunatAttribute(): string
    {
        $docClienteSunat=[
            0=>'S/N',
            1=>'DNI',
            4=>'CARNET EXT.',
            6=>'RUC',
            7=>'PASAPORTE',
            11=>'P. NAC.'
        ];
        return $docClienteSunat[$this->attributes['tb_cliente_tip']];
    }
    public function getExtrasAttribute(): array
    {
        if($this->attributes['cs_tipodocumento_id']==7){
            $nota=DB::connection('central')
                ->table('FACT_Nota_Contable')
                ->select('FACT_Nota_Contable.*','pa_acto_medico.nro_historia','pa_acto_medico.coaseguro','pa_aseguradoras.nom_aseg','pa_aseguradoras.dir_aseg')
                ->leftJoin('pa_acto_medico',function($q){
                    $q->on('FACT_Nota_Contable.acto_medico','=','pa_acto_medico.nro_acto')
                        ->where('pa_acto_medico.nro_acto','<>',0);
                })
                ->leftJoin('pa_aseguradoras',function($q){
                    $q->on('pa_acto_medico.cod_aseg','=','pa_aseguradoras.cod_aseg')
                        ->where('pa_acto_medico.id_tipo_paci','=','02');
                })
                ->where('nro_documento',$this->attributes['tb_venta_ser'].$this->attributes['tb_venta_num'])
                ->first();
            if($nota){
                $mostrar_paciente=trim($nota->nom_paciente)=='VARIOS'?0:1;
                return [
                    'caja'=>$nota->nro_caja,
                    'dni_cajero' => trim($nota->nro_dni),
                    'nom_cajero'=>trim($nota->crea_user),
                    'nro_acto'=>($nota->acto_medico==0?'S/N':$nota->acto_medico),
                    'mostrar_paciente'=>$mostrar_paciente,
                    'nro_historia'=>$nota->nro_historia,
                    'pedido'=>0,
                    'redondeo'=>0,
                    'coaseguro'=>$nota->coaseguro,
                    'aseguradora'=>$nota->nom_aseg,
                    'dir_asegu'=>$nota->dir_aseg
                ];
            }
            return [];
        }
        switch ($this->attributes['procedencia']){
            case 1:
                $doc_aseg=DB::connection('central')
                    ->table('pa_doc_asegurados')
                    ->select('pa_doc_asegurados.*','pa_acto_medico.coaseguro','pa_pacientes.ape_paterno', 'pa_pacientes.ape_materno', 'pa_pacientes.nom_paciente', 'pa_aseguradoras.nom_aseg','pa_aseguradoras.dir_aseg')
                    ->leftJoin('pa_acto_medico',function($q){
                        $q->on('pa_doc_asegurados.nro_actomedico','=','pa_acto_medico.nro_acto')
                            ->where('pa_acto_medico.nro_acto','<>',0);
                    })
                    ->leftJoin('pa_aseguradoras',function($q){
                        $q->on('pa_acto_medico.cod_aseg','=','pa_aseguradoras.cod_aseg')
                            ->where('pa_acto_medico.id_tipo_paci','=','02');
                    })
                    ->leftJoin('pa_pacientes','pa_acto_medico.nro_historia','=','pa_pacientes.nro_historia')
                    ->where('num_doc',$this->attributes['tb_venta_ser'].$this->attributes['tb_venta_num'])
                    ->first();
                if($doc_aseg){

                    return [
                        'caja'=>$doc_aseg->nro_caja,
                        'dni_cajero' => trim($doc_aseg->nro_dni),
                        'nom_cajero'=>trim($doc_aseg->crea_user),
                        'nro_acto'=>($doc_aseg->nro_actomedico==0?'S/N':$doc_aseg->nro_actomedico),
                        'mostrar_paciente'=>$doc_aseg->mostrar_paciente,
                        'nro_historia'=>$doc_aseg->nro_historia,
                        'pedido'=>0,
                        'redondeo'=>$doc_aseg->redondeo,
                        'coaseguro'=>$doc_aseg->coaseguro,
                        'aseguradora'=>$doc_aseg->nom_aseg,
                        'dir_asegu'=>$doc_aseg->dir_aseg
                    ];
                }
                break;
            case 3:
                $doc_farma=DB::connection('central')
                    ->table('fa_documentos_farma')
                    ->select('fa_documentos_farma.*','pa_acto_medico.coaseguro','pa_pacientes.ape_paterno', 'pa_pacientes.ape_materno','pa_pacientes.nom_paciente', 'pa_aseguradoras.nom_aseg','pa_aseguradoras.dir_aseg')
                    ->leftJoin('pa_pacientes','fa_documentos_farma.nro_historia','=','pa_pacientes.nro_historia')
                    ->leftJoin('pa_acto_medico',function($q){
                        $q->on('fa_documentos_farma.nro_acto','=','pa_acto_medico.nro_acto')
                            ->where('pa_acto_medico.nro_acto','<>',0);
                    })
                    ->leftJoin('pa_aseguradoras',function($q){
                        $q->on('pa_acto_medico.cod_aseg','=','pa_aseguradoras.cod_aseg')
                            ->where('pa_acto_medico.id_tipo_paci','=','02');
                    })
                    ->where('nro_documento',$this->attributes['tb_venta_ser'].$this->attributes['tb_venta_num'])
                    ->first();
                if($doc_farma){
                    return [
                        'caja'=>$doc_farma->nro_caja,
                        'dni_cajero' => trim($doc_farma->nro_dni),
                        'nom_cajero'=>trim($doc_farma->crea_user),
                        'nro_acto'=>($doc_farma->nro_acto==0?'S/N':$doc_farma->nro_acto),
                        'mostrar_paciente'=>$doc_farma->mostrar_paciente,
                        'nro_historia'=>$doc_farma->nro_historia,
                        'pedido'=>$doc_farma->nro_secuencia,
                        'redondeo'=>$doc_farma->redondeado,
                        'coaseguro'=>$doc_farma->coaseguro,
                        'aseguradora'=>$doc_farma->nom_aseg,
                        'dir_asegu'=>$doc_farma->dir_aseg
                    ];
                }
                break;
        }
        return [];
    }


}
