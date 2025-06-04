<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MOP_T_TRAINER_DAILY_ACTIVITY extends Model
{
    use HasFactory;
    protected $connection = 'MSADMIN';
    protected $table = "MOP_T_TRAINER_DAILY_ACTIVITY";
    protected $fillable = [
        'ID','JDE_NO','EMPLOYEE_NAME','SITE','DATE_ACTIVITY',
        'KPI_TYPE','ACTIVITY','UNIT_DETAIL','TOTAL_PARTICIPANT','TOTAL_HOUR',
        'CREATED_BY','CREATED_AT',
        'UPDATED_BY',
        'UPDATED_AT'
    ];
}
