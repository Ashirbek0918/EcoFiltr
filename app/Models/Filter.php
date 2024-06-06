<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Filter extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d',
        'updated_at' => 'datetime:Y-m-d',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public static function getFiltersByDate($date, $filterType)
    {
        $dateStr = $date->toDateString();
        if ($filterType == 'expired') {
            return self::whereRaw("DATE(changed_at + INTERVAL expiration_date MONTH) < ?", [$dateStr])
                ->whereHas('order', function ($query) {
                    $query->where('status', 'active');
                })
                ->get();
        } else {
            return self::whereRaw("DATE(changed_at + INTERVAL expiration_date MONTH) = ?", [$dateStr])
                ->whereHas('order', function ($query) {
                    $query->where('status', 'active');
                })
                ->get();
        }
    }

}
