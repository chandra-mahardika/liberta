<?php

namespace App\Domain;

class Role
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $description = '',
    ) {}
}
