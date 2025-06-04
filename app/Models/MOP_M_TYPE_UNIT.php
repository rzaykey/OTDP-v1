<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MOP_M_TYPE_UNIT extends Model
{
    use HasFactory;
    protected $connection = 'MSADMIN';
    protected $table = "MOP_M_TYPE_UNIT";
    protected $fillable = [
        'ID','TYPE','CLASS','CREATED_AT','UPDATED_AT'
    ];
}
