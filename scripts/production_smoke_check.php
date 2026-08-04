<?php

declare(strict_types=1);

use Bootstrap\Database;
use Bootstrap\SchemaIntegrityGuard;

require dirname(__DIR__) . '/bootstrap/autoload.php';

$__productionSmokeConfig = loadProductionSmokeConfig();

if (!function_exists('app')) {
    function app(?string $key = null, mixed $default = null): mixed
    {
        global $__productionSmokeConfig;

        if ($key === null) {
            return $__productionSmokeConfig;
        }

        $segments = explode('.', $key);
        $value = $__productionSmokeConfig;

        foreach ($segments as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }

            $value = $value[$segment];
        }

        return $value;
    }
}

$checks = [];
$warnings = [];
$failures = [];

try {
    $connection = Database::connection();
    $databaseName = (string) app('database.database', 'bps');

    SchemaIntegrityGuard::assertValid($connection, $databaseName);
    pass($checks, 'Schema integrity guard passed.');

    $tableCount = (int) scalar($connection, 'SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE()');
    $parentCount = (int) scalar($connection, 'SELECT COUNT(*) FROM parent_procurement');
    $userCount = (int) scalar($connection, 'SELECT COUNT(*) FROM users');
    $auditCount = (int) scalar($connection, 'SELECT COUNT(*) FROM procurement_activity_logs');

    pass($checks, 'Connected to database `' . $databaseName . '`.');
    pass($checks, 'Found ' . $tableCount . ' tables, ' . $parentCount . ' procurement records, ' . $userCount . ' users, and ' . $auditCount . ' procurement audit rows.');

    $duplicateReferences = (int) scalar(
        $connection,
        'SELECT COUNT(*)
         FROM (
             SELECT reference_number
             FROM parent_procurement
             GROUP BY reference_number
             HAVING COUNT(*) > 1
         ) duplicates'
    );
    recordZeroCheck($checks, $failures, $duplicateReferences, 'Duplicate reference numbers');

    $invalidUserReferences = (int) scalar(
        $connection,
        "SELECT COUNT(*)
         FROM (
             SELECT p.id
             FROM parent_procurement p
             LEFT JOIN users u ON u.id = p.created_by
             WHERE u.id IS NULL
             UNION ALL
             SELECT p.id
             FROM parent_procurement p
             LEFT JOIN users u ON u.id = p.updated_by
             WHERE u.id IS NULL
             UNION ALL
             SELECT p.id
             FROM parent_procurement p
             LEFT JOIN users u ON u.id = p.archived_by
             WHERE p.archived_by IS NOT NULL AND u.id IS NULL
             UNION ALL
             SELECT p.id
             FROM parent_procurement p
             LEFT JOIN users u ON u.id = p.archive_approved_by
             WHERE p.archive_approved_by IS NOT NULL AND u.id IS NULL
         ) invalid_refs"
    );
    recordZeroCheck($checks, $failures, $invalidUserReferences, 'Invalid parent procurement user references');

    $workflowIssues = (int) scalar(
        $connection,
        "SELECT COUNT(*)
         FROM (
             SELECT p.id
             FROM parent_procurement p
             INNER JOIN awards a ON a.parent_procurement_id = p.id
             LEFT JOIN resolutions r ON r.parent_procurement_id = p.id
             WHERE p.procurement_mode = 'competitive_bidding' AND r.id IS NULL
             UNION ALL
             SELECT p.id
             FROM parent_procurement p
             INNER JOIN contracts c ON c.parent_procurement_id = p.id
             LEFT JOIN awards a ON a.parent_procurement_id = p.id
             WHERE p.procurement_mode = 'competitive_bidding' AND a.id IS NULL
             UNION ALL
             SELECT p.id
             FROM parent_procurement p
             INNER JOIN notices_to_proceed n ON n.parent_procurement_id = p.id
             LEFT JOIN contracts c ON c.parent_procurement_id = p.id
             WHERE p.procurement_mode = 'competitive_bidding' AND c.id IS NULL
             UNION ALL
             SELECT p.id
             FROM parent_procurement p
             INNER JOIN awards a ON a.parent_procurement_id = p.id
             LEFT JOIN rfqs r ON r.parent_procurement_id = p.id
             LEFT JOIN abstract_of_quotations aq ON aq.parent_procurement_id = p.id
             LEFT JOIN canvasses cv ON cv.parent_procurement_id = p.id
             WHERE p.procurement_mode = 'svp' AND r.id IS NULL AND aq.id IS NULL AND cv.id IS NULL
         ) workflow_issues"
    );
    recordZeroCheck($checks, $failures, $workflowIssues, 'Workflow prerequisite issues');

    $publicStatusRows = rows(
        $connection,
        'SELECT posting_status, COUNT(*) AS total
         FROM parent_procurement
         GROUP BY posting_status
         ORDER BY posting_status'
    );
    pass($checks, 'Posting status distribution: ' . json_encode($publicStatusRows, JSON_UNESCAPED_SLASHES));

    $documentRows = documentRows($connection);
    $missingFiles = missingDocumentFiles($documentRows);
    if ($missingFiles > 0) {
        warn($warnings, 'Missing uploaded document files: ' . $missingFiles . ' of ' . count($documentRows) . ' document rows. Review Phase 2 missing_document_files.csv before release sign-off.');
    } else {
        pass($checks, 'All ' . count($documentRows) . ' document files exist on disk.');
    }
} catch (Throwable $throwable) {
    fail($failures, $throwable->getMessage());
}

