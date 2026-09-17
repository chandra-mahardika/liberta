<?php

namespace Liberta\Exception;

class ValidationException extends Exception
{
    /** @var array<string, string[]> */
    private array $errors;

    /**
     * @param array<string, string[]> $errors  Map of field => [messages]
     */
    public function __construct(array $errors, ?\Throwable $previous = null)
    {
        $this->errors = $errors;
        parent::__construct('Validation failed', 422, $previous);
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function firstError(): ?string
    {
        foreach ($this->errors as $fieldErrors) {
            return $fieldErrors[0] ?? null;
        }
        return null;
    }
}
