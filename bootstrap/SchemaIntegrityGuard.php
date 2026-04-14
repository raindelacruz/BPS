<?php

namespace Bootstrap;

use PDO;
use RuntimeException;

class SchemaIntegrityGuard
{
    private const REQUIRED_TABLES = [
        'users' => ['id', 'username', 'email', 'role', 'region', 'branch'],
        'parent_procurement' => [
            'id',
            'procurement_mode',
            'reference_number',
            'procurement_title',
            'abc',
            'mode_of_procurement',
            'posting_date',
            'bid_submission_deadline',
            'description',
            'posting_status',
            'current_stage',
            'archived_at',
            'archive_reason',
            'archived_by',
            'archive_approval_reference',
            'archive_approved_by',
            'archive_approved_at',
            'category',
            'end_user_unit',
            'region',
            'branch',
            'created_by',
            'updated_by',
        ],
        'rfqs' => ['id', 'parent_procurement_id', 'file_path', 'file_hash', 'posted_at'],
        'abstract_of_quotations' => ['id', 'parent_procurement_id', 'file_path', 'file_hash', 'posted_at'],
        'canvasses' => ['id', 'parent_procurement_id', 'file_path', 'file_hash', 'posted_at'],
        'bid_notices' => ['id', 'parent_procurement_id', 'file_path', 'file_hash', 'posted_at'],
        'supplemental_bid_bulletins' => ['id', 'parent_procurement_id', 'file_path', 'file_hash', 'posted_at'],
        'resolutions' => ['id', 'parent_procurement_id', 'file_path', 'file_hash', 'posted_at'],
        'awards' => ['id', 'parent_procurement_id', 'file_path', 'file_hash', 'posted_at'],
        'contracts' => ['id', 'parent_procurement_id', 'file_path', 'file_hash', 'posted_at'],
        'contract_or_purchase_orders' => ['id', 'parent_procurement_id', 'file_path', 'file_hash', 'posted_at'],
        'notices_to_proceed' => ['id', 'parent_procurement_id', 'file_path', 'file_hash', 'posted_at'],
        'procurement_activity_logs' => [
            'id',
            'parent_procurement_id',
            'user_id',
            'action_type',
            'document_type',
            'document_id',
            'before_snapshot',
            'after_snapshot',
            'reason',
            'file_hash',
            'approval_reference',
        ],
    ];

    private const FORBIDDEN_TABLES = ['notices'];

    public static function assertValid(PDO $connection, string $databaseName): void
    {
        $issues = [];

        foreach (self::FORBIDDEN_TABLES as $table) {
            if (self::tableExists($connection, $databaseName, $table)) {
                $issues[] = 'Legacy table `' . $table . '` is still present.';
            }
        }

        foreach (self::REQUIRED_TABLES as $table => $columns) {
            if (!self::tableExists($connection, $databaseName, $table)) {
                $issues[] = 'Required table `' . $table . '` is missing.';
                continue;
            }

            $actualColumns = self::columnsFor($connection, $databaseName, $table);
            foreach ($columns as $column) {
                if (!in_array($column, $actualColumns, true)) {
                    $issues[] = 'Required column `' . $table . '.' . $column . '` is missing.';
                }
            }
        }

        if ($issues !== []) {
            throw new RuntimeException('Schema integrity check failed: ' . implode(' ', $issues));
        }
    }

    private static function tableExists(PDO $connection, string $databaseName, string $table): bool
    {
        $statement = $connection->prepare(
            'SELECT COUNT(*)
             FROM information_schema.tables
             WHERE table_schema = :schema
               AND table_name = :table_name'
        );
        $statement->execute([
            'schema' => $databaseName,
            'table_name' => $table,
        ]);

        return (int) $statement->fetchColumn() > 0;
    }

    private static function columnsFor(PDO $connection, string $databaseName, string $table): array
    {
        $statement = $connection->prepare(
            'SELECT column_name
             FROM information_schema.columns
             WHERE table_schema = :schema
               AND table_name = :table_name
             ORDER BY ordinal_position ASC'
        );
        $statement->execute([
            'schema' => $databaseName,
            'table_name' => $table,
        ]);

        return array_map(
            static fn (array $row): string => (string) $row['column_name'],
            $statement->fetchAll() ?: []
        );
    }
}
