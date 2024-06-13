<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Models\Filter;
use Illuminate\Http\Request;
use App\Http\Resources\User\UsersWithByTypesFiltersREsource;

class FilterController extends Controller
{
    public function getFilteredUsers(Request $request)
    {
        $query = User::query()->where('users.status', 'active');
        Filter::getfilters();

        if ($request->has('filter')) {
            $filterType = $request->input('filter');
            $date = Carbon::now()->startOfDay(); // Default value

            if ($filterType == 'today') {
                $date = Carbon::now()->startOfDay();
            } elseif ($filterType == 'tomorrow') {
                $date = Carbon::now()->addDay()->startOfDay();
            } elseif ($filterType == 'expired') {
                $date = Carbon::now()->startOfDay(); // Changed to now instead of yesterday
            }

            $filters = Filter::getfilters();
            $userIds = $filters->filter(function ($filter) use ($date, $filterType) {
                $changedAt = Carbon::parse($filter->changed_at);
                $expired = $changedAt->copy()->addMonths($filter->expiration_date)->endOfDay();

                if (($filterType == 'today' || $filterType == 'tomorrow') && $expired->isSameDay($date)) {
                    return true;
                } elseif ($filterType == 'expired' && $expired->lessThan($date)) {
                    return true;
                }
                return false;
            })->pluck('user_id')->toArray();

            $query->whereIn('users.id', $userIds);
        }


        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('phone', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate($request->input('per_page', 15));


        return response()->json([
            'success' => true,
            'total' => $users->total(),
            'per_page' => $users->perPage(),
            'current_page' => $users->currentPage(),
            'last_page' => $users->lastPage(),
            'data' => UsersWithByTypesFiltersResource::collection($users)
        ]);
    }

    public function update(Filter $filter)
    {
        if ($filter->update(['changed_at' => Carbon::now()])) {
            return response()->json([
                'success' => true,
                'message' => 'Filter updated successfully'
            ], 200);
        }
    }
}
