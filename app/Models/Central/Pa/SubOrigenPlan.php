<?php

namespace App\Models\Central\Pa;

use Illuminate\Database\Eloquent\Model;

class SubOrigenPlan extends Model
{
    protected $connection = 'central';
    protected $table = 'pa_sub_origenes_plan';
    protected $primaryKey = 'codsub_origen';
    protected $keyType = "string";
    public $incrementing=false;
    public $timestamps = false;
}
