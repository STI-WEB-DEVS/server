<?php

namespace App\Service;

// use App\Http\Resources\LanguageResource;
// use App\Repository\LanguageRepository;

class CustomerService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getCustomers()
    {
        return "List of Customers";
    }
}
