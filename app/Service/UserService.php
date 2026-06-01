<?php

namespace App\Service;

use App\Http\Resources\UserResource;
use App\Repository\UserRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function loginUser(object $payload)
    {
        if (empty($payload->email) || empty($payload->password)) {
            return response()->json(['message' => 'Email and password are required'], 400);
        }

        $user = $this->userRepository->findByField('email', $payload->email);

        if (! $user) {
            return response()->json(['message' => 'User not found'], 401);
        }

        if (! Hash::check($payload->password, $user->password)) {
            return response()->json(['message' => 'Invalid password'], 401);
        }

        $token = $user->createToken($user->email)->plainTextToken;

        return response()->json([
            'user' => new UserResource($user),
            'token' => $token,
        ], 200);
    }

    /**
     * Validate and register a new customer account using the UserRepository
     */
    public function registerCustomer(object $payload)
    {
        // 1. Validate incoming data manually since it's passed as an object payload
        $validator = Validator::make((array) $payload, [
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255', // If you have a specific custom uniqueness lookup in repository, use that or standard rules
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $validator->errors()
            ], 422);
        }

        // Check if user already exists via Repository to avoid database exceptions
        $existingUser = $this->userRepository->findByField('email', $payload->email);
        if ($existingUser) {
            return response()->json(['message' => 'Email address is already registered'], 422);
        }

        // 2. Map payload out and pass it cleanly to your repository handler
        // Assumes your repository has a standard create/store method accepting an array
        $user = $this->userRepository->create([
            'uuid'     => (string) Str::uuid(), 
            'name'     => $payload->name,
            'email'    => $payload->email,
            'password' => Hash::make($payload->password),
            'role'     => 'customer', // Guarantees proper routing maps in Nuxt upon authorization layout checks
        ]);

        // 3. Issue Sanctum token immediately so they can log straight in if needed
        $token = $user->createToken($user->email)->plainTextToken;

        return response()->json([
            'message' => 'Customer account registered successfully!',
            'user'    => new UserResource($user),
            'token'   => $token,
        ], 201);
    }

    public function logoutUser(object $user)
    {
        if ($user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }

        return response()->json(['message' => 'Logged out successfully'], 200);
    }
}