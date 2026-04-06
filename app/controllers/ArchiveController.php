<?php

namespace App\Controllers;

use App\Helpers\SecurityHelper;
use App\Helpers\SessionHelper;

class ArchiveController extends BaseController
{
    public function archive(array $params = []): void
    {
        SecurityHelper::requireAuth();
        if (!SecurityHelper::verifyCsrf($_POST['_token'] ?? null)) {
            SessionHelper::flash('error', 'Your session expired. Please try again.');
            $this->redirect('notices');
        }

        SessionHelper::flash('error', 'Archiving is disabled for the strict sequential posting module.');
        $this->redirect('notices/' . (int) ($params['id'] ?? 0));
    }

    public function unarchive(array $params = []): void
    {
        SecurityHelper::requireAuth();
        if (!SecurityHelper::verifyCsrf($_POST['_token'] ?? null)) {
            SessionHelper::flash('error', 'Your session expired. Please try again.');
            $this->redirect('notices');
        }

        SessionHelper::flash('error', 'Unarchiving is disabled for the strict sequential posting module.');
        $this->redirect('notices/' . (int) ($params['id'] ?? 0));
    }
}
