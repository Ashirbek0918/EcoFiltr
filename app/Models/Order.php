<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $guarded= ['id'];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function filters(): HasMany
    {
        return $this->hasMany(Filter::class);
    }
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    
}
