<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PROINT_EMPLOYEE extends Model
{
    use HasFactory;
    protected $connection = 'sqlsrv';
    protected $table = "V_EMPALL";
}
