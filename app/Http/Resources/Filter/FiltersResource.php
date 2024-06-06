<?php

namespace App\Http\Resources\Filter;

use Illuminate\Http\Request;
use App\Http\Resources\Comment\CommentsResource;
use Illuminate\Http\Resources\Json\JsonResource;

class FiltersResource extends JsonResource
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
        ];
    }
}
