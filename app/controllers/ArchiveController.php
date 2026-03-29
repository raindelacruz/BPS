<?php

namespace App\Controllers;

use App\Helpers\ResponseHelper;
use App\Helpers\SecurityHelper;
use App\Helpers\SessionHelper;
use App\Models\Notice;
use App\Services\ArchiveService;
use App\Services\DateStatusService;
use Bootstrap\Database;
use Throwable;

class ArchiveController extends BaseController
{
    private Notice $notices;

    private DateStatusService $statusService;

    private ArchiveService $archiveService;

    public function __construct()
    {
        $this->notices = new Notice();
        $this->statusService = new DateStatusService($this->notices);
        $this->archiveService = new ArchiveService($this->notices, $this->statusService);
    }

    public function archive(array $params = []): void
    {
        SecurityHelper::requireAuth();
        $this->enforceCsrf();

        $notice = $this->loadNotice((int) ($params['id'] ?? 0));
        $notice = $this->statusService->synchronizeNotice($notice);
        $guard = $this->archiveService->canArchive($notice, SecurityHelper::currentUser() ?? []);

        if (!$guard['allowed']) {
            SessionHelper::flash('error', implode(' ', $guard['errors']));
            $this->redirect('notices/' . $notice['id']);
        }

        $connection = Database::connection();
        $connection->beginTransaction();

        try {
            $this->archiveService->archive((string) $notice['reference_code']);
            $connection->commit();

            SessionHelper::flash('success', 'Workflow set archived successfully.');
        } catch (Throwable $throwable) {
            if ($connection->inTransaction()) {
                $connection->rollBack();
            }

            SessionHelper::flash('error', 'Workflow set could not be archived.');
        }

        $this->redirect('notices/' . $notice['id']);
    }

    public function unarchive(array $params = []): void
    {
        SecurityHelper::requireAuth();
        $this->enforceCsrf();

        $notice = $this->loadNotice((int) ($params['id'] ?? 0));
        $user = SecurityHelper::currentUser() ?? [];

        if ((int) ($notice['uploaded_by'] ?? 0) !== (int) ($user['id'] ?? 0)) {
            ResponseHelper::abort(403, 'Only the uploader may unarchive this workflow set.');
        }

        $connection = Database::connection();
        $connection->beginTransaction();

        try {
            $this->archiveService->unarchive((string) $notice['reference_code']);
            $connection->commit();

            SessionHelper::flash('success', 'Workflow set restored successfully.');
        } catch (Throwable $throwable) {
            if ($connection->inTransaction()) {
                $connection->rollBack();
            }

            SessionHelper::flash('error', 'Workflow set could not be restored.');
        }

        $this->redirect('notices/' . $notice['id']);
    }

    private function loadNotice(int $id): array
    {
        $notice = $this->notices->findById($id);

        if (!$notice) {
            ResponseHelper::abort(404, 'Notice not found.');
        }

        return $notice;
    }

    private function enforceCsrf(): void
    {
        if (!SecurityHelper::verifyCsrf($_POST['_token'] ?? null)) {
            ResponseHelper::abort(419, 'Invalid CSRF token.');
        }
    }
}
