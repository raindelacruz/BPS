<?php

namespace App\Models;

class Notice extends BaseModel
{
    public const WORKFLOW_TYPES = ['bid', 'sbb', 'resolution', 'award', 'contract', 'proceed'];

    public function create(array $data): int
    {
        $statement = $this->connection()->prepare(
            'INSERT INTO notices (
                title,
                reference_code,
                type,
                file_path,
                upload_date,
                start_date,
                end_date,
                uploaded_by,
                description,
                is_archived,
                status,
                region,
                branch,
                procurement_type,
                archived_at
            ) VALUES (
                :title,
                :reference_code,
                :type,
                :file_path,
                NOW(),
                :start_date,
                :end_date,
                :uploaded_by,
                :description,
                :is_archived,
                :status,
                :region,
                :branch,
                :procurement_type,
                :archived_at
            )'
        );

        $statement->execute([
            'title' => $data['title'],
            'reference_code' => $data['reference_code'],
            'type' => $data['type'],
            'file_path' => $data['file_path'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'uploaded_by' => $data['uploaded_by'],
            'description' => $data['description'],
            'is_archived' => (int) ($data['is_archived'] ?? 0),
            'status' => $data['status'],
            'region' => $data['region'],
            'branch' => $data['branch'] ?? null,
            'procurement_type' => $data['procurement_type'],
            'archived_at' => $data['archived_at'] ?? null,
        ]);

        return (int) $this->connection()->lastInsertId();
    }

