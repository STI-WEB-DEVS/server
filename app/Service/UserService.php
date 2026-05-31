<?php

namespace App\Service;

use App\Http\Resources\UserResource;
use App\Repository\UserRepository;
use Illuminate\Support\Facades\Hash;

class UserService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function loginUser(object $payload)
    {
        $email = $payload->input('email');
        $password = $payload->input('password');

        if (empty($email) || empty($password)) {
            return response()->json(['message' => 'Email and password are required'], 400);
        }

        $user = $this->userRepository->findByField('email', $email);

        if (! $user) {
            return response()->json(['message' => 'User not found'], 401);
        }

        if (! Hash::check($password, $user->password)) {
            return response()->json(['message' => 'Invalid password'], 401);
        }

        $token = $user->createToken($user->email)->plainTextToken;

        $user->load('customer');

        return response()->json([
            'user' => new UserResource($user),
            'token' => $token,
        ], 200);
    }

    public function logoutUser(object $user)
    {
        if ($user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }

        return response()->json(['message' => 'Logged out successfully'], 200);
    }
}