<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MOP_M_KPI extends Model
{
    use HasFactory;
    protected $connection = 'MSADMIN';
    protected $table = "MOP_M_KPI";
    protected $fillable = [
        'ID',
        'KPI',
    ];
}
