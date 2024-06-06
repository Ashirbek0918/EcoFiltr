<?php

namespace App\Http\Controllers;

use App\Models\Employer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $user = Employer::where('email', $request->email)->first();
        if (!$user or !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => "Invalid password or email",
            ], 403);
        }
        $token = $user->createToken('employer')->plainTextToken;
        return response()->json([
            'success' => true,
            'token' => $token
        ]);
    }

    public function getme(){
        return response()->json([
            'success' => true,
            'user' =>auth()->user()
        ]);
    }
    public function logOut(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response([
            'success' => true,
            'message' => "You successfully logged out",
        ]);
    }
}
