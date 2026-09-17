<?php

declare(strict_types=1);

namespace Liberta\Http\Validation;

use Liberta\Exception\ValidationException;

class Validator
{
    /** @var array<string, string[]> */
    private array $errors = [];

    /** @var array<string, mixed> */
    private array $currentData = [];

    /**
     * Validate data against rules.
     *
     * @param array<string, mixed> $data
     * @param array<string, string> $rules  Field => rule string (e.g., 'required|string|min:3')
     * @param array<string, string> $messages  Custom error messages
     * @return array<string, mixed>  Sanitized data
     * @throws ValidationException
     */
    public function validate(array $data, array $rules, array $messages = []): array
    {
        $this->errors = [];
        $this->currentData = $data;

        foreach ($rules as $field => $ruleString) {
            $fieldRules = $this->parseRules($ruleString);
            $value = $data[$field] ?? null;
            $label = $messages[$field . '.label'] ?? $this->humanize($field);

            foreach ($fieldRules as $rule) {
                $this->applyRule($field, $value, $rule, $label, $messages);
            }
        }

        if (!empty($this->errors)) {
            throw new ValidationException($this->errors);
        }

        return $data;
    }

    /**
     * Validate a single field.
     *
     * @param mixed $value
     */
    public function validateField(string $field, mixed $value, string $ruleString, array $messages = []): void
    {
        $this->errors = [];
        $fieldRules = $this->parseRules($ruleString);
        $label = $messages[$field . '.label'] ?? $this->humanize($field);

        foreach ($fieldRules as $rule) {
            $this->applyRule($field, $value, $rule, $label, $messages);
        }

        if (!empty($this->errors)) {
            throw new ValidationException($this->errors);
        }
    }

    /**
     * @return array<string, string[]>
     */
    public function errors(): array
    {
        return $this->errors;
    }

    private function parseRules(string $ruleString): array
    {
        return array_map('trim', explode('|', $ruleString));
    }

    /**
     * @param mixed $value
     */
    private function applyRule(string $field, mixed $value, string $rule, string $label, array $messages): void
    {
        $params = [];
        if (str_contains($rule, ':')) {
            [$rule, $paramStr] = explode(':', $rule, 2);
            $params = explode(',', $paramStr);
        }

        $messageKey = $field . '.' . $rule;

        match ($rule) {
            'required' => $this->validateRequired($field, $value, $label, $messages[$messageKey] ?? null),
            'string' => $this->validateString($field, $value, $label, $messages[$messageKey] ?? null),
            'integer' => $this->validateInteger($field, $value, $label, $messages[$messageKey] ?? null),
            'float' => $this->validateFloat($field, $value, $label, $messages[$messageKey] ?? null),
            'email' => $this->validateEmail($field, $value, $label, $messages[$messageKey] ?? null),
            'min' => $this->validateMin($field, $value, (int)($params[0] ?? 0), $label, $messages[$messageKey] ?? null),
            'max' => $this->validateMax($field, $value, (int)($params[0] ?? 0), $label, $messages[$messageKey] ?? null),
            'between' => $this->validateBetween($field, $value, (int)($params[0] ?? 0), (int)($params[1] ?? 0), $label, $messages[$messageKey] ?? null),
            'in' => $this->validateIn($field, $value, $params, $label, $messages[$messageKey] ?? null),
            'alpha' => $this->validateAlpha($field, $value, $label, $messages[$messageKey] ?? null),
            'alphaNum' => $this->validateAlphaNum($field, $value, $label, $messages[$messageKey] ?? null),
            'alphaDash' => $this->validateAlphaDash($field, $value, $label, $messages[$messageKey] ?? null),
            'date' => $this->validateDate($field, $value, $label, $messages[$messageKey] ?? null),
            'url' => $this->validateUrl($field, $value, $label, $messages[$messageKey] ?? null),
            'uuid' => $this->validateUuid($field, $value, $label, $messages[$messageKey] ?? null),
            'regex' => $this->validateRegex($field, $value, $params[0] ?? '', $label, $messages[$messageKey] ?? null),
            'confirmed' => $this->validateConfirmed($field, $value, $label, $messages[$messageKey] ?? null),
            'array' => $this->validateArray($field, $value, $label, $messages[$messageKey] ?? null),
            default => null,
        };
    }

    /**
     * @param mixed $value
     */
    private function addError(string $field, string $message): void
    {
        $this->errors[$field][] = $message;
    }

    private function humanize(string $field): string
    {
        return ucfirst(str_replace('_', ' ', $field));
    }

    /**
     * @param mixed $value
     */
    private function validateRequired(string $field, mixed $value, string $label, ?string $message): void
    {
        if ($value === null || $value === '' || $value === []) {
            $this->addError($field, $message ?? "{$label} wajib diisi.");
        }
    }

    /**
     * @param mixed $value
     */
    private function validateString(string $field, mixed $value, string $label, ?string $message): void
    {
        if ($value !== null && $value !== '' && !is_string($value)) {
            $this->addError($field, $message ?? "{$label} harus berupa string.");
        }
    }

    /**
     * @param mixed $value
     */
    private function validateInteger(string $field, mixed $value, string $label, ?string $message): void
    {
        if ($value !== null && $value !== '' && !ctype_digit((string) $value)) {
            $this->addError($field, $message ?? "{$label} harus berupa angka bulat.");
        }
    }

    /**
     * @param mixed $value
     */
    private function validateFloat(string $field, mixed $value, string $label, ?string $message): void
    {
        if ($value !== null && $value !== '' && !is_numeric($value)) {
            $this->addError($field, $message ?? "{$label} harus berupa angka.");
        }
    }

