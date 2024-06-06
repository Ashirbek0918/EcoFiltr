<?php

namespace App\Http\Controllers;

use App\Http\Resources\User\UsersWithByTypesFiltersREsource;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Models\Filter;
use Illuminate\Http\Request;
use App\Http\Resources\User\UsersWithFiltersREsource;

class FilterController extends Controller
{
    private function filters($filters)
    {
        $orderIds = $filters->pluck('order_id')->unique();

        $orders = Order::whereIn('id', $orderIds)
            ->with(['user', 'filters' => function ($query) use ($filters) {
                $query->whereIn('id', $filters->pluck('id'));
            }])
            ->get();
        $users = collect([]);
        $orderUsers = $orders->pluck('user')->unique();

        foreach ($orderUsers as $user) {
            $userFilters = $orders->filter(function ($order) use ($user) {
                return $order->user->id === $user->id;
            })->flatMap->filters;
            $user->setRelation('filters', $userFilters);
            $users->push($user);
        }
        return $users;
    }

    public function getFilteredUsers(Request $request, $filterType)
    {
        $date = Carbon::today();
        if ($filterType == 'tomorrow') {
            $date = Carbon::tomorrow();
        } elseif ($filterType == 'expired') {
            $date = Carbon::yesterday();
        }
        $query = User::query();
        if ($request->has('search')) {
            $search = $request->input('search');
            if (is_numeric($search)) {
                $query->where('phone', 'like', "%{$search}%");
            } else {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
            }
        }
        $filters = Filter::getFiltersByDate($date, $filterType);
        $users = $this->filters($filters);
        $users = $query->whereIn('id', $users->pluck('id'))->paginate($request->input('per_page', 10));

        return response()->json([
            'success' => true,
            'total' => $users->total(),
            'per_page' => $users->perPage(),
            'current_page' => $users->currentPage(),
            'last_page' => $users->lastPage(),
            'data' => UsersWithByTypesFiltersREsource::collection($users)
        ]);
    }

    public function all(Request $request)
    {
        $userIds = Order::where('status', $request->input('status'))->pluck('user_id')->toArray();
        $query = User::whereIn('id', $userIds);
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($query) use ($search) {
                if (is_numeric($search)) {
                    $query->where('phone', 'like', "%{$search}%");
                } else {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%");
                }
            });
        }
        $users = $query->paginate($request->input('per_page', 10));
        return response()->json([
            'success' => true,
            'total' => $users->total(),
            'per_page' => $users->perPage(),
            'current_page' => $users->currentPage(),
            'last_page' => $users->lastPage(),
            'data' => UsersWithFiltersREsource::collection($users)
        ]);
    }

    public function delete($id)
    {
        $filter = Filter::findOrFail($id);
        $order = Order::findOrFail($filter->order_id);
        $order->filters()->delete();
        $order->delete();
        return response()->json([
            'success' => true,
            'message' => 'Filter deleted successfully'
        ]);
    }
}
