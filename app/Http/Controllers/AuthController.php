<?php

namespace App\Http\Controllers;

use App\Service\UserService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

   public function login(Request $request)
{
    $credentials = $request->validate([
        'email'    => 'required|email',
        'password' => 'required',
    ]);

    if (!\Illuminate\Support\Facades\Auth::attempt($credentials)) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    $user = \Illuminate\Support\Facades\Auth::user();

    $token = $user->createToken($user->email)->plainTextToken;

    return response()->json([
        'token' => $token,
        'user' => [
            'uuid'  => $user->uuid,
            'name'  => $user->name,
            'email' => $user->email,
            'roles' => $user->getRoleNames(), // This pulls from Spatie
        ],
    ]);
}

    public function logout(Request $request)
    {
        return $this->userService->logoutUser($request->user());
    }

}
