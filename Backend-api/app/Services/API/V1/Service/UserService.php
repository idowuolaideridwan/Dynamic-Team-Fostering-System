<?php

namespace App\Services\API\V1\Service;

use App\Models\API\V1\User;
use App\Repositories\API\V1\Contracts\UserRepositoryInterface;
use App\Services\API\V1\Contracts\UserServiceInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class UserService implements UserServiceInterface
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register(array $data): User
    {
        // Check for duplicate email
        if ($this->userRepository->findByEmail($data['email'])) {
            throw ValidationException::withMessages(['email' => 'The email has already been taken.']);
        }

        // Create the user
        $data['password'] = Hash::make($data['password']);
        return $this->userRepository->create($data);
    }

    public function authenticate(array $credentials)
    {
        if (!$token = JWTAuth::attempt($credentials)) {
            throw new \Exception('Invalid credentials', 401);
        }
        return $token;
    }
}
