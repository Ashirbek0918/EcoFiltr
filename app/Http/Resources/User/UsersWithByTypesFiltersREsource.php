<?php

namespace App\Http\Resources\User;

use Carbon\Carbon;
use App\Models\Filter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Order\OrdersWithByTypesFiltersREsource;

class UsersWithByTypesFiltersREsource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
   
    public function toArray(Request $request): array
    {
        $type= $request->route('filterType');
        $date = ($type == 'today') ? Carbon::now() : Carbon::tomorrow();
        $dateStr = $date->toDateString();
        $filteredOrdersQuery = $this->orders()
            ->whereHas('filters', function ($query) use ($dateStr, $type) {
                if ($type == 'expired') {
                    $query->whereRaw("DATE(changed_at + INTERVAL expiration_date MONTH) < ?", [$dateStr]);
                } else {
                    $query->whereRaw("DATE(changed_at + INTERVAL expiration_date MONTH) = ?", [$dateStr]);
                }
            })
            ->whereHas('filters.order', function ($query) {
                $query->where('status', 'active');
            });
        $filteredOrders = $filteredOrdersQuery->get();
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'address' => $this->address,
            'created_at' => $this->created_at->format('Y-m-d'),
            'orders' => OrdersWithByTypesFiltersREsource::collection($filteredOrders)
        ];
    }
}
