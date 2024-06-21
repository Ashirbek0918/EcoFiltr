<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Models\Filter;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\User\UserAddRequest;
use App\Http\Resources\Filter\FiltersResource;

class OrderController extends Controller
{
    public function create(UserAddRequest $request)
    {
        try {
            $data = $request->validated();
            $category = Category::findOrFail($data['category_id']);
            DB::beginTransaction();
            $created_at = $data['created_at'] ?? Carbon::now();
            $user = User::create([
                'name' => $data['name'],
                'phone' => $data['phone'],
                'address' => $data['address'],
                'created_at' => $created_at,
            ]);
            $order = Order::create([
                'user_id' => $user->id,
                'category_id' => $category->id,
                'status' => 'active',
            ]);
            $filterExpirationDates = $data['expiration_date'];
            $filterDates = [];
            if (empty($filterExpirationDates)) {
                if ($category->id == 1) {
                    $filterExpirationDates = [2, 4];
                    $filterDates[] = $request->input('first_date') ?? Carbon::today();
                    $filterDates[] = $request->input('second_date') ?? Carbon::today();
                } else if ($category->id == 2) {
                    $filterExpirationDates = [6];
                    $filterDates[] = $request->input('first_date') ?? Carbon::today();
                }
            }
            for ($i = 0; $i < count($filterExpirationDates); $i++) {
                Filter::create([
                    'order_id' => $order->id,
                    'user_id' => $order->user->id,
                    'ordered_at' => $filterDates[$i],
                    'expiration_date' => $filterExpirationDates[$i],
                    'category_id' => $category->id,
                    'changed_at' => $filterDates[$i],
                ]);
            }
            // $filteradded = $data['filteradded'] ?? Carbon::today();
            // $filters = array_map(function ($expirationDate) use ($order, $category,$filteradded) {
            //     return [
            //         'order_id' => $order->id,
            //         'user_id' => $order->user->id,
            //         'ordered_at' => $filteradded,
            //         'expiration_date' => $expirationDate,
            //         'category_id' => $category->id,
            //         'changed_at' => $filteradded,
            //     ];
            // }, $filterExpirationDates);
            // Filter::insert($filters);
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
}
