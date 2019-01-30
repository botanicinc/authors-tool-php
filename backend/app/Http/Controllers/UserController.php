<?php

namespace App\Http\Controllers;

use App\User;

class UserController extends Controller
{
    /**
     * Returns user data
     *
     * @return Response
     */
    public function index()
    {
        return response()->json(User::get());
    }
    
}
