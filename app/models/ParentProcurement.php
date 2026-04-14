<?php

namespace App\Models;

class ParentProcurement extends BaseModel
{
    public function create(array $data): int
    {
        $statement = $this->connection()->prepare(
            'INSERT INTO parent_procurement (
                procurement_mode,
                reference_number,
                procurement_title,
                abc,
                mode_of_procurement,
                posting_date,
                bid_submission_deadline,
                description,
                posting_status,
                current_stage,
                archived_at,
                archive_reason,
                archived_by,
                archive_approval_reference,
                archive_approved_by,
                archive_approved_at,
                category,
                end_user_unit,
                region,
                branch,
                created_by,
                updated_by
            ) VALUES (
                :procurement_mode,
                :reference_number,
                :procurement_title,
                :abc,
                :mode_of_procurement,
                :posting_date,
                :bid_submission_deadline,
                :description,
                :posting_status,
                :current_stage,
                :archived_at,
                :archive_reason,
                :archived_by,
                :archive_approval_reference,
                :archive_approved_by,
                :archive_approved_at,
                :category,
                :end_user_unit,
                :region,
                :branch,
                :created_by,
                :updated_by
            )'
        );

        $statement->execute([
            'procurement_mode' => $data['procurement_mode'],
            'reference_number' => $data['reference_number'],
            'procurement_title' => $data['procurement_title'],
            'abc' => $data['abc'],
            'mode_of_procurement' => $data['procurement_mode'],
            'posting_date' => $data['posting_date'],
            'bid_submission_deadline' => $data['bid_submission_deadline'],
            'description' => $data['description'],
            'posting_status' => $data['posting_status'],
            'current_stage' => $data['current_stage'],
            'archived_at' => $data['archived_at'] ?? null,
            'archive_reason' => $data['archive_reason'] ?? null,
            'archived_by' => $data['archived_by'] ?? null,
            'archive_approval_reference' => $data['archive_approval_reference'] ?? null,
            'archive_approved_by' => $data['archive_approved_by'] ?? null,
            'archive_approved_at' => $data['archive_approved_at'] ?? null,
            'category' => $data['category'] ?? null,
            'end_user_unit' => $data['end_user_unit'] ?? null,
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
                 procurement_mode = :procurement_mode,
                 mode_of_procurement = :mode_of_procurement,
                 posting_date = :posting_date,
                 bid_submission_deadline = :bid_submission_deadline,
                 description = :description,
                 posting_status = :posting_status,
                 current_stage = :current_stage,
                 archived_at = :archived_at,
                 archive_reason = :archive_reason,
                 archived_by = :archived_by,
                 archive_approval_reference = :archive_approval_reference,
                 archive_approved_by = :archive_approved_by,
                 archive_approved_at = :archive_approved_at,
                 category = :category,
                 end_user_unit = :end_user_unit,
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
            'procurement_mode' => $data['procurement_mode'],
            'mode_of_procurement' => $data['procurement_mode'],
            'posting_date' => $data['posting_date'],
            'bid_submission_deadline' => $data['bid_submission_deadline'],
            'description' => $data['description'],
            'posting_status' => $data['posting_status'],
            'current_stage' => $data['current_stage'],
            'archived_at' => $data['archived_at'] ?? null,
            'archive_reason' => $data['archive_reason'] ?? null,
            'archived_by' => $data['archived_by'] ?? null,
            'archive_approval_reference' => $data['archive_approval_reference'] ?? null,
            'archive_approved_by' => $data['archive_approved_by'] ?? null,
            'archive_approved_at' => $data['archive_approved_at'] ?? null,
            'category' => $data['category'] ?? null,
            'end_user_unit' => $data['end_user_unit'] ?? null,
            'region' => $data['region'],
            'branch' => $data['branch'] ?? null,
            'updated_by' => $data['updated_by'],
        ]);
    }

    public function updateWorkflowAndPostingState(int $id, string $stage, string $postingStatus): bool
    {
        $statement = $this->connection()->prepare(
            'UPDATE parent_procurement
             SET current_stage = :current_stage,
                 posting_status = :posting_status,
                 updated_at = NOW()
             WHERE id = :id'
        );

        return $statement->execute([
            'id' => $id,
            'current_stage' => $stage,
            'posting_status' => $postingStatus,
        ]);
    }

    public function updateArchiveState(
        int $id,
        string $archivedAt,
        string $postingStatus,
        string $archiveReason,
        string $approvalReference,
        int $archivedBy,
        int $approvedBy
    ): bool
    {
        $statement = $this->connection()->prepare(
            'UPDATE parent_procurement
             SET posting_status = :posting_status,
                 archived_at = :archived_at,
                 archive_reason = :archive_reason,
                 archived_by = :archived_by,
                 archive_approval_reference = :archive_approval_reference,
                 archive_approved_by = :archive_approved_by,
                 archive_approved_at = :archive_approved_at,
                 updated_by = :updated_by
             WHERE id = :id'
        );

        return $statement->execute([
            'id' => $id,
            'posting_status' => $postingStatus,
            'archived_at' => $archivedAt,
            'archive_reason' => $archiveReason,
            'archived_by' => $archivedBy,
            'archive_approval_reference' => $approvalReference,
            'archive_approved_by' => $approvedBy,
            'archive_approved_at' => $archivedAt,
            'updated_by' => $archivedBy,
        ]);
    }

    public function updateOperationalState(
        int $id,
        ?string $category,
        ?string $endUserUnit,
        int $updatedBy
    ): bool {
        $statement = $this->connection()->prepare(
            'UPDATE parent_procurement
             SET category = :category,
                 end_user_unit = :end_user_unit,
                 updated_by = :updated_by
             WHERE id = :id'
        );

        return $statement->execute([
            'id' => $id,
            'category' => $category,
            'end_user_unit' => $endUserUnit,
            'updated_by' => $updatedBy,
        ]);
    }

    public function findById(int $id): ?array
    {
        $statement = $this->connection()->prepare(
            'SELECT p.*,
                    p.procurement_mode AS mode_of_procurement,
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
                    p.procurement_mode AS mode_of_procurement,
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
                    p.procurement_mode AS mode_of_procurement,
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
                    p.procurement_mode AS mode_of_procurement,
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
        $sql = 'SELECT p.*,
                       p.procurement_mode AS mode_of_procurement
                FROM parent_procurement p
                WHERE 1 = 1';
        $params = [];

        if ($search !== null && $search !== '') {
            $sql .= ' AND (procurement_title LIKE :search OR reference_number LIKE :search OR description LIKE :search)';
            $params['search'] = '%' . $search . '%';
        }

        if ($region !== null && $region !== '') {
            $sql .= ' AND region = :region';
            $params['region'] = $region;
        }

        if ($modeOfProcurement !== null && $modeOfProcurement !== '') {
            $sql .= ' AND p.procurement_mode = :procurement_mode';
            $params['procurement_mode'] = $modeOfProcurement;
        }

        $sql .= ' ORDER BY p.created_at DESC, p.id DESC';
        $statement = $this->connection()->prepare($sql);
        $statement->execute($params);

        return $statement->fetchAll() ?: [];
    }

    public function findByReferenceNumber(string $referenceNumber): ?array
    {
        $statement = $this->connection()->prepare(
            'SELECT p.*,
                    p.procurement_mode AS mode_of_procurement,
                    creator.username AS creator_username,
                    creator.firstname AS creator_firstname,
                    creator.lastname AS creator_lastname
             FROM parent_procurement p
             INNER JOIN users creator ON creator.id = p.created_by
             WHERE p.reference_number = :reference_number
             LIMIT 1'
        );

        $statement->execute(['reference_number' => $referenceNumber]);

        return $statement->fetch() ?: null;
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

    public function hasAnyOwnershipReferences(int $userId): bool
    {
        $statement = $this->connection()->prepare(
            'SELECT COUNT(*)
             FROM parent_procurement
             WHERE created_by = :user_id
                OR updated_by = :user_id
                OR archived_by = :user_id
                OR archive_approved_by = :user_id'
        );
        $statement->execute(['user_id' => $userId]);

        return (int) $statement->fetchColumn() > 0;
    }
}
