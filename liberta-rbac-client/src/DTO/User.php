<?php

namespace Liberta\Rbac\DTO;

class User
{
    public function __construct(
        public readonly string $id,
        public readonly string $username,
        public readonly array $roles = [],
        public readonly array $permissions = [],
    ) {}

    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->permissions, true);
    }

    public function hasRole(string $role): bool
    {
        return in_array($role, $this->roles, true);
    }

    public function toArray(): array
    {
        return [
            'id'          => $this->id,
            'username'    => $this->username,
            'roles'       => $this->roles,
            'permissions' => $this->permissions,
        ];
    }
}
