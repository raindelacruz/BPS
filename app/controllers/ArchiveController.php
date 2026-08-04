<?php

namespace App\Controllers;

use App\Helpers\SecurityHelper;
use App\Helpers\SessionHelper;
use App\Services\ProcurementPostingService;

class ArchiveController extends BaseController
{
    private ProcurementPostingService $posting;

    public function __construct()
    {
        $this->posting = new ProcurementPostingService();
    }

    public function archive(array $params = []): void
    {
        SecurityHelper::requireAuth();
        if (!SecurityHelper::verifyCsrf($_POST['_token'] ?? null)) {
            SessionHelper::flash('error', 'Your session expired. Please try again.');
            $this->redirect('notices');
        }

        $result = $this->posting->archiveParent((int) ($params['id'] ?? 0), SecurityHelper::currentUser() ?? [], $_POST);
        SessionHelper::flash($result['allowed'] ? 'success' : 'error', $result['allowed'] ? 'Procurement record archived successfully.' : ($result['errors'][0] ?? 'Procurement record could not be archived.'));
        $this->redirect('notices/' . (int) ($params['id'] ?? 0));
    }

    public function unarchive(array $params = []): void
    {
        SecurityHelper::requireAuth();
        if (!SecurityHelper::verifyCsrf($_POST['_token'] ?? null)) {
            SessionHelper::flash('error', 'Your session expired. Please try again.');
            $this->redirect('notices');
        }

        $result = $this->posting->unarchiveParent((int) ($params['id'] ?? 0), SecurityHelper::currentUser() ?? []);
        SessionHelper::flash('error', $result['errors'][0] ?? 'Procurement record could not be restored.');
        $this->redirect('notices/' . (int) ($params['id'] ?? 0));
    }
}
