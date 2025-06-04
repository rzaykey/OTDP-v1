<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MOP_M_MODEL_UNIT extends Model
{
    use HasFactory;
    protected $connection = 'MSADMIN';
    protected $table = "MOP_M_MODEL_UNIT";
    protected $fillable = [
        'ID','MODEL','TYPE','CREATED_AT','UPDATED_AT'
    ];
}
