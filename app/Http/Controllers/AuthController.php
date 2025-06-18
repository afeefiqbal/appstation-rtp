<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    //register user 
    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|string|email|unique:users',
            'password'              => 'required|string|min:6|confirmed',
            'is_admin'              => 'sometimes|boolean',
        ], [
            'name.required'         => 'Name is required.',
            'email.required'        => 'Email is required.',
            'email.email'           => 'Please enter a valid email.',
            'email.unique'          => 'This email is already registered.',
            'password.required'     => 'Password is required.',
            'password.confirmed'    => 'Passwords do not match.',
            'password.min'          => 'Password must be at least 6 characters.',
        ]);
         if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation Failed',
                'errors'  => $validator->errors(),
            ], 422);
        }


        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => bcrypt($request->password),
            'is_admin'  => $request->has('is_admin') ? $request->is_admin : false,
        ]);

        $token = $user->createToken('rtb-token')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully.',
            'user'    => $user,
            'token'   => $token,
        ], 201);
    }

    //login 
    public function login(Request $request)
    {
      
       $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required'        => 'Email is required.',
            'email.email'           => 'Please enter a valid email.',
            'password.required'     => 'Password is required.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation Failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
           return response()->json([
                'message' => 'Invalid credentials.'
            ], 401);
        }

        $token = $user->createToken('rtb-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful.',
            'user'    => $user,
            'token'   => $token,
        ], 200);
    }

    public function logout(Request $request)
    {
        
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out Successfully']);
    }
}
