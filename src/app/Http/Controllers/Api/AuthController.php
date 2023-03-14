<?php


namespace App\Http\Controllers\Api;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;
use \Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ( Auth::attempt($credentials) ) {
            $user = User::select(['name', 'email'])->whereEmail($request->email)->first();

            return response()->json(['user' => $user], Response::HTTP_OK);
        }

        return response()->json('User Not Found.', Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function logout (Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response(['message' => 'You have been successfully logged out.'], Response::HTTP_OK);
    }

}
