<?php

namespace App\Services;

use DateTimeInterface;
use RuntimeException;
use Throwable;

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

        return $this->deliver($email, $subject, $message);
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

        return $this->deliver($email, $subject, $message);
    }

    private function deliver(string $email, string $subject, string $message): bool
    {
        $transport = strtolower((string) app('mail.transport', 'smtp'));

        if ($transport === 'log') {
            return $this->logMail($email, $subject, $message);
        }

        try {
            return $this->sendViaSmtp($email, $subject, $message);
        } catch (Throwable $throwable) {
            $this->logMailFailure($email, $subject, $throwable->getMessage());

            return false;
        }
    }

    private function sendViaSmtp(string $email, string $subject, string $message): bool
    {
        $host = (string) app('mail.host', 'localhost');
        $port = (int) app('mail.port', 25);
        $username = (string) app('mail.username', '');
        $password = (string) app('mail.password', '');
        $encryption = strtolower((string) app('mail.encryption', ''));
        $timeout = max(1, (int) app('mail.timeout', 10));
        $fromAddress = (string) app('mail.from_address', 'noreply@agency.gov.ph');
        $fromName = (string) app('mail.from_name', 'eBPS');

        $remoteHost = $encryption === 'ssl'
            ? 'ssl://' . $host
            : $host;

        $socket = @stream_socket_client(
            $remoteHost . ':' . $port,
            $errorCode,
            $errorMessage,
            $timeout,
            STREAM_CLIENT_CONNECT
        );

        if (!is_resource($socket)) {
            throw new RuntimeException('SMTP connection failed: ' . $errorMessage . ' (' . $errorCode . ')');
        }

        stream_set_timeout($socket, $timeout);

        try {
            $this->assertReply($socket, [220]);
            $this->sendCommand($socket, 'EHLO localhost', [250]);

            if (in_array($encryption, ['tls', 'starttls'], true)) {
                $this->sendCommand($socket, 'STARTTLS', [220]);

                if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                    throw new RuntimeException('Unable to enable STARTTLS encryption.');
                }

                $this->sendCommand($socket, 'EHLO localhost', [250]);
            }

            if ($username !== '') {
                $this->sendCommand($socket, 'AUTH LOGIN', [334]);
                $this->sendCommand($socket, base64_encode($username), [334]);
                $this->sendCommand($socket, base64_encode($password), [235]);
            }

            $this->sendCommand($socket, 'MAIL FROM:<' . $fromAddress . '>', [250]);
            $this->sendCommand($socket, 'RCPT TO:<' . $email . '>', [250, 251]);
            $this->sendCommand($socket, 'DATA', [354]);

            $headers = [
                'From: ' . $this->formatAddress($fromAddress, $fromName),
                'To: ' . $email,
                'Subject: ' . $this->encodeHeader($subject),
                'MIME-Version: 1.0',
                'Content-Type: text/plain; charset=UTF-8',
                'Content-Transfer-Encoding: 8bit',
            ];

            $payload = implode("\r\n", $headers)
                . "\r\n\r\n"
                . $this->escapeSmtpData($message)
                . "\r\n.";

            fwrite($socket, $payload . "\r\n");
            $this->assertReply($socket, [250]);
            $this->sendCommand($socket, 'QUIT', [221]);
        } finally {
            fclose($socket);
        }

        return true;
    }

    private function sendCommand($socket, string $command, array $expectedCodes): void
    {
        fwrite($socket, $command . "\r\n");
        $this->assertReply($socket, $expectedCodes);
    }

    private function assertReply($socket, array $expectedCodes): void
    {
        $response = $this->readResponse($socket);
        $code = (int) substr($response, 0, 3);

        if (!in_array($code, $expectedCodes, true)) {
            throw new RuntimeException('Unexpected SMTP response: ' . trim($response));
        }
    }

    private function readResponse($socket): string
    {
        $response = '';

        while (($line = fgets($socket, 512)) !== false) {
            $response .= $line;

            if (isset($line[3]) && $line[3] === ' ') {
                break;
            }
        }

        if ($response === '') {
            throw new RuntimeException('SMTP server returned an empty response.');
        }

        return $response;
    }

    private function formatAddress(string $email, string $name): string
    {
        $trimmedName = trim($name);

        if ($trimmedName === '') {
            return $email;
        }

        return $this->encodeHeader($trimmedName) . ' <' . $email . '>';
    }

    private function encodeHeader(string $value): string
    {
        return '=?UTF-8?B?' . base64_encode($value) . '?=';
    }

    private function escapeSmtpData(string $message): string
    {
        $normalized = str_replace(["\r\n", "\r"], "\n", $message);
        $lines = explode("\n", $normalized);

        foreach ($lines as &$line) {
            if (str_starts_with($line, '.')) {
                $line = '.' . $line;
            }
        }

        return implode("\r\n", $lines);
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

    private function logMailFailure(string $email, string $subject, string $reason): void
    {
        $directory = app('app.storage_path') . '/temp';

        if (!is_dir($directory)) {
            mkdir($directory, 0775, true);
        }

        $payload = sprintf(
            "[%s] TO: %s%sSUBJECT: %s%sERROR: %s%s%s",
            date('Y-m-d H:i:s'),
            $email,
            PHP_EOL,
            $subject,
            PHP_EOL,
            $reason,
            PHP_EOL,
            str_repeat('-', 60) . PHP_EOL
        );

        file_put_contents($directory . '/mail-error.log', $payload, FILE_APPEND);
    }
}
