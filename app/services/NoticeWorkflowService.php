<?php

namespace App\Services;

class NoticeWorkflowService extends BaseService
{
    public function buildRelatedNoticePayload(string $type, array $bid, array $data, int $userId, string $filePath, string $status): array
    {
        return [
            'title' => $data['title'],
            'reference_code' => $bid['reference_code'],
            'type' => $type,
            'file_path' => $filePath,
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'uploaded_by' => $userId,
            'description' => $data['description'],
            'is_archived' => 0,
            'status' => $status,
            'region' => $bid['region'],
            'branch' => $bid['branch'] ?? null,
            'procurement_type' => $bid['procurement_type'],
        ];
    }
}
