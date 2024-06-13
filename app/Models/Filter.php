<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Filter extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
    public static function getfilters()
    {
        $date = Carbon::now()->startOfDay();
        $filters = static::all();
        $filters->map(function ($filter) use ($date) {
            $changedAt = Carbon::parse($filter->changed_at);
            $expired = $changedAt->copy()->addMonths($filter->expiration_date)->startOfDay();
    
            if ($expired == Carbon::now()->startOfDay()) {
                $filter->update(['status' => 'be_changed']);
            } elseif ($expired->lessThan($date)) {
                $filter->update(['status' => 'expired']);
            } elseif ($expired == Carbon::tomorrow()->startOfDay()) {
                $filter->update(['status' => 'be_changed']);
            }else{
                $filter->update(['status' => 'not_expired']);
            }
        });
        return $filters;
    }
}
