<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\Client as PassportClient;
use Laravel\Passport\HasApiTokens;

class Client extends PassportClient
{
    use HasApiTokens;
}
