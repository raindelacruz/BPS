<?php

namespace App\Services;

class FileUploadService extends BaseService
{
    public function storeNoticePdf(array $file): string
    {
        if (!isset($file['tmp_name']) || !is_string($file['tmp_name'])) {
            throw new \RuntimeException('Uploaded file is invalid.');
        }

        $directory = app('app.upload_path');

        if (!is_dir($directory)) {
            mkdir($directory, 0775, true);
        }

        $filename = uniqid('notice_', true) . '.pdf';
        $destination = rtrim($directory, '/\\') . DIRECTORY_SEPARATOR . $filename;

        if (is_uploaded_file($file['tmp_name'])) {
            $stored = move_uploaded_file($file['tmp_name'], $destination);
        } else {
            $stored = copy($file['tmp_name'], $destination);
        }

        if (!$stored) {
            throw new \RuntimeException('Unable to store uploaded PDF.');
        }

        return 'storage/uploads/notices/' . $filename;
    }

    public function delete(?string $relativePath): void
    {
        if (!$relativePath) {
            return;
        }

        $absolutePath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relativePath);

        if (is_file($absolutePath)) {
            unlink($absolutePath);
        }
    }
}
