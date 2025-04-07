<?php

namespace App\Repositories\API\V1\Contracts;

use App\Models\API\V1\User;

interface UserRepositoryInterface
{
    public function findByEmail(string $email): ?User;
    public function create(array $data): User;
}
