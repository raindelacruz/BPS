<?php

namespace App\Models;

use InvalidArgumentException;

class ProcurementDocument extends BaseModel
{
    public const TYPE_BID_NOTICE = 'bid_notice';
    public const TYPE_SBB = 'supplemental_bid_bulletin';
    public const TYPE_RESOLUTION = 'resolution';
    public const TYPE_AWARD = 'award';
    public const TYPE_CONTRACT = 'contract';
    public const TYPE_NOTICE_TO_PROCEED = 'notice_to_proceed';

    public const MAP = [
        self::TYPE_BID_NOTICE => [
            'table' => 'bid_notices',
            'label' => 'Bid Notice / Invitation to Bid',
            'stage' => 1,
            'repeatable' => false,
        ],
        self::TYPE_SBB => [
            'table' => 'supplemental_bid_bulletins',
            'label' => 'Supplemental/Bid Bulletin',
            'stage' => 2,
            'repeatable' => true,
        ],
        self::TYPE_RESOLUTION => [
            'table' => 'resolutions',
            'label' => 'Resolution',
            'stage' => 3,
            'repeatable' => false,
        ],
        self::TYPE_AWARD => [
            'table' => 'awards',
            'label' => 'Notice of Award / Award',
            'stage' => 4,
            'repeatable' => false,
        ],
        self::TYPE_CONTRACT => [
            'table' => 'contracts',
            'label' => 'Contract',
            'stage' => 5,
            'repeatable' => false,
        ],
        self::TYPE_NOTICE_TO_PROCEED => [
            'table' => 'notices_to_proceed',
            'label' => 'Notice to Proceed',
            'stage' => 6,
            'repeatable' => false,
        ],
    ];

    public function create(string $type, array $data): int
    {
        $meta = $this->meta($type);
        $statement = $this->connection()->prepare(
            'INSERT INTO ' . $meta['table'] . ' (
                parent_procurement_id,
                title,
                description,
                file_path,
                document_type,
                sequence_stage,
                posted_at,
                is_locked,
                locked_at,
                lock_reason,
                is_reopened,
                reopened_at,
                reopened_by,
                created_by,
                updated_by
            ) VALUES (
                :parent_procurement_id,
                :title,
                :description,
                :file_path,
                :document_type,
                :sequence_stage,
                :posted_at,
                :is_locked,
                :locked_at,
                :lock_reason,
                :is_reopened,
                :reopened_at,
                :reopened_by,
                :created_by,
                :updated_by
            )'
        );

        $statement->execute([
            'parent_procurement_id' => $data['parent_procurement_id'],
            'title' => $data['title'],
            'description' => $data['description'],
            'file_path' => $data['file_path'],
            'document_type' => $type,
            'sequence_stage' => $meta['stage'],
            'posted_at' => $data['posted_at'],
            'is_locked' => (int) ($data['is_locked'] ?? 0),
            'locked_at' => $data['locked_at'] ?? null,
            'lock_reason' => $data['lock_reason'] ?? null,
            'is_reopened' => (int) ($data['is_reopened'] ?? 0),
            'reopened_at' => $data['reopened_at'] ?? null,
            'reopened_by' => $data['reopened_by'] ?? null,
            'created_by' => $data['created_by'],
            'updated_by' => $data['updated_by'],
        ]);

