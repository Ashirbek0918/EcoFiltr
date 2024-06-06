<?php

namespace App\Http\Resources\Order;

use Illuminate\Http\Request;
use App\Http\Resources\Filter\FiltersResource;
use App\Http\Resources\Comment\CommentsResource;
use Illuminate\Http\Resources\Json\JsonResource;

class OrdersWithFiltersREsource extends JsonResource
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
            'category' =>[
                'id' => $this->category_id,
                'name' => $this->category->type
            ],
            'address' => $this->address,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'comments' => CommentsResource::collection($this->comments),
            'filters' => FiltersResource::collection($this->filters)
        ];
    }
}
