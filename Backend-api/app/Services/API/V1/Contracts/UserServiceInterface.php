<?php

namespace App\Services\API\V1\Contracts;

use App\Models\API\V1\User;

interface UserServiceInterface
{
    public function register(array $data): User;
    public function authenticate(array $credentials);
}
