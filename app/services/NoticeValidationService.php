<?php

namespace App\Services;

use App\Helpers\ProcurementTypeHelper;
use App\Models\Notice;
use DateTimeImmutable;

class NoticeValidationService extends BaseService
{
    private const RELATED_TYPES = ['sbb', 'resolution', 'award', 'contract', 'proceed'];

    public function __construct(private readonly ?Notice $notices = null)
    {
    }

    public function procurementTypes(): array
    {
        return ProcurementTypeHelper::all();
    }

    public function validateBid(array $input, ?array $file, ?int $ignoreId = null, bool $requireFile = true): array
    {
        $data = $this->normalizeBidInput($input);
        $errors = [];

        foreach (['title', 'reference_code', 'procurement_type', 'start_date', 'end_date', 'description'] as $field) {
            if ($data[$field] === '') {
                $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' is required.';
            }
        }

        if ($data['procurement_type'] !== '' && !in_array($data['procurement_type'], ProcurementTypeHelper::values(), true)) {
            $errors[] = 'Procurement type is invalid.';
        }

        if ($data['reference_code'] !== '' && ($this->notices ?? new Notice())->referenceCodeExistsForBid($data['reference_code'], $ignoreId)) {
            $errors[] = 'Reference code is already used by another bid.';
        }

        if ($data['start_date'] !== '' && $data['end_date'] !== '') {
            try {
                $start = new DateTimeImmutable($data['start_date']);
                $end = new DateTimeImmutable($data['end_date']);

                if ($end <= $start) {
                    $errors[] = 'End date must be later than start date.';
                }

                $data['start_date'] = $start->format('Y-m-d H:i:s');
                $data['end_date'] = $end->format('Y-m-d H:i:s');
            } catch (\Exception) {
                $errors[] = 'Notice dates are invalid.';
            }
        }

        if ($requireFile || ($file && ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE)) {
            $errors = array_merge($errors, $this->validatePdf($file));
        }

        return [
            'data' => $data,
            'errors' => $errors,
        ];
    }

    public function relatedTypes(): array
    {
        return self::RELATED_TYPES;
    }

    public function validateRelatedNotice(array $input, ?array $file, bool $requireFile = true): array
    {
        $data = [
            'type' => strtolower(trim((string) ($input['type'] ?? ''))),
            'selected_bid_id' => (int) ($input['selected_bid_id'] ?? 0),
            'title' => trim((string) ($input['title'] ?? '')),
            'start_date' => trim((string) ($input['start_date'] ?? '')),
            'end_date' => trim((string) ($input['end_date'] ?? '')),
            'description' => trim((string) ($input['description'] ?? '')),
        ];
        $errors = [];

        foreach (['type', 'selected_bid_id', 'title', 'start_date', 'end_date', 'description'] as $field) {
            if ($data[$field] === '' || $data[$field] === 0) {
                $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' is required.';
            }
        }

        if ($data['type'] !== '' && !in_array($data['type'], self::RELATED_TYPES, true)) {
            $errors[] = 'Notice type is invalid.';
        }

        if ($data['start_date'] !== '' && $data['end_date'] !== '') {
            try {
                $start = new DateTimeImmutable($data['start_date']);
                $end = new DateTimeImmutable($data['end_date']);

                if ($end <= $start) {
                    $errors[] = 'End date must be later than start date.';
                }

                $data['start_date'] = $start->format('Y-m-d H:i:s');
                $data['end_date'] = $end->format('Y-m-d H:i:s');
            } catch (\Exception) {
                $errors[] = 'Notice dates are invalid.';
            }
        }

        if ($requireFile || ($file && ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE)) {
            $errors = array_merge($errors, $this->validatePdf($file));
        }

        return [
            'data' => $data,
            'errors' => $errors,
        ];
    }

    private function normalizeBidInput(array $input): array
    {
        return [
            'title' => trim((string) ($input['title'] ?? '')),
            'reference_code' => trim((string) ($input['reference_code'] ?? '')),
            'procurement_type' => trim((string) ($input['procurement_type'] ?? '')),
            'start_date' => trim((string) ($input['start_date'] ?? '')),
            'end_date' => trim((string) ($input['end_date'] ?? '')),
            'description' => trim((string) ($input['description'] ?? '')),
        ];
    }

    private function validatePdf(?array $file): array
    {
        if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return ['PDF file is required.'];
        }

        if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            return ['PDF upload failed.'];
        }

        $extension = strtolower(pathinfo((string) ($file['name'] ?? ''), PATHINFO_EXTENSION));
        $mimeType = strtolower((string) ($file['type'] ?? ''));

        if ($extension !== 'pdf' && $mimeType !== 'application/pdf') {
            return ['Only PDF uploads are allowed.'];
        }

        return [];
    }
}
