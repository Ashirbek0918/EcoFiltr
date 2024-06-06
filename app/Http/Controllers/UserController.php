<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\User\UserAddRequest;
use App\Http\Resources\Comment\CommentsResource;

class UserController extends Controller
{
    public function create(UserAddRequest $request){
        $data = $request->validated();
        User::create([
            'name'=>$data['name'],
            'phone'=>$data['phone'],
            'address'=>$data['address'],
        ]);
        return response()->json([
            'success' => true,
            'message' => 'User created successfully'
        ],201);
    }

    public function update(Request $request,User $user){
        if($user->update($request->all())){
            return response()->json([
               'success' => true,
               'message' => 'User updated successfully'
            ],200);
        }
    }


    public function delete(User $user){
        $orders = $user->orders;
        foreach($orders as $order){
            $order->update(['status'=>'deleted']);
        }
        $user->delete();
        return response()->json([
           'success' => true,
           'message' => 'User deleted successfully'
        ],200);
    }

    public function about(User $user){
        if($user){
            return response()->json([
               'success' => true,
                'user' =>[
                    'id' => $user->id,
                    'name' => $user->name,
                    'phone' => $user->phone,
                    'address' => $user->address,
                    'orders' => $user->orders->map(function($order){
                        return [
                            'id' => $order->id,
                           'status' => $order->status,
                            'created_at' => $order->created_at,
                            'updated_at' => $order->updated_at,
                            'comments' => CommentsResource::collection($order->comments),
                            'filters' => $order->filters->map(function($product){
                                return [
                                    'id' => $product->id,
                                    'ordered_at' => $product->ordered_at,
                                    'expiration_date' => $product->expiration_date,
                                    'changed_at' => $product->changed_at,
                                ];
                            })
                        ];
                    })
                ]
            ],200);
        }
    }
}
