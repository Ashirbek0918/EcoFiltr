<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Requests\Order\OrderAddRequest;
use App\Http\Resources\Filter\FiltersResource;
use App\Models\Filter;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function create(OrderAddRequest $request)
    {
        try {
            $data = $request->validated();
            $category = Category::findOrFail($data['category_id']);
            $userAddress = $data['address'] ?? User::findOrFail($data['user_id'])->address;
            DB::beginTransaction();
            $order = Order::create([
                'user_id' => $data['user_id'],
                'category_id' => $category->id,
                'status' => 'active',
                'address' => $userAddress,
            ]);
            if ($category->type == 'secondary') {
                $filterExpirationDates = ['2', '4'];
            } elseif ($category->type == 'primary') {
                $filterExpirationDates = ['6'];
            }
            $filters = array_map(function ($expirationDate) use ($order, $category) {
                return [
                    'order_id' => $order->id,
                    'ordered_at' => Carbon::now()->subMonths(2)->format('Y-m-d'),
                    'expiration_date' => $expirationDate,
                    'category_id' => $category->id,
                    'changed_at' => Carbon::now()->subMonths(2)->format('Y-m-d'),
                ];
            }, $filterExpirationDates);
            Filter::insert($filters);
            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Order created successfully',
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'An error occurred while recording order',
                'details' => $e->getMessage(),
                'line' => $e->getLine(),
                'body' => $e->getTraceAsString(),
            ], 500);
        }
    }

    public function update(Request $request, Order $order)
    {
        if ($order->update([
            'status' => $request->input('status'),
        ])) {
            return response()->json([
                'success' => true,
                'message' => 'Order updated successfully'
            ], 200);
        }
    }

    public function delete(Order $order)
    {
        if($order->type != 'deleted'){
            $order->update(['status' => 'deleted']);
            return response()->json([
                'success' => true,
                'message' => 'Order deleted successfully.'
            ], 200);
        }
        return response()->json([
           'success' => false,
           'message' => 'Order already deleted'
        ], 200);
        
    }

    public function archiveOrders(Request $request)
    {
        $type = $request->input('type') ?? 'stopped';
        $perPage = $request->input('per_page', 10);
        $query = User::query();
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                if (is_numeric($search)) {
                    $q->where('phone', 'like', "%{$search}%");
                } else {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%");
                }
            });
        }
        $users = $query->whereHas('orders', function ($q) use ($type) {
            $q->where('status', $type);
        })->with(['orders' => function ($q) use ($type) {
            $q->where('status', $type)->with('category', 'filters');
        }])->paginate($perPage);

        $collection = collect($users->items())->map(function ($user) use ($type) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'phone' => $user->phone,
                'address' => $user->address,
                'created_at' => $user->created_at->format('Y-m-d'),
                'orders' => $user->orders->map(function ($order) {
                    return [
                        'id' => $order->id,
                        'category' => [
                            'id' => $order->category->id,
                            'name' => $order->category->type
                        ],
                        'address' => $order->address,
                        'created_at' => $order->created_at->format('Y-m-d'),
                        'filters' => FiltersResource::collection($order->filters)
                    ];
                })
            ];
        });

        return response()->json([
            'success' => true,
            'total' => $users->total(),
            'per_page' => $users->perPage(),
            'current_page' => $users->currentPage(),
            'last_page' => $users->lastPage(),
            'data' => $collection
        ], 200);
    }
}
