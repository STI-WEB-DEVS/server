<?php

namespace App\Service;

use App\Repository\UserRepository;
use App\Repository\CustomersRepository; 
use Illuminate\Support\Facades\Hash;

class UserService
{
    private UserRepository $userRepository;
    private CustomersRepository $customersRepository; 

    public function __construct(UserRepository $userRepository, CustomersRepository $customersRepository)
    {
        $this->userRepository = $userRepository;
        $this->customersRepository = $customersRepository;
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

        try {
            $customer = $this->customersRepository->findByField('email', $payload->email);
            $customerUuid = $customer->uuid;
            $customerName = $customer->name ?? 'Guest';
        } catch (\Exception $e) {
            $customerUuid = $user->uuid;
            $customerName = $user->name ?? 'Guest';
        }

        $token = $user->createToken($user->email)->plainTextToken;

        return response()->json([
            'uuid'  => $customerUuid,      
            'name'  => $customerName,    
            'role'  => $user->getRoleNames()->first() ?? 'customer',
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