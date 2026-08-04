<?php

namespace App\Models;

class LoginAttemptLog extends BaseModel
{
    public function create(array $data): int
    {
        $statement = $this->connection()->prepare(
            'INSERT INTO login_attempt_logs (
                user_id,
                username_entered,
                event_type,
                outcome,
                failure_reason,
                message,
                ip_address,
                user_agent,
                request_method,
                request_uri,
                context
            ) VALUES (
                :user_id,
                :username_entered,
                :event_type,
                :outcome,
                :failure_reason,
                :message,
                :ip_address,
                :user_agent,
                :request_method,
                :request_uri,
                :context
            )'
        );

        $statement->execute([
            'user_id' => $data['user_id'] ?? null,
            'username_entered' => $data['username_entered'] ?? null,
            'event_type' => $data['event_type'],
            'outcome' => $data['outcome'],
            'failure_reason' => $data['failure_reason'] ?? null,
            'message' => $data['message'] ?? null,
            'ip_address' => $data['ip_address'] ?? null,
            'user_agent' => $data['user_agent'] ?? null,
            'request_method' => $data['request_method'] ?? null,
            'request_uri' => $data['request_uri'] ?? null,
            'context' => $data['context'] ?? null,
        ]);

        return (int) $this->connection()->lastInsertId();
    }

    public function latest(array $filters = [], int $limit = 100): array
    {
        $conditions = [];
        $parameters = [];

        if (($filters['search'] ?? '') !== '') {
            $conditions[] = '(l.username_entered LIKE :search OR u.username LIKE :search OR l.ip_address LIKE :search)';
            $parameters['search'] = '%' . $filters['search'] . '%';
        }

        if (($filters['outcome'] ?? '') !== '') {
            $conditions[] = 'l.outcome = :outcome';
            $parameters['outcome'] = $filters['outcome'];
        }

        if (($filters['event_type'] ?? '') !== '') {
            $conditions[] = 'l.event_type = :event_type';
            $parameters['event_type'] = $filters['event_type'];
        }

        if (($filters['failure_reason'] ?? '') !== '') {
            $conditions[] = 'l.failure_reason = :failure_reason';
            $parameters['failure_reason'] = $filters['failure_reason'];
        }

        $where = $conditions === [] ? '' : 'WHERE ' . implode(' AND ', $conditions);
        $limit = max(1, min($limit, 500));

        $statement = $this->connection()->prepare(
            'SELECT l.*,
                    u.username,
                    u.firstname,
                    u.lastname,
                    u.email,
                    u.role,
                    u.is_active
             FROM login_attempt_logs l
             LEFT JOIN users u ON u.id = l.user_id
             ' . $where . '
             ORDER BY l.created_at DESC, l.id DESC
             LIMIT ' . $limit
        );

        $statement->execute($parameters);

        return $statement->fetchAll() ?: [];
    }
}
