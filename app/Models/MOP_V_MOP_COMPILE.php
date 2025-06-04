<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MOP_V_MOP_COMPILE extends Model
{
    use HasFactory;
    protected $connection = 'MSADMIN';
    protected $table = "V_OTPD_MOP_COMPILE";

}
