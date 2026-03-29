<?php

namespace App\Services;

use DateTimeInterface;

class EmailService extends BaseService
{
    public function sendRegistrationVerification(
        string $email,
        string $recipientName,
        string $code,
        DateTimeInterface $expiresAt
    ): bool {
        $subject = 'eBPS Account Verification';
        $message = implode(PHP_EOL, [
            'Hello ' . trim($recipientName) . ',',
            '',
            'Your eBPS verification code is: ' . $code,
            'This code expires on ' . $expiresAt->format('Y-m-d H:i:s') . '.',
        ]);

        return $this->logMail($email, $subject, $message);
    }

    public function sendEmailChangeNotice(
        string $email,
        string $recipientName,
        string $token,
        DateTimeInterface $expiresAt
    ): bool {
        $subject = 'eBPS Email Change Request';
        $message = implode(PHP_EOL, [
            'Hello ' . trim($recipientName) . ',',
            '',
            'An eBPS email change request has been created for your account.',
            'Verification token: ' . $token,
            'Verification link: ' . rtrim((string) app('app.url'), '/') . '/email-change/verify?token=' . urlencode($token),
            'This request expires on ' . $expiresAt->format('Y-m-d H:i:s') . '.',
        ]);

        return $this->logMail($email, $subject, $message);
    }

    private function logMail(string $email, string $subject, string $message): bool
    {
        $directory = app('app.storage_path') . '/temp';

        if (!is_dir($directory)) {
            mkdir($directory, 0775, true);
        }

        $payload = sprintf(
            "[%s] TO: %s%sSUBJECT: %s%s%s%s",
            date('Y-m-d H:i:s'),
            $email,
            PHP_EOL,
            $subject,
            PHP_EOL,
            $message,
            PHP_EOL . str_repeat('-', 60) . PHP_EOL
        );

        return file_put_contents($directory . '/mail.log', $payload, FILE_APPEND) !== false;
    }
}
