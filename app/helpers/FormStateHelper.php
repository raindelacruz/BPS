<?php

namespace App\Helpers;

class FormStateHelper
{
    public static function flash(string $formKey, array $errors = [], array $old = []): void
    {
        SessionHelper::flash(self::flashKey($formKey), [
            'errors' => $errors,
            'old' => $old,
        ]);
    }

    public static function consume(string $formKey, array $defaults = []): array
    {
        $state = SessionHelper::pullFlash(self::flashKey($formKey), []);
        $old = is_array($state['old'] ?? null) ? $state['old'] : [];
        $errors = is_array($state['errors'] ?? null) ? $state['errors'] : [];

        return [
            'old' => array_merge($defaults, $old),
            'errors' => $errors,
        ];
    }

    private static function flashKey(string $formKey): string
    {
        return '_form_state_' . $formKey;
    }
}
