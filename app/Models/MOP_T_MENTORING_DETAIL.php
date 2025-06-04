<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MOP_T_MENTORING_DETAIL extends Model
{
    use HasFactory;
    protected $connection = 'MSADMIN';
    protected $table = "MOP_T_MENTORING_DETAIL";
    protected $fillable = [
      'ID','FID_MENTORING','FID_INDICATOR','IS_OBSERVASI',
      'IS_MENTORING','NOTE_OBSERVASI', 'NOTE_MENTORING',
	  'CREATED_AT', 'CREATED_BY','UPDATED_AT','UPDATED_BY',
    ];
}


