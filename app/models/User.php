<?php

namespace App\Models;

class User extends BaseModel
{
    public function all(): array
    {
        $statement = $this->connection()->query(
            'SELECT *
             FROM users
             ORDER BY created_at DESC, id DESC'
        );

        return $statement->fetchAll() ?: [];
    }

    public function findById(int $id): ?array
    {
        $statement = $this->connection()->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
        $statement->execute(['id' => $id]);

        return $statement->fetch() ?: null;
    }

    public function findByUsername(string $username): ?array
    {
        $statement = $this->connection()->prepare('SELECT * FROM users WHERE username = :username LIMIT 1');
        $statement->execute(['username' => $username]);

        return $statement->fetch() ?: null;
    }

    public function findByEmail(string $email): ?array
    {
        $statement = $this->connection()->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $statement->execute(['email' => $email]);

        return $statement->fetch() ?: null;
    }

    public function usernameExists(string $username): bool
    {
        return $this->findByUsername($username) !== null;
    }

    public function usernameExistsForOther(string $username, int $ignoreId): bool
    {
        $statement = $this->connection()->prepare(
            'SELECT COUNT(*) FROM users WHERE username = :username AND id <> :id'
        );
        $statement->execute([
            'username' => $username,
            'id' => $ignoreId,
        ]);

        return (int) $statement->fetchColumn() > 0;
    }

    public function emailExists(string $email): bool
    {
        return $this->findByEmail($email) !== null;
    }

    public function emailExistsForOther(string $email, int $ignoreId): bool
    {
        $statement = $this->connection()->prepare(
            'SELECT COUNT(*) FROM users WHERE email = :email AND id <> :id'
        );
        $statement->execute([
            'email' => $email,
            'id' => $ignoreId,
        ]);

        return (int) $statement->fetchColumn() > 0;
    }

    public function create(array $data): int
    {
        $statement = $this->connection()->prepare(
            'INSERT INTO users (
                username,
                firstname,
                middle_initial,
                lastname,
                region,
                branch,
                password,
                role,
                email,
                verification_token,
                verification_code,
                token_expiry,
                is_verified,
                is_active,
                created_at,
                updated_at
            ) VALUES (
                :username,
                :firstname,
                :middle_initial,
                :lastname,
                :region,
                :branch,
                :password,
                :role,
                :email,
                :verification_token,
                :verification_code,
                :token_expiry,
                :is_verified,
                :is_active,
                NOW(),
                NOW()
            )'
        );

        $statement->execute([
            'username' => $data['username'],
            'firstname' => $data['firstname'],
            'middle_initial' => $data['middle_initial'] ?: null,
            'lastname' => $data['lastname'],
            'region' => $data['region'],
            'branch' => $data['branch'] ?: null,
            'password' => $data['password'],
            'role' => $data['role'],
            'email' => $data['email'],
            'verification_token' => $data['verification_token'] ?? null,
            'verification_code' => $data['verification_code'],
            'token_expiry' => $data['token_expiry'],
            'is_verified' => (int) $data['is_verified'],
            'is_active' => (int) $data['is_active'],
        ]);

        return (int) $this->connection()->lastInsertId();
    }

    public function markVerified(int $userId): bool
    {
        $statement = $this->connection()->prepare(
            'UPDATE users
             SET is_verified = 1,
                 is_active = 1,
                 verification_code = NULL,
                 token_expiry = NULL,
                 updated_at = NOW()
             WHERE id = :id'
        );

        return $statement->execute(['id' => $userId]);
    }

    public function updateById(int $id, array $data): bool
    {
        $statement = $this->connection()->prepare(
            'UPDATE users
             SET username = :username,
                 firstname = :firstname,
                 middle_initial = :middle_initial,
                 lastname = :lastname,
                 region = :region,
                 branch = :branch,
                 role = :role,
                 email = :email,
                 updated_at = NOW()
             WHERE id = :id'
        );

        return $statement->execute([
            'id' => $id,
            'username' => $data['username'],
            'firstname' => $data['firstname'],
            'middle_initial' => $data['middle_initial'] ?: null,
            'lastname' => $data['lastname'],
            'region' => $data['region'],
            'branch' => $data['branch'] ?: null,
            'role' => $data['role'],
            'email' => $data['email'],
        ]);
    }

    public function updateEmailById(int $id, string $email): bool
    {
        $statement = $this->connection()->prepare(
            'UPDATE users
             SET email = :email,
                 updated_at = NOW()
             WHERE id = :id'
        );

        return $statement->execute([
            'id' => $id,
            'email' => $email,
        ]);
    }

    public function updateActiveState(int $id, bool $isActive): bool
    {
        $statement = $this->connection()->prepare(
            'UPDATE users
             SET is_active = :is_active,
                 updated_at = NOW()
             WHERE id = :id'
        );

        return $statement->execute([
            'id' => $id,
            'is_active' => (int) $isActive,
        ]);
    }

    public function updatePassword(int $id, string $passwordHash): bool
    {
        $statement = $this->connection()->prepare(
            'UPDATE users
             SET password = :password,
                 updated_at = NOW()
             WHERE id = :id'
        );

        return $statement->execute([
            'id' => $id,
            'password' => $passwordHash,
        ]);
    }

    public function deleteById(int $id): bool
    {
        $statement = $this->connection()->prepare('DELETE FROM users WHERE id = :id');

        return $statement->execute(['id' => $id]);
    }
}
