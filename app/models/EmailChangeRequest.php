<?php

namespace App\Models;

class EmailChangeRequest extends BaseModel
{
    public function create(array $data): int
    {
        $statement = $this->connection()->prepare(
            'INSERT INTO email_change_requests (
                user_id,
                current_email,
                new_email,
                token,
                status,
                created_at,
                expires_at
            ) VALUES (
                :user_id,
                :current_email,
                :new_email,
                :token,
                :status,
                NOW(),
                :expires_at
            )'
        );

        $statement->execute([
            'user_id' => $data['user_id'],
            'current_email' => $data['current_email'],
            'new_email' => $data['new_email'],
            'token' => $data['token'],
            'status' => $data['status'] ?? 'pending',
            'expires_at' => $data['expires_at'],
        ]);

        return (int) $this->connection()->lastInsertId();
    }

    public function findPendingByToken(string $token): ?array
    {
        $statement = $this->connection()->prepare(
            'SELECT * FROM email_change_requests
             WHERE token = :token
               AND status = :status
               AND expires_at >= NOW()
             LIMIT 1'
        );

        $statement->execute([
            'token' => $token,
            'status' => 'pending',
        ]);

        return $statement->fetch() ?: null;
    }

    public function cancelPendingForUser(int $userId): bool
    {
        $statement = $this->connection()->prepare(
            'UPDATE email_change_requests
             SET status = :status
             WHERE user_id = :user_id
               AND status = :pending_status'
        );

        return $statement->execute([
            'user_id' => $userId,
            'status' => 'cancelled',
            'pending_status' => 'pending',
        ]);
    }

    public function markCompleted(int $id): bool
    {
        $statement = $this->connection()->prepare(
            'UPDATE email_change_requests
             SET status = :status
             WHERE id = :id'
        );

        return $statement->execute([
            'id' => $id,
            'status' => 'completed',
        ]);
    }

    public function markCancelled(int $id): bool
    {
        $statement = $this->connection()->prepare(
            'UPDATE email_change_requests
             SET status = :status
             WHERE id = :id'
        );

        return $statement->execute([
            'id' => $id,
            'status' => 'cancelled',
        ]);
    }
}
