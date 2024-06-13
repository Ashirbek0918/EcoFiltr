<?php

namespace App\Http\Resources\User;

use App\Http\Resources\Filter\FiltersResource;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UsersWithByTypesFiltersREsource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $filters = $this->getFilteredFilters($request);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'address' => $this->address,
            'status' => $this->status,
            'created_at' => $this->created_at->format('Y-m-d'),
            'description' => $this->description,
            'category' => $this->order()->first()->category->type,
            'filters' => FiltersResource::collection($filters)
        ];
    }

    /**
     * Get filtered filters based on the request parameters.
     *
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getFilteredFilters(Request $request): Collection
    {
        $filterType = $request->input('filter');
        $date = null;

        if ($filterType == 'today') {
            $date = Carbon::now()->startOfDay();
        } elseif ($filterType == 'tomorrow') {
            $date = Carbon::tomorrow()->startOfDay();
        } elseif ($filterType == 'expired') {
            $date = Carbon::now()->startOfDay();
        }

        $filters = $this->order()->first()->filters;
        if (!$filterType) {
            return $filters;
        }
        $filtered = $filters->filter(function ($filter) use ($date, $filterType) {
            $changedAt = Carbon::parse($filter->changed_at);
            $expired = $changedAt->copy()->addMonths($filter->expiration_date)->endOfDay();

            if ($filterType == 'today' || $filterType == 'tomorrow') {
                return $expired->isSameDay($date);
            } elseif ($filterType == 'expired') {
                return $expired->isBefore($date);
            }

            return false;
        });

        return $filtered;
    }
}
