<?php

namespace App\Models\Central;

use Illuminate\Database\Eloquent\Model;

class DetalleDocArqueo extends Model
{
    protected $table = 'pa_detalle_doc_arqueo';
    protected $primaryKey = 'nro_secuencia';
    public $timestamps = false;
    protected $dates = ['fecha_gene','creacion','fecha_anula'];

    public function getLabelFormPagoAttribute(){
        $forma_pago=[
            1=>'EF',
            2=>'CH',
            3=>'TJ',
            4=>'OT'
        ];
        return $forma_pago[$this->attributes['forma_pago']];
    }
}
