<?php

namespace App\Helpers;

class ViewHelper
{
    public static function render(string $template, array $data = [], string $layout = 'main'): void
    {
        $viewsPath = app('app.views_path');
        $templatePath = $viewsPath . '/' . str_replace('.', '/', $template) . '.php';
        $layoutPath = $viewsPath . '/layouts/' . $layout . '.php';

        if (!is_file($templatePath)) {
            ResponseHelper::abort(500, 'View not found: ' . $template);
        }

        extract($data, EXTR_SKIP);

        ob_start();
        require $templatePath;
        $content = ob_get_clean();

        require $layoutPath;
    }

    public static function escape(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}
