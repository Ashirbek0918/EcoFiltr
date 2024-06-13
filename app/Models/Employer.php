<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Employer extends Model
{
    use HasFactory,HasApiTokens;

    protected $guarded= ['id'];

    protected $hidden = [
        'password',
    ];
}
