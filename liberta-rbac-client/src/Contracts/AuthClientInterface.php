<?php

namespace Liberta\Rbac\Contracts;

use Liberta\Rbac\DTO\User;

interface AuthClientInterface
{
    public function authenticate(string $token): User;

    public function authorize(
        User $user,
        string $permission
    ): bool;
}
