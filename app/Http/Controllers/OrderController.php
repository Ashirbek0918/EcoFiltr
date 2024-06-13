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
            $user = User::create([
                'name' => $data['name'],
                'phone' => $data['phone'],
                'address' => $data['address'],
            ]);
            $order = Order::create([
                'user_id' => $user->id,
                'category_id' => $category->id,
                'status' => 'active',
            ]);
            $filterExpirationDates = $data['expiration_date'];
            if(empty($filterExpirationDates)){
                if($category->id == 1){
                    $filterExpirationDates = [2, 4];
                }else if($category->id == 2){
                    $filterExpirationDates = [6];
                }
            }
            $filters = array_map(function ($expirationDate) use ($order, $category) {
                return [
                    'order_id' => $order->id,
                    'user_id' => $order->user->id,
                    'ordered_at' => Carbon::now()->subMonths(3)->addDays(12),
                    'expiration_date' => $expirationDate,
                    'category_id' => $category->id,
                    'changed_at' => Carbon::now()->subMonths(3)->addDays(12),
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
   
}
