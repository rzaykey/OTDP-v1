<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MOP_T_MENTORING_PENILAIAN extends Model
{
    use HasFactory;
    protected $connection = 'MSADMIN';
    protected $table = "MOP_T_MENTORING_PENILAIAN";
    protected $fillable = [
        'ID','FID_MENTORING','INDICATOR','TYPE_PENILAIAN','YSCORE','POINT',
        'CREATED_AT', 'CREATED_BY', 'UPDATED_AT', 'UPDATED_BY',
    ];
}