    public function updateById(int $id, array $data): bool
    {
        $statement = $this->connection()->prepare(
            'UPDATE notices
             SET title = :title,
                 reference_code = :reference_code,
                 file_path = :file_path,
                 start_date = :start_date,
                 end_date = :end_date,
                 description = :description,
                 status = :status,
                 region = :region,
                 branch = :branch,
                 procurement_type = :procurement_type
             WHERE id = :id'
        );

        return $statement->execute([
            'id' => $id,
            'title' => $data['title'],
            'reference_code' => $data['reference_code'],
            'file_path' => $data['file_path'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'description' => $data['description'],
            'status' => $data['status'],
            'region' => $data['region'],
            'branch' => $data['branch'] ?? null,
            'procurement_type' => $data['procurement_type'],
        ]);
    }

    public function deleteById(int $id): bool
    {
        $statement = $this->connection()->prepare('DELETE FROM notices WHERE id = :id');

        return $statement->execute(['id' => $id]);
    }

    public function reassignUploader(int $fromUserId, int $toUserId): bool
    {
        $statement = $this->connection()->prepare(
            'UPDATE notices
             SET uploaded_by = :to_user_id
             WHERE uploaded_by = :from_user_id'
        );

        return $statement->execute([
            'from_user_id' => $fromUserId,
            'to_user_id' => $toUserId,
        ]);
    }

    public function findById(int $id): ?array
    {
        $statement = $this->connection()->prepare(
            'SELECT n.*, u.username AS uploader_username, u.firstname, u.lastname
             FROM notices n
             INNER JOIN users u ON u.id = n.uploaded_by
             WHERE n.id = :id
             LIMIT 1'
        );

        $statement->execute(['id' => $id]);

        return $statement->fetch() ?: null;
    }

    public function findByReferenceCode(string $referenceCode): array
    {
        $statement = $this->connection()->prepare(
            'SELECT n.*, u.username AS uploader_username, u.firstname, u.lastname
             FROM notices n
             INNER JOIN users u ON u.id = n.uploaded_by
             WHERE n.reference_code = :reference_code
             ORDER BY
                CASE n.type
                    WHEN "bid" THEN 1
                    WHEN "sbb" THEN 2
                    WHEN "resolution" THEN 3
                    WHEN "award" THEN 4
                    WHEN "contract" THEN 5
                    WHEN "proceed" THEN 6
                    ELSE 7
                END,
                n.start_date ASC,
                n.id ASC'
        );

        $statement->execute(['reference_code' => $referenceCode]);

        return $statement->fetchAll() ?: [];
    }

    public function referenceCodeExistsForBid(string $referenceCode, ?int $ignoreId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM notices WHERE type = :type AND reference_code = :reference_code';
        $params = [
            'type' => 'bid',
            'reference_code' => $referenceCode,
        ];

        if ($ignoreId !== null) {
            $sql .= ' AND id <> :ignore_id';
            $params['ignore_id'] = $ignoreId;
        }

        $statement = $this->connection()->prepare($sql);
        $statement->execute($params);

        return (int) $statement->fetchColumn() > 0;
    }

    public function findByUploader(int $userId): array
    {
        $statement = $this->connection()->prepare(
            'SELECT n.*, u.username AS uploader_username, u.firstname, u.lastname
             FROM notices n
             INNER JOIN users u ON u.id = n.uploaded_by
             WHERE n.uploaded_by = :uploaded_by
             ORDER BY n.upload_date DESC, n.id DESC'
        );

        $statement->execute(['uploaded_by' => $userId]);

        return $statement->fetchAll() ?: [];
    }

    public function findAllBids(): array
    {
        $statement = $this->connection()->prepare(
            'SELECT n.*, u.username AS uploader_username, u.firstname, u.lastname
             FROM notices n
             INNER JOIN users u ON u.id = n.uploaded_by
             WHERE n.type = :type
             ORDER BY n.upload_date DESC, n.id DESC'
        );

        $statement->execute(['type' => 'bid']);

        return $statement->fetchAll() ?: [];
    }

    public function findArchivedBids(): array
    {
        $statement = $this->connection()->prepare(
            'SELECT n.*, u.username AS uploader_username, u.firstname, u.lastname
             FROM notices n
             INNER JOIN users u ON u.id = n.uploaded_by
             WHERE n.type = :type
               AND n.is_archived = 1
             ORDER BY n.archived_at DESC, n.id DESC'
        );

        $statement->execute([
            'type' => 'bid',
        ]);

        return $statement->fetchAll() ?: [];
    }

    public function findActiveNonArchivedBids(): array
    {
        $statement = $this->connection()->prepare(
            'SELECT n.*, u.username AS uploader_username, u.firstname, u.lastname
             FROM notices n
             INNER JOIN users u ON u.id = n.uploaded_by
             WHERE n.type = :type
               AND n.status = :status
               AND n.is_archived = 0
             ORDER BY n.start_date DESC, n.id DESC'
        );

        $statement->execute([
            'type' => 'bid',
            'status' => 'active',
        ]);

        return $statement->fetchAll() ?: [];
    }

    public function hasActiveNonArchivedType(
        string $referenceCode,
        string $type,
        ?string $region = null,
        ?int $ignoreId = null
    ): bool {
        $sql = 'SELECT COUNT(*) FROM notices
                WHERE reference_code = :reference_code
                  AND type = :type
                  AND is_archived = 0
                  AND status <> :archived_status';
        $params = [
            'reference_code' => $referenceCode,
            'type' => $type,
            'archived_status' => 'archived',
        ];

        if ($region !== null) {
            $sql .= ' AND region = :region';
            $params['region'] = $region;
        }

        if ($ignoreId !== null) {
            $sql .= ' AND id <> :ignore_id';
            $params['ignore_id'] = $ignoreId;
        }

        $statement = $this->connection()->prepare($sql);
        $statement->execute($params);

        return (int) $statement->fetchColumn() > 0;
    }

    public function findWorkflowBidByReferenceCode(string $referenceCode): ?array
    {
        $statement = $this->connection()->prepare(
            'SELECT n.*, u.username AS uploader_username, u.firstname, u.lastname
             FROM notices n
             INNER JOIN users u ON u.id = n.uploaded_by
             WHERE n.reference_code = :reference_code
               AND n.type = :type
             ORDER BY n.id ASC
             LIMIT 1'
        );

        $statement->execute([
            'reference_code' => $referenceCode,
            'type' => 'bid',
        ]);

        return $statement->fetch() ?: null;
    }

    public function findPublicActiveBids(?string $search = null, ?string $region = null, ?string $procurementType = null): array
    {
        $sql = 'SELECT n.*, u.username AS uploader_username, u.firstname, u.lastname
                FROM notices n
                INNER JOIN users u ON u.id = n.uploaded_by
                WHERE n.type = :type
                  AND n.status = :status
                  AND n.is_archived = 0';
        $params = [
            'type' => 'bid',
            'status' => 'active',
        ];

        if ($search !== null && $search !== '') {
            $sql .= ' AND (n.title LIKE :search OR n.reference_code LIKE :search OR n.description LIKE :search)';
            $params['search'] = '%' . $search . '%';
        }

        if ($region !== null && $region !== '') {
            $sql .= ' AND n.region = :region';
            $params['region'] = $region;
        }

        if ($procurementType !== null && $procurementType !== '') {
            $sql .= ' AND n.procurement_type = :procurement_type';
            $params['procurement_type'] = $procurementType;
        }

        $sql .= ' ORDER BY n.start_date DESC, n.id DESC';

        $statement = $this->connection()->prepare($sql);
        $statement->execute($params);

        return $statement->fetchAll() ?: [];
    }

    public function updateStatus(int $id, string $status): bool
    {
        $statement = $this->connection()->prepare(
            'UPDATE notices SET status = :status WHERE id = :id'
        );

        return $statement->execute([
            'id' => $id,
            'status' => $status,
        ]);
    }

    public function updateArchiveStateByReferenceCode(string $referenceCode, bool $isArchived, ?string $archivedAt = null): bool
    {
        $statement = $this->connection()->prepare(
            'UPDATE notices
             SET is_archived = :is_archived,
                 status = :status,
                 archived_at = :archived_at
             WHERE reference_code = :reference_code'
        );

        return $statement->execute([
            'reference_code' => $referenceCode,
            'is_archived' => (int) $isArchived,
            'status' => $isArchived ? 'archived' : 'pending',
            'archived_at' => $archivedAt,
        ]);
    }

    public function updateLifecycleFieldsById(int $id, string $status, bool $isArchived = false, ?string $archivedAt = null): bool
    {
        $statement = $this->connection()->prepare(
            'UPDATE notices
             SET status = :status,
                 is_archived = :is_archived,
                 archived_at = :archived_at
             WHERE id = :id'
        );

        return $statement->execute([
            'id' => $id,
            'status' => $status,
            'is_archived' => (int) $isArchived,
            'archived_at' => $archivedAt,
        ]);
    }
}