        return (int) $this->connection()->lastInsertId();
    }

    public function updateById(string $type, int $id, array $data): bool
    {
        $meta = $this->meta($type);
        $statement = $this->connection()->prepare(
            'UPDATE ' . $meta['table'] . '
             SET title = :title,
                 description = :description,
                 file_path = :file_path,
                 posted_at = :posted_at,
                 updated_by = :updated_by
             WHERE id = :id'
        );

        return $statement->execute([
            'id' => $id,
            'title' => $data['title'],
            'description' => $data['description'],
            'file_path' => $data['file_path'],
            'posted_at' => $data['posted_at'],
            'updated_by' => $data['updated_by'],
        ]);
    }

    public function findById(string $type, int $id): ?array
    {
        $meta = $this->meta($type);
        $statement = $this->connection()->prepare(
            'SELECT d.*,
                    creator.username AS creator_username,
                    creator.firstname AS creator_firstname,
                    creator.lastname AS creator_lastname,
                    reopener.username AS reopener_username
             FROM ' . $meta['table'] . ' d
             INNER JOIN users creator ON creator.id = d.created_by
             LEFT JOIN users reopener ON reopener.id = d.reopened_by
             WHERE d.id = :id
             LIMIT 1'
        );

        $statement->execute(['id' => $id]);

        return $statement->fetch() ?: null;
    }

    public function findForParent(string $type, int $parentId): array
    {
        $meta = $this->meta($type);
        $statement = $this->connection()->prepare(
            'SELECT d.*,
                    creator.username AS creator_username,
                    creator.firstname AS creator_firstname,
                    creator.lastname AS creator_lastname,
                    reopener.username AS reopener_username
             FROM ' . $meta['table'] . ' d
             INNER JOIN users creator ON creator.id = d.created_by
             LEFT JOIN users reopener ON reopener.id = d.reopened_by
             WHERE d.parent_procurement_id = :parent_procurement_id
             ORDER BY d.posted_at ASC, d.id ASC'
        );

        $statement->execute(['parent_procurement_id' => $parentId]);

        return $statement->fetchAll() ?: [];
    }

    public function findOneForParent(string $type, int $parentId): ?array
    {
        $documents = $this->findForParent($type, $parentId);

        return $documents[0] ?? null;
    }

    public function existsForParent(string $type, int $parentId, ?int $ignoreId = null): bool
    {
        $meta = $this->meta($type);
        $sql = 'SELECT COUNT(*) FROM ' . $meta['table'] . ' WHERE parent_procurement_id = :parent_procurement_id';
        $params = ['parent_procurement_id' => $parentId];

        if ($ignoreId !== null) {
            $sql .= ' AND id <> :ignore_id';
            $params['ignore_id'] = $ignoreId;
        }

        $statement = $this->connection()->prepare($sql);
        $statement->execute($params);

        return (int) $statement->fetchColumn() > 0;
    }

    public function lockForParent(string $type, int $parentId, string $reason): bool
    {
        $meta = $this->meta($type);
        $statement = $this->connection()->prepare(
            'UPDATE ' . $meta['table'] . '
             SET is_locked = 1,
                 locked_at = NOW(),
                 lock_reason = :lock_reason,
                 is_reopened = 0,
                 reopened_at = NULL,
                 reopened_by = NULL
             WHERE parent_procurement_id = :parent_procurement_id'
        );

        return $statement->execute([
            'parent_procurement_id' => $parentId,
            'lock_reason' => $reason,
        ]);
    }

    public function reopen(string $type, int $id, int $userId): bool
    {
        $meta = $this->meta($type);
        $statement = $this->connection()->prepare(
            'UPDATE ' . $meta['table'] . '
             SET is_locked = 0,
                 lock_reason = NULL,
                 is_reopened = 1,
                 reopened_at = NOW(),
                 reopened_by = :reopened_by
             WHERE id = :id'
        );

        return $statement->execute([
            'id' => $id,
            'reopened_by' => $userId,
        ]);
    }

    public function allForParent(int $parentId): array
    {
        $documents = [];

        foreach (array_keys(self::MAP) as $type) {
            foreach ($this->findForParent($type, $parentId) as $document) {
                $document['document_type'] = $type;
                $document['document_label'] = self::label($type);
                $documents[] = $document;
            }
        }

        usort($documents, static function (array $left, array $right): int {
            if ((int) $left['sequence_stage'] === (int) $right['sequence_stage']) {
                return strcmp((string) $left['posted_at'], (string) $right['posted_at']);
            }

            return (int) $left['sequence_stage'] <=> (int) $right['sequence_stage'];
        });

        return $documents;
    }

    public static function label(string $type): string
    {
        return self::MAP[$type]['label'] ?? $type;
    }

    public static function stageNumber(string $type): int
    {
        return (int) (self::MAP[$type]['stage'] ?? 0);
    }

    public static function isRepeatable(string $type): bool
    {
        return (bool) (self::MAP[$type]['repeatable'] ?? false);
    }

    public static function types(): array
    {
        return array_keys(self::MAP);
    }

    private function meta(string $type): array
    {
        if (!isset(self::MAP[$type])) {
            throw new InvalidArgumentException('Unsupported procurement document type.');
        }

        return self::MAP[$type];
    }
}
