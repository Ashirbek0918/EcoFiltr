<?php

namespace App\Http\Resources\Filter;

use App\Http\Resources\Comment\CommentsResource;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ByTpesFiltersResource extends JsonResource
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
            'ordered_at' => $this->ordered_at,
            'changed_at' => $this->changed_at,
            'expiration_date' => $this->expiration_date,
            'comments' => CommentsResource::collection($this->comments),
        ];
    }
}
