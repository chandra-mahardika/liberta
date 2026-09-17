<?php

namespace App\Domain;

class Permission
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $description = '',
    ) {}
}