printSection('PASS', $checks);
printSection('WARN', $warnings);
printSection('FAIL', $failures);

if ($failures !== []) {
    exit(1);
}

exit(0);

function loadProductionSmokeConfig(): array
{
    $config = [];
    $configPath = dirname(__DIR__) . '/app/config';
    foreach (glob($configPath . '/*.php') ?: [] as $file) {
        $config[pathinfo($file, PATHINFO_FILENAME)] = require $file;
    }

    return $config;
}

function scalar(PDO $connection, string $sql): mixed
{
    return $connection->query($sql)->fetchColumn();
}

function rows(PDO $connection, string $sql): array
{
    $result = $connection->query($sql)->fetchAll();

    return is_array($result) ? $result : [];
}

function documentRows(PDO $connection): array
{
    return rows(
        $connection,
        "SELECT 'bid_notice' AS document_type, id, parent_procurement_id, file_path FROM bid_notices
         UNION ALL SELECT 'supplemental_bid_bulletin', id, parent_procurement_id, file_path FROM supplemental_bid_bulletins
         UNION ALL SELECT 'resolution', id, parent_procurement_id, file_path FROM resolutions
         UNION ALL SELECT 'award', id, parent_procurement_id, file_path FROM awards
         UNION ALL SELECT 'contract', id, parent_procurement_id, file_path FROM contracts
         UNION ALL SELECT 'notice_to_proceed', id, parent_procurement_id, file_path FROM notices_to_proceed
         UNION ALL SELECT 'rfq', id, parent_procurement_id, file_path FROM rfqs
         UNION ALL SELECT 'abstract_of_quotations', id, parent_procurement_id, file_path FROM abstract_of_quotations
         UNION ALL SELECT 'canvass', id, parent_procurement_id, file_path FROM canvasses
         UNION ALL SELECT 'contract_or_purchase_order', id, parent_procurement_id, file_path FROM contract_or_purchase_orders"
    );
}

function missingDocumentFiles(array $documents): int
{
    $missing = 0;
    $root = dirname(__DIR__);

    foreach ($documents as $document) {
        $relativePath = trim((string) ($document['file_path'] ?? ''));
        if ($relativePath === '') {
            $missing++;
            continue;
        }

        $absolutePath = $root . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relativePath);
        if (!is_file($absolutePath)) {
            $missing++;
        }
    }

    return $missing;
}

function recordZeroCheck(array &$checks, array &$failures, int $count, string $label): void
{
    if ($count === 0) {
        pass($checks, $label . ': none found.');
        return;
    }

    fail($failures, $label . ': ' . $count . ' found.');
}

function pass(array &$checks, string $message): void
{
    $checks[] = $message;
}

function warn(array &$warnings, string $message): void
{
    $warnings[] = $message;
}

function fail(array &$failures, string $message): void
{
    $failures[] = $message;
}

function printSection(string $label, array $messages): void
{
    echo '[' . $label . ']' . PHP_EOL;
    if ($messages === []) {
        echo '- none' . PHP_EOL . PHP_EOL;
        return;
    }

    foreach ($messages as $message) {
        echo '- ' . $message . PHP_EOL;
    }
    echo PHP_EOL;
}
