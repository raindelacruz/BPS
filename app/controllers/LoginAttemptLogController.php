<?php

namespace App\Controllers;

use App\Helpers\SecurityHelper;
use App\Models\LoginAttemptLog;

class LoginAttemptLogController extends BaseController
{
    private LoginAttemptLog $logs;

    public function __construct()
    {
        $this->logs = new LoginAttemptLog();
    }

    public function index(array $params = []): void
    {
        SecurityHelper::requireAuth();
        SecurityHelper::requireRole('admin');

        $filters = [
            'search' => trim((string) ($_GET['search'] ?? '')),
            'outcome' => trim((string) ($_GET['outcome'] ?? '')),
            'event_type' => trim((string) ($_GET['event_type'] ?? '')),
            'failure_reason' => trim((string) ($_GET['failure_reason'] ?? '')),
        ];

        $this->view('auth/login_logs', [
            'title' => 'Login Logs',
            'logs' => $this->logs->latest($filters),
            'filters' => $filters,
            'outcomes' => ['success' => 'Success', 'failure' => 'Failure'],
            'eventTypes' => [
                'login_attempt' => 'Login attempt',
                'csrf_failure' => 'Session expired',
                'logout' => 'Logout',
            ],
            'failureReasons' => [
                'missing_username' => 'Missing username',
                'missing_password' => 'Missing password',
                'missing_fields' => 'Missing fields',
                'username_not_found' => 'Username not found',
                'invalid_password' => 'Invalid password',
                'inactive_account' => 'Inactive account',
                'csrf_failed' => 'Session/CSRF failed',
            ],
        ]);
    }
}
