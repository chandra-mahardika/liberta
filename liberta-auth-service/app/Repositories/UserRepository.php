<?php

namespace App\Repositories;

use App\Domain\User;
use Liberta\Sql\DB;

class UserRepository
{
    public function __construct(
        protected DB $db
    ) {}

    public function findByUsername(string $username): ?User
    {
        $row = $this->db
            ->table('users')
            ->where('username', '=', $username)
            ->first();

        if (!$row) {
            return null;
        }

        return $this->mapUser($row);
    }

    public function findById(string $id): ?User
    {
        $row = $this->db
            ->table('users')
            ->where('id', '=', $id)
            ->first();

        if (!$row) {
            return null;
        }

        return $this->mapUser($row);
    }

    private function mapUser(object $row): User
    {
        $roles = $this->db
            ->table('roles')
            ->select('roles.name')
            ->join('user_roles', 'user_roles.role_id', '=', 'roles.id')
            ->where('user_roles.user_id', '=', $row->id)
            ->pluck('name');

        $permissions = $this->db
            ->table('permissions')
            ->select('permissions.name')
            ->join('role_permissions', 'role_permissions.permission_id', '=', 'permissions.id')
            ->join('user_roles', 'user_roles.role_id', '=', 'role_permissions.role_id')
            ->where('user_roles.user_id', '=', $row->id)
            ->distinct()
            ->pluck('name');

        return new User(
            id: (string) $row->id,
            username: $row->username,
            passwordHash: $row->password_hash,
            roles: $roles,
            permissions: $permissions,
        );
    }
}
