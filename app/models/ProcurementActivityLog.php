<?php

namespace App\Models;

class ProcurementActivityLog extends BaseModel
{
    public function create(array $data): int
    {
        $statement = $this->connection()->prepare(
            'INSERT INTO procurement_activity_logs (
                parent_procurement_id,
                document_type,
                document_id,
                sequence_stage,
                action_type,
                acted_by,
                action_note
            ) VALUES (
                :parent_procurement_id,
                :document_type,
                :document_id,
                :sequence_stage,
                :action_type,
                :acted_by,
                :action_note
            )'
        );

        $statement->execute([
            'parent_procurement_id' => $data['parent_procurement_id'],
            'document_type' => $data['document_type'],
            'document_id' => $data['document_id'] ?? null,
            'sequence_stage' => $data['sequence_stage'],
            'action_type' => $data['action_type'],
            'acted_by' => $data['acted_by'],
            'action_note' => $data['action_note'] ?? null,
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
             INNER JOIN users u ON u.id = l.acted_by
             WHERE l.parent_procurement_id = :parent_procurement_id
             ORDER BY l.created_at DESC, l.id DESC'
        );

        $statement->execute(['parent_procurement_id' => $parentId]);

        return $statement->fetchAll() ?: [];
    }
}
