<?php

namespace App\Models\Central\Fa;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $connection = 'central';
    protected $table = "fa_producto";
    protected $primaryKey = "codpro";
    protected $keyType = "string";
    public $incrementing = false;
}