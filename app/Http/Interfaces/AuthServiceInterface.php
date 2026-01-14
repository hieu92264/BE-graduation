<?php

namespace App\Http\Interfaces;

interface AuthServiceInterface
{
    public function register(array $data): array;

    public function login(array $data): array;

    public function logout(string $refreshToken): array;

    public function refreshToken(string $refreshToken): array;

    public function me(): array;

    public function changeUserPass(int $userId, array $password): array;
}
