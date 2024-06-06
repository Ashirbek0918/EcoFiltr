<?php

namespace App\Http\Resources\Order;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Filter\ByTpesFiltersResource;

class OrdersWithByTypesFiltersREsource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $type = $request->route('filterType');
        $date = ($type == 'today') ? Carbon::now() : Carbon::tomorrow();
        $filters = $this->filters()->where(function ($query) use ($date, $type) {
            $dateStr = $date->toDateString(); 
            if ($type == 'expired') {
                $query->whereRaw("DATE(changed_at + INTERVAL expiration_date MONTH) < ?", [$dateStr]);
            } else {
                $query->whereRaw("DATE(changed_at + INTERVAL expiration_date MONTH) = ?", [$dateStr]);
            }
        })->get();

        return [
            'id' => $this->id,
            'category' => [
                'id' => $this->category_id,
                'name' => $this->category->type
            ],
            'address' => $this->address,
            'created_at' => $this->created_at->format('Y-m-d'),
            'filters' => ByTpesFiltersResource::collection($filters)
        ];
    }
}
