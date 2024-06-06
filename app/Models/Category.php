<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $guarded= ['id'];

    protected $casts = [
        'created_at' => 'datetime:d/m/Y', 
        'updated_at' => 'datetime:d/m/Y',
    ];
}