    /**
     * @param mixed $value
     */
    private function validateEmail(string $field, mixed $value, string $label, ?string $message): void
    {
        if ($value !== null && $value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->addError($field, $message ?? "{$label} harus berupa email valid.");
        }
    }

    /**
     * @param mixed $value
     */
    private function validateMin(string $field, mixed $value, int $min, string $label, ?string $message): void
    {
        if ($value === null || $value === '') {
            return;
        }

        if (is_string($value) && mb_strlen($value) < $min) {
            $this->addError($field, $message ?? "{$label} minimal {$min} karakter.");
        } elseif (is_array($value) && count($value) < $min) {
            $this->addError($field, $message ?? "{$label} minimal {$min} item.");
        } elseif (is_numeric($value) && $value < $min) {
            $this->addError($field, $message ?? "{$label} minimal {$min}.");
        }
    }

    /**
     * @param mixed $value
     */
    private function validateMax(string $field, mixed $value, int $max, string $label, ?string $message): void
    {
        if ($value === null || $value === '') {
            return;
        }

        if (is_string($value) && mb_strlen($value) > $max) {
            $this->addError($field, $message ?? "{$label} maksimal {$max} karakter.");
        } elseif (is_array($value) && count($value) > $max) {
            $this->addError($field, $message ?? "{$label} maksimal {$max} item.");
        } elseif (is_numeric($value) && $value > $max) {
            $this->addError($field, $message ?? "{$label} maksimal {$max}.");
        }
    }

    /**
     * @param mixed $value
     */
    private function validateBetween(string $field, mixed $value, int $min, int $max, string $label, ?string $message): void
    {
        if ($value === null || $value === '') {
            return;
        }

        if (is_string($value)) {
            $len = mb_strlen($value);
            if ($len < $min || $len > $max) {
                $this->addError($field, $message ?? "{$label} harus antara {$min} dan {$max} karakter.");
            }
        } elseif (is_numeric($value)) {
            if ($value < $min || $value > $max) {
                $this->addError($field, $message ?? "{$label} harus antara {$min} dan {$max}.");
            }
        }
    }

    /**
     * @param mixed $value
     * @param list<string> $allowed
     */
    private function validateIn(string $field, mixed $value, array $allowed, string $label, ?string $message): void
    {
        if ($value === null || $value === '') {
            return;
        }

        if (!in_array((string) $value, $allowed, true)) {
            $this->addError($field, $message ?? "{$label} harus salah satu dari: " . implode(', ', $allowed) . ".");
        }
    }

    /**
     * @param mixed $value
     */
    private function validateAlpha(string $field, mixed $value, string $label, ?string $message): void
    {
        if ($value !== null && $value !== '' && !preg_match('/^\p{L}+$/u', (string) $value)) {
            $this->addError($field, $message ?? "{$label} hanya boleh berisi huruf.");
        }
    }

    /**
     * @param mixed $value
     */
    private function validateAlphaNum(string $field, mixed $value, string $label, ?string $message): void
    {
        if ($value !== null && $value !== '' && !preg_match('/^[\p{L}\p{N}]+$/u', (string) $value)) {
            $this->addError($field, $message ?? "{$label} hanya boleh berisi huruf dan angka.");
        }
    }

    /**
     * @param mixed $value
     */
    private function validateAlphaDash(string $field, mixed $value, string $label, ?string $message): void
    {
        if ($value !== null && $value !== '' && !preg_match('/^[\p{L}\p{N}_\-]+$/u', (string) $value)) {
            $this->addError($field, $message ?? "{$label} hanya boleh berisi huruf, angka, dash, dan underscore.");
        }
    }

    /**
     * @param mixed $value
     */
    private function validateDate(string $field, mixed $value, string $label, ?string $message): void
    {
        if ($value !== null && $value !== '' && strtotime((string) $value) === false) {
            $this->addError($field, $message ?? "{$label} harus berupa tanggal valid.");
        }
    }

    /**
     * @param mixed $value
     */
    private function validateUrl(string $field, mixed $value, string $label, ?string $message): void
    {
        if ($value !== null && $value !== '' && !filter_var($value, FILTER_VALIDATE_URL)) {
            $this->addError($field, $message ?? "{$label} harus berupa URL valid.");
        }
    }

    /**
     * @param mixed $value
     */
    private function validateUuid(string $field, mixed $value, string $label, ?string $message): void
    {
        if ($value !== null && $value !== '' && !preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', (string) $value)) {
            $this->addError($field, $message ?? "{$label} harus berupa UUID valid.");
        }
    }

    /**
     * @param mixed $value
     */
    private function validateRegex(string $field, mixed $value, string $pattern, string $label, ?string $message): void
    {
        if ($value !== null && $value !== '' && !preg_match($pattern, (string) $value)) {
            $this->addError($field, $message ?? "{$label} format tidak valid.");
        }
    }

    /**
     * @param mixed $value
     * @param array<string, mixed> $data
     */
    private function validateConfirmed(string $field, mixed $value, string $label, ?string $message): void
    {
        $confirmField = $field . '_confirmation';

        if (!isset($this->errors[$field])) {
            $data = $this->getCurrentData();
            $confirmValue = $data[$confirmField] ?? null;

            if ($value !== $confirmValue) {
                $this->addError($field, $message ?? "{$label} konfirmasi tidak cocok.");
            }
        }
    }

    /**
     * @param mixed $value
     */
    private function validateArray(string $field, mixed $value, string $label, ?string $message): void
    {
        if ($value !== null && !is_array($value)) {
            $this->addError($field, $message ?? "{$label} harus berupa array.");
        }
    }

    /** @return array<string, mixed> */
    private function getCurrentData(): array
    {
        return $this->currentData ?? [];
    }
}
