<?php

namespace App\Models;

class PasswordReset extends BaseModel
{
    public function create(int $userId, string $tokenHash, string $expiresAt): bool
    {
        $this->deleteActiveForUser($userId);

        $statement = $this->connection()->prepare(
            'INSERT INTO password_reset_requests (
                user_id,
                token_hash,
                expires_at,
                created_at
            ) VALUES (
                :user_id,
                :token_hash,
                :expires_at,
                NOW()
            )'
        );

        return $statement->execute([
            'user_id' => $userId,
            'token_hash' => $tokenHash,
            'expires_at' => $expiresAt,
        ]);
    }

    public function findValidByTokenHash(string $tokenHash): ?array
    {
        $statement = $this->connection()->prepare(
            'SELECT *
             FROM password_reset_requests
             WHERE token_hash = :token_hash
               AND used_at IS NULL
               AND expires_at >= NOW()
             LIMIT 1'
        );
        $statement->execute(['token_hash' => $tokenHash]);

        return $statement->fetch() ?: null;
    }

    public function markUsed(int $id): bool
    {
        $statement = $this->connection()->prepare(
            'UPDATE password_reset_requests
             SET used_at = NOW()
             WHERE id = :id'
        );

        return $statement->execute(['id' => $id]);
    }

    public function deleteActiveForUser(int $userId): bool
    {
        $statement = $this->connection()->prepare(
            'DELETE FROM password_reset_requests
             WHERE user_id = :user_id
               AND used_at IS NULL'
        );

        return $statement->execute(['user_id' => $userId]);
    }
}
