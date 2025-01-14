<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\HttpModules\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function isLogged(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'isLogged' => Auth::check()
            ]
        ]);
    }

    public function userLogged(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            return new UserResource($user);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'User not authenticated'
        ], 401);
    }
}
