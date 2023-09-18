<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    protected const TOKEN_NAME = 'BookingAppAuth';

    public function register(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|min:3',
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        try {
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);

            $user->save();
        
            $token = $user->createToken(self::TOKEN_NAME)->accessToken;
    
            return response()->json(['token' => $token], 200);
        } catch (Exception $e) {
            // log error $e->getMessage()
            $message = $e->getMessage();
            
            if (str_contains($message, 'Duplicate entry') && str_contains($message, 'users.users_email_unique')) {
                return response()->json(['message' => "Email {$request->email} is already registered."], 500);
            }
            
            return response()->json(['message' => 'There is an error in creating account'], 500);
        }
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required',
        ]); 
 
        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return response(['message' => 'Account found'], 404);
        }

        if (Hash::check($request->password, $user->password)) {
            $token = $user->createToken(self::TOKEN_NAME)->accessToken;
            $response = ['token' => $token];
        
            return response($response, 200);
        }
        
        return response(['message' => 'Invalid password'], 422);
    }
    
    public function logout(Request $request) {
        $token = $request->user()->token();
        $token->revoke();
        $response = ['message' => 'Logged out successful.'];

        return response($response, 200);
    }
}
