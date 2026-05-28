<?php

namespace App\Service;

use App\Http\Resources\UserResource;
use App\Repository\UserRepository;
use App\Repository\CustomersRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserService
{
    private UserRepository $userRepository;
    private CustomersRepository $customersRepository;

    public function __construct(UserRepository $userRepository, CustomersRepository $customersRepository)
    {
        $this->userRepository = $userRepository;
        $this->customersRepository = $customersRepository;
    }

    public function registerCustomer(object $payload)
    {
        // Validate input
        if (empty($payload->name) || empty($payload->email) || empty($payload->password)) {
            return response()->json(['message' => 'Name, email, and password are required'], 400);
        }

        // Check if email already exists
        if ($this->userRepository->findByField('email', $payload->email)) {
            return response()->json(['message' => 'Email already registered'], 409);
        }

        // Use transaction for consistency
        return DB::transaction(function () use ($payload) {
            // Create customer record
            $customer = $this->customersRepository->create([
                'name'  => $payload->name,
                'email' => $payload->email,
            ]);

            // Create user record linked to customer
            $user = $this->userRepository->create([
                'name'        => $payload->name,
                'email'       => $payload->email,
                'password'    => Hash::make($payload->password),
                'customer_id' => $customer->id,
            ]);

            // Assign customer role
            $user->assignRole('customer');

            // Load customer relationship
            $user->load('customer');

            // Create token for auto-login
            $token = $user->createToken($user->email)->plainTextToken;

            return response()->json([
                'message' => 'Customer registered successfully',
                'user'    => new UserResource($user),
                'token'   => $token,
            ], 201);
        });
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

        // Eager-load customer so customer_uuid is available in UserResource
        $user->load('customer');

        $token = $user->createToken($user->email)->plainTextToken;

        return response()->json([
            'user'  => new UserResource($user),
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
