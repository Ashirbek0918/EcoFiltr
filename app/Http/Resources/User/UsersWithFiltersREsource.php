<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use App\Http\Resources\Filter\FiltersResource;
use App\Http\Resources\Order\OrdersWithFiltersREsource;
use Illuminate\Http\Resources\Json\JsonResource;

class UsersWithFiltersREsource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'address' => $this->address,
            'created_at' => $this->created_at,
            'orders' => OrdersWithFiltersREsource::collection($this->orders->where('status','active')),
        ];
    }
}
