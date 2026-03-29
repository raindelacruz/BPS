<?php

namespace App\Controllers;

use App\Helpers\ResponseHelper;
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
}
