<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MOP_M_ACTIVITY extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $connection = 'MSADMIN';
    protected $table = "MOP_M_ACTIVITY";
    protected $fillable = [
        'ID','KPI','ACTIVITY','SITE',
    ];
}
