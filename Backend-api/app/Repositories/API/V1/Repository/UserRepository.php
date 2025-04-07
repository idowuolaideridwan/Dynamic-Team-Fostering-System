<?php

namespace App\Repositories\API\V1\Repository;

use App\Models\API\V1\User;
use App\Repositories\API\V1\Contracts\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function create(array $data): User
    {
        return User::create($data);
    }
}
