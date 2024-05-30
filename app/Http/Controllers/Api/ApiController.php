<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{
    public function register(Request $request)
    {
        try 
        {
        $validateUser = validator::make($request->all(),
        [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
        ]);

        if($validateUser->fails()){
            return response()->json(
                [
                    'status'  => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ],401
            );
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
        ]);

        return response()->json(
            [
                'status'  => true,
                'message' => 'User Created Success',
                'token' => $user->createToken("API TOKEN")->plainTextToken
            ],200
        );
            } catch (\Throwable $th) {
                return response()->json(
                    [
                        'status'  => false,
                        'message' => $th->getMessage(),
                    ],500
                );
            }

    }

    public function login(Request $request)
    {
        try 
        {
            $validateUser = validator::make($request->all(),
            [
                'email' => 'required|email',
                'password' => 'required',
            ]);
    
            if($validateUser->fails()){
                return response()->json(
                    [
                        'status'  => false,
                        'message' => 'validation error',
                        'errors' => $validateUser->errors()
                    ],401
                );
            }

            if(!Auth::attempt($request->only(['email','password']))){
                return response()->json(
                    [
                        'status'  => false,
                        'message' => 'Email and Password does not match with our record.'
                    ],401
                );
            }

            $user = User::where('email',$request->email)->first();
            return response()->json(
                [
                    'status'  => true,
                    'message' => 'User Login Success',
                    'token' => $user->createToken("API TOKEN")->plainTextToken
                ],200
            );

        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status'  => false,
                    'message' => $th->getMessage(),
                ],500
            );
        }
    }

    public function profile(){
        $userData = auth()->user();
        return response()->json(
            [
                'status'  => true,
                'message' => 'Profile Information',
                'data' => $userData,
                'id' => auth()->user()->id
            ],200
        );
    }
    
    public function logout(){
        auth()->user()->tokens()->delete();
        return response()->json(
            [
                'status'  => true,
                'message' => 'LogOut Success',
                'data' => [],
            ],200
        );

    }
}
