<?php

namespace App\Controllers;

use App\Helpers\FormStateHelper;
use App\Helpers\LogHelper;
use App\Helpers\ResponseHelper;
use App\Helpers\SessionHelper;
use App\Helpers\ValidationHelper;
use Throwable;
use App\Helpers\ViewHelper;

abstract class BaseController
{
    private const BYTES_PER_KILOBYTE = 1024;

    protected function view(string $template, array $data = [], string $layout = 'main'): void
    {
        ViewHelper::render($template, $data, $layout);
    }

    protected function json(array $payload, int $status = 200): void
    {
        ResponseHelper::json($payload, $status);
    }

    protected function redirect(string $path): void
    {
        ResponseHelper::redirect($path);
    }

    protected function formState(string $formKey, array $defaults = []): array
    {
        return FormStateHelper::consume($formKey, $defaults);
    }

    protected function redirectWithValidation(string $path, string $formKey, array $errors, array $old = [], ?string $message = null): void
    {
        FormStateHelper::flash($formKey, $errors, $old);
        SessionHelper::flash('error', $message ?? 'Please correct the highlighted fields and try again.');
        $this->redirect($path);
    }

    protected function redirectWithError(string $path, string $message, ?string $formKey = null, array $old = []): void
    {
        if ($formKey !== null) {
            FormStateHelper::flash($formKey, [], $old);
        }

        SessionHelper::flash('error', $message);
        $this->redirect($path);
    }

    protected function handleFormException(
        Throwable $throwable,
        string $logMessage,
        string $redirectPath,
        string $userMessage,
        ?string $formKey = null,
        array $old = [],
        array $context = []
    ): void {
        LogHelper::error($logMessage, $context, $throwable);
        $this->redirectWithError($redirectPath, $userMessage, $formKey, $old);
    }

    protected function enforceCsrfOrRedirect(string $redirectPath, ?string $formKey = null, array $old = []): void
    {
        if (!\App\Helpers\SecurityHelper::verifyCsrf($_POST['_token'] ?? null)) {
            $message = $this->csrfFailureMessage();
            $errors = [];
            ValidationHelper::addError($errors, '_global', $message);
            $this->redirectWithValidation($redirectPath, $formKey ?? 'csrf', $errors, $old, $message);
        }
    }

    protected function csrfFailureMessage(string $default = 'Your session expired. Please try again.'): string
    {
        if (!$this->requestExceedsPostMaxSize()) {
            return $default;
        }

        $limit = $this->effectiveUploadLimitLabel();

        return $limit !== null
            ? 'Upload failed because the file is too large. Maximum allowed size is ' . $limit . '.'
            : 'Upload failed because the file is too large.';
    }

    protected function effectiveUploadLimitLabel(): ?string
    {
        $limits = array_filter([
            $this->iniSizeToBytes((string) ini_get('upload_max_filesize')),
            $this->iniSizeToBytes((string) ini_get('post_max_size')),
        ], static fn (int $value): bool => $value > 0);

        if ($limits === []) {
            return null;
        }

        return $this->formatBytes((int) min($limits));
    }

    private function requestExceedsPostMaxSize(): bool
    {
        if (strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET')) !== 'POST') {
            return false;
        }

        $contentLength = (int) ($_SERVER['CONTENT_LENGTH'] ?? 0);
        if ($contentLength <= 0 || $_POST !== [] || $_FILES !== []) {
            return false;
        }

        $postMaxSize = $this->iniSizeToBytes((string) ini_get('post_max_size'));

        return $postMaxSize > 0 && $contentLength > $postMaxSize;
    }

    private function iniSizeToBytes(string $value): int
    {
        $normalized = trim($value);
        if ($normalized === '') {
            return 0;
        }

        $unit = strtolower(substr($normalized, -1));
        $number = (float) $normalized;

        return match ($unit) {
            'g' => (int) round($number * self::BYTES_PER_KILOBYTE * self::BYTES_PER_KILOBYTE * self::BYTES_PER_KILOBYTE),
            'm' => (int) round($number * self::BYTES_PER_KILOBYTE * self::BYTES_PER_KILOBYTE),
            'k' => (int) round($number * self::BYTES_PER_KILOBYTE),
            default => (int) round($number),
        };
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes >= self::BYTES_PER_KILOBYTE ** 3) {
            return rtrim(rtrim(number_format($bytes / (self::BYTES_PER_KILOBYTE ** 3), 2, '.', ''), '0'), '.') . ' GB';
        }

        if ($bytes >= self::BYTES_PER_KILOBYTE ** 2) {
            return rtrim(rtrim(number_format($bytes / (self::BYTES_PER_KILOBYTE ** 2), 2, '.', ''), '0'), '.') . ' MB';
        }

        if ($bytes >= self::BYTES_PER_KILOBYTE) {
            return rtrim(rtrim(number_format($bytes / self::BYTES_PER_KILOBYTE, 2, '.', ''), '0'), '.') . ' KB';
        }

        return $bytes . ' bytes';
    }
}
