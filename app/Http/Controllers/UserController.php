<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function update(Request $request,User $user){
        if($user->update($request->all())){
            return response()->json([
               'success' => true,
               'message' => 'User updated successfully'
            ],200);
        }
    }

    public function delete(User $user){
       if($user){
        $user->update([
            'status' => 'inactive'
        ]);
        return response()->json([
           'success' => true,
           'message' => 'User archived successfully'
        ]);
       }
    } 

    public function about(User $user){
        return response()->json([
           'success' => true,
            'user' =>[
                'id' => $user->id,
                'name' => $user->name,
                'phone' => $user->phone,
                'address' => $user->address,
                'description' => $user->description,
            ]
        ]);
    }
}
