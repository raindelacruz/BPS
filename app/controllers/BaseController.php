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
            $errors = [];
            ValidationHelper::addError($errors, '_global', 'Your session expired. Please try again.');
            $this->redirectWithValidation($redirectPath, $formKey ?? 'csrf', $errors, $old, 'Your session expired. Please submit the form again.');
        }
    }
}
