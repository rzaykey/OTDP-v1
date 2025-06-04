<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MOP_T_MENTORING_HEADER extends Model
{
    use HasFactory;
    protected $connection = 'MSADMIN';
    protected $table = "MOP_T_MENTORING_HEADER";
    protected $fillable = [
        'ID',
        'TYPE_MENTORING',
        'TRAINER_JDE',
        'TRAINER_NAME',
        'OPERATOR_JDE',
        'OPERATOR_NAME',
        'SITE',
        'AREA',
        'UNIT_TYPE',
        'UNIT_MODEL',
        'UNIT_NUMBER',
        'DATE_MENTORING',
        'START_TIME',
        'END_TIME',
        'AVERAGE_YSCORE_OBSERVATION',
        'AVERAGE_POINT_OBSERVATION',
        'AVERAGE_YSCORE_MENTORING',
        'AVERAGE_POINT_MENTORING',
        'SCORE1_PRODUCTIVITY',
        'SCORE1_SAFETY_AWARNESS',
        'SCORE1_MACHINE_HEALTH',
        'SCORE1_FULL_EFFICIENT',
        'SCORE2_PRODUCTIVITY',
        'SCORE2_SAFETY_AWARNESS',
        'SCORE2_MACHINE_HEALTH',
        'SCORE2_FULL_EFFICIENT',
        'CREATED_AT',
        'CREATED_BY',
        'UPDATED_AT',
        'UPDATED_BY',
    ];
}
