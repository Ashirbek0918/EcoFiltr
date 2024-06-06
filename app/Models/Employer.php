<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Employer extends Model
{
    use HasFactory,HasApiTokens;

    protected $guarded= ['id'];

    protected $casts = [
        'created_at' => 'datetime:d/m/Y', 
        'updated_at' => 'datetime:d/m/Y',
    ];
    protected $hidden = [
        'password',
    ];
}
