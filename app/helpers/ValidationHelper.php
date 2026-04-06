<?php

namespace App\Helpers;

class ValidationHelper
{
    public static function addError(array &$errors, string $field, string $message): void
    {
        if (!isset($errors[$field]) || !is_array($errors[$field])) {
            $errors[$field] = [];
        }

        $errors[$field][] = $message;
    }

    public static function hasErrors(array $errors): bool
    {
        foreach ($errors as $messages) {
            if (is_array($messages) && $messages !== []) {
                return true;
            }
        }

        return false;
    }

    public static function first(array $errors, string $field): ?string
    {
        $messages = $errors[$field] ?? null;

        if (!is_array($messages) || $messages === []) {
            return null;
        }

        return (string) $messages[0];
    }

    public static function all(array $errors): array
    {
        $flattened = [];

        foreach ($errors as $messages) {
            if (!is_array($messages)) {
                continue;
            }

            foreach ($messages as $message) {
                $flattened[] = (string) $message;
            }
        }

        return $flattened;
    }

    public static function inputClass(array $errors, string $field, string $class = 'input-error'): string
    {
        return self::first($errors, $field) !== null ? $class : '';
    }
}
