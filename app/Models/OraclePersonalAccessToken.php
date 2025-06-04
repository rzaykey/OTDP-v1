<?php

namespace App\Models;

use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

class OraclePersonalAccessToken extends SanctumPersonalAccessToken
{
    protected $connection = 'MSADMIN'; // koneksi Oracle kamu
    protected $table = 'personal_access_tokens'; // sesuaikan kalau tabelnya beda
}
