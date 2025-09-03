<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(Request $request)
    {

        try {
            $data =   $request->validate([
                'name' => ['required'],
                'email' => ['required', 'email', 'unique:users'],
                'password' => ['required', 'confirmed'],
            ]);
            $data['password'] = Hash::make($data['password']);

            $user = User::create($data);
            $token = $user->createToken('auth')->plainTextToken;
            return response()->json(
                [
                    'ok' => true,
                    'user' => $user,
                    'token' => $token
                ],
                201
            );
        } catch (\Exception $e) {
            return response()->json([
                'ok' => false,
                'message' => $e->getMessage()
            ], 401);
        }
    }


    public function login(Request $request)
    {


        $data =   $request->validate([

            'email' => ['required', 'email', 'exists:users,email'],
            'password' => ['required'],
        ]);
        if (Auth::attempt(['email' => $data['email'], 'password' => $data['password']])) {
            $user = auth()->user();
            $token = $user->createToken('auth')->plainTextToken;
            return response()->json([
                'ok' => true,
                'user' => $user,
                'token' => $token
            ], 200);
        } else {
            return response()->json([
                'ok' => false,
                'message' => 'invalid credentials'
            ], 401);
        }
    }
    public function logout(Request $request){
        $user=$request->user();
        $user->currentAccessToken()->delete();

        return response(['message'=> 'you are logout']);
}
