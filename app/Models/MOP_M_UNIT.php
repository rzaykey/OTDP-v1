<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MOP_M_UNIT extends Model
{
    use HasFactory;
    protected $connection = 'MSADMIN';
    protected $table = "MOP_M_UNIT";
    protected $fillable = [
        'ID','NO_UNIT','TYPE','MODEL','MERK','CATEGORY_MENTORING','SITE','CREATED_AT','UPDATED_AT', 'UPDATED_BY',
    ];
}
