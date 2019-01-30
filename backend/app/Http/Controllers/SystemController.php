<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use App\User;

class SystemController extends Controller
{

    /**
     * User login
     * 
     * @param \Illuminate\Http\Request $request Object with data from HTTP client user/pass
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory Response success/fail
     */
    public function login(\Illuminate\Http\Request $request)
    {

        $user = User::where('username', $request->input('username'))->first();

        if ($user && Hash::check($request->input('password'), $user->password)) {

            if (!$user->token) {            
                $token = base64_encode(str_random(40));
                User::where('username', $request->input('username'))->update(['token' => $token]);
            } else {
                $token = $user->token;
            }
            
            return response()->json(['status' => 'success', 'token' => $token]);
        } else {

            return response()->json(['status' => 'fail'], 401);
        }
    }

    /**
     * Ping service response
     * 
     * Used to check client version from header through middleware CheckClientVersion
     * 
     * @param \Illuminate\Http\Request $req
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function ping(\Illuminate\Http\Request $req)
    {
        return response()->json(['message' => 'PONG']);
    }

}
