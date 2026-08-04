<?php

namespace App\Models;

class ProcurementActivityLog extends BaseModel
{
    public function create(array $data): int
    {
        $statement = $this->connection()->prepare(
            'INSERT INTO procurement_activity_logs (
                parent_procurement_id,
                user_id,
                action_type,
                document_type,
                document_id,
                before_snapshot,
                after_snapshot,
                reason,
                file_hash,
                approval_reference
            ) VALUES (
                :parent_procurement_id,
                :user_id,
                :action_type,
                :document_type,
                :document_id,
                :before_snapshot,
                :after_snapshot,
                :reason,
                :file_hash,
                :approval_reference
            )'
        );

        $statement->execute([
            'parent_procurement_id' => $data['parent_procurement_id'],
            'user_id' => $data['user_id'],
            'action_type' => $data['action_type'],
            'document_type' => $data['document_type'],
            'document_id' => $data['document_id'] ?? null,
            'before_snapshot' => $data['before_snapshot'] ?? null,
            'after_snapshot' => $data['after_snapshot'] ?? null,
            'reason' => $data['reason'] ?? null,
            'file_hash' => $data['file_hash'] ?? null,
            'approval_reference' => $data['approval_reference'] ?? null,
        ]);

        return (int) $this->connection()->lastInsertId();
    }

    public function findByParent(int $parentId): array
    {
        $statement = $this->connection()->prepare(
            'SELECT l.*,
                    u.username,
                    u.firstname,
                    u.lastname
             FROM procurement_activity_logs l
             INNER JOIN users u ON u.id = l.user_id
             WHERE l.parent_procurement_id = :parent_procurement_id
             ORDER BY l.created_at DESC, l.id DESC'
        );

        $statement->execute(['parent_procurement_id' => $parentId]);

        return $statement->fetchAll() ?: [];
    }

    public function hasAnyUserReferences(int $userId): bool
    {
        $statement = $this->connection()->prepare(
            'SELECT COUNT(*)
             FROM procurement_activity_logs
             WHERE user_id = :user_id'
        );
        $statement->execute(['user_id' => $userId]);

        return (int) $statement->fetchColumn() > 0;
    }
}
