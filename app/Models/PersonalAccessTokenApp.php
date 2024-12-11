<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;
class PersonalAccessTokenApp extends SanctumPersonalAccessToken
{
    protected $table = 'genapppersonalaccesstokens';

}
