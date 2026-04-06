<?php

namespace App\Models;

class ParentProcurement extends BaseModel
{
    public function create(array $data): int
    {
        $statement = $this->connection()->prepare(
            'INSERT INTO parent_procurement (
                reference_number,
                procurement_title,
                abc,
                mode_of_procurement,
                posting_date,
                bid_submission_deadline,
                description,
                status,
                current_stage,
                is_archived,
                archived_at,
                region,
                branch,
                created_by,
                updated_by
            ) VALUES (
                :reference_number,
                :procurement_title,
                :abc,
                :mode_of_procurement,
                :posting_date,
                :bid_submission_deadline,
                :description,
                :status,
                :current_stage,
                :is_archived,
                :archived_at,
                :region,
                :branch,
                :created_by,
                :updated_by
            )'
        );

        $statement->execute([
            'reference_number' => $data['reference_number'],
            'procurement_title' => $data['procurement_title'],
            'abc' => $data['abc'],
            'mode_of_procurement' => $data['mode_of_procurement'],
            'posting_date' => $data['posting_date'],
            'bid_submission_deadline' => $data['bid_submission_deadline'],
            'description' => $data['description'],
            'status' => $data['status'],
            'current_stage' => $data['current_stage'],
            'is_archived' => (int) ($data['is_archived'] ?? 0),
            'archived_at' => $data['archived_at'] ?? null,
            'region' => $data['region'],
            'branch' => $data['branch'] ?? null,
            'created_by' => $data['created_by'],
            'updated_by' => $data['updated_by'],
        ]);

        return (int) $this->connection()->lastInsertId();
    }

    public function updateById(int $id, array $data): bool
    {
        $statement = $this->connection()->prepare(
            'UPDATE parent_procurement
             SET reference_number = :reference_number,
                 procurement_title = :procurement_title,
                 abc = :abc,
                 mode_of_procurement = :mode_of_procurement,
                 posting_date = :posting_date,
                 bid_submission_deadline = :bid_submission_deadline,
                 description = :description,
                 status = :status,
                 current_stage = :current_stage,
                 is_archived = :is_archived,
                 archived_at = :archived_at,
                 region = :region,
                 branch = :branch,
                 updated_by = :updated_by
             WHERE id = :id'
        );

        return $statement->execute([
            'id' => $id,
            'reference_number' => $data['reference_number'],
            'procurement_title' => $data['procurement_title'],
            'abc' => $data['abc'],
            'mode_of_procurement' => $data['mode_of_procurement'],
            'posting_date' => $data['posting_date'],
            'bid_submission_deadline' => $data['bid_submission_deadline'],
            'description' => $data['description'],
            'status' => $data['status'],
            'current_stage' => $data['current_stage'],
            'is_archived' => (int) ($data['is_archived'] ?? 0),
            'archived_at' => $data['archived_at'] ?? null,
            'region' => $data['region'],
            'branch' => $data['branch'] ?? null,
            'updated_by' => $data['updated_by'],
        ]);
    }

    public function updateStageAndStatus(int $id, string $stage, string $status): bool
    {
        $statement = $this->connection()->prepare(
            'UPDATE parent_procurement
             SET current_stage = :current_stage,
                 status = :status
             WHERE id = :id'
        );

        return $statement->execute([
            'id' => $id,
            'current_stage' => $stage,
            'status' => $status,
        ]);
    }

    public function findById(int $id): ?array
    {
        $statement = $this->connection()->prepare(
            'SELECT p.*,
                    creator.username AS creator_username,
                    creator.firstname AS creator_firstname,
                    creator.lastname AS creator_lastname
             FROM parent_procurement p
             INNER JOIN users creator ON creator.id = p.created_by
             WHERE p.id = :id
             LIMIT 1'
        );

        $statement->execute(['id' => $id]);

        return $statement->fetch() ?: null;
    }

    public function findAll(): array
    {
        $statement = $this->connection()->query(
            'SELECT p.*,
                    creator.username AS creator_username,
                    creator.firstname AS creator_firstname,
                    creator.lastname AS creator_lastname
             FROM parent_procurement p
             INNER JOIN users creator ON creator.id = p.created_by
             ORDER BY p.created_at DESC, p.id DESC'
        );

        return $statement->fetchAll() ?: [];
    }

    public function findByCreator(int $userId): array
    {
        $statement = $this->connection()->prepare(
            'SELECT p.*,
                    creator.username AS creator_username,
                    creator.firstname AS creator_firstname,
                    creator.lastname AS creator_lastname
             FROM parent_procurement p
             INNER JOIN users creator ON creator.id = p.created_by
             WHERE p.created_by = :created_by
             ORDER BY p.created_at DESC, p.id DESC'
        );

        $statement->execute(['created_by' => $userId]);

        return $statement->fetchAll() ?: [];
    }

    public function findByBranch(string $branch): array
    {
        $statement = $this->connection()->prepare(
            'SELECT p.*,
                    creator.username AS creator_username,
                    creator.firstname AS creator_firstname,
                    creator.lastname AS creator_lastname
             FROM parent_procurement p
             INNER JOIN users creator ON creator.id = p.created_by
             WHERE p.branch = :branch
             ORDER BY p.created_at DESC, p.id DESC'
        );

        $statement->execute(['branch' => $branch]);

        return $statement->fetchAll() ?: [];
    }

    public function findPublic(?string $search = null, ?string $region = null, ?string $modeOfProcurement = null): array
    {
        $sql = 'SELECT *
                FROM parent_procurement
                WHERE is_archived = 0
                  AND status <> :pending_status';
        $params = [
            'pending_status' => 'pending',
        ];

        if ($search !== null && $search !== '') {
            $sql .= ' AND (procurement_title LIKE :search OR reference_number LIKE :search OR description LIKE :search)';
            $params['search'] = '%' . $search . '%';
        }

        if ($region !== null && $region !== '') {
            $sql .= ' AND region = :region';
            $params['region'] = $region;
        }

        if ($modeOfProcurement !== null && $modeOfProcurement !== '') {
            $sql .= ' AND mode_of_procurement = :mode_of_procurement';
            $params['mode_of_procurement'] = $modeOfProcurement;
        }

        $sql .= ' ORDER BY created_at DESC, id DESC';
        $statement = $this->connection()->prepare($sql);
        $statement->execute($params);

        return $statement->fetchAll() ?: [];
    }

    public function referenceNumberExists(string $referenceNumber, ?int $ignoreId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM parent_procurement WHERE reference_number = :reference_number';
        $params = ['reference_number' => $referenceNumber];

        if ($ignoreId !== null) {
            $sql .= ' AND id <> :ignore_id';
            $params['ignore_id'] = $ignoreId;
        }

        $statement = $this->connection()->prepare($sql);
        $statement->execute($params);

        return (int) $statement->fetchColumn() > 0;
    }
}
