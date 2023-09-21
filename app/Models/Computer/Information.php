<?php

namespace App\Models\Computer;

use Illuminate\Database\Eloquent\Model;

class Information extends Model
{
    protected $connection = 'local';
    protected $table = 'information';
}
