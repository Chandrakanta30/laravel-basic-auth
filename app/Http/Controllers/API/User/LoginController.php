<?php

namespace App\Http\Controllers\API\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;


class LoginController extends Controller
{
    public function register(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if($user){
            return response()->json(['code' => 401, 'message' => 'Email exists']);
        }

        $user = User::create([
            'name' => $request->name,
            'email' =>$request->email,
            'password' => Hash::make($request->password),
        ]);
            return $user;
    }

    public function login(Request $request)
    {
        
        $credentials = request(['email', 'password']);

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Incorrect credentials',
                'code' => 401,
            ]);
        } else {

            $user = auth()->user();
            $tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->token;
            $token->expires_at = Carbon::now()->addWeeks(5);
            $token->save();

            return response()->json([
                'message' => 'Login successful',
                'user' => $user,
                'access_token' => $tokenResult->accessToken,
                'code' => 200
            ]);
        }
    }
}
