<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MASTER_SITE extends Model
{
    use HasFactory;
    protected $connection = 'MSADMIN';
    protected $table = "MASTER_SITE";
    protected $fillable = [
        'ID',
        'CODE_SITE',
        'NAME_SITE',
        'ACTIVE',
    ];
}
