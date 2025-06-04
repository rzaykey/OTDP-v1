<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MOP_T_HMTRAIN_HOURS extends Model
{
    use HasFactory;
    protected $connection = 'MSADMIN';
    protected $table = "MOP_T_HMTRAIN_HOURS";
    protected $fillable = [
        'ID','JDE_NO','EMPLOYEE_NAME','POSITION','SITE','DATE_ACTIVITY',
        'TRAINING_TYPE','UNIT_CLASS','UNIT_TYPE','CODE','BATCH',
        'PLAN_TOTAL_HM','HM_START','HM_END','TOTAL_HM','PROGRES',
        'CREATED_BY','CREATED_AT',
        'UPDATED_BY',
        'UPDATED_AT'
    ];
}
