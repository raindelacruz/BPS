<?php

use App\Helpers\ResponseHelper;
use App\Helpers\SecurityHelper;
use App\Helpers\ViewHelper;

$appName = (string) app('app.name', 'Bid Posting System');
$pageTitle = isset($title) ? ViewHelper::escape($title) . ' | ' . ViewHelper::escape($appName) : ViewHelper::escape($appName);
$currentUser = SecurityHelper::currentUser();
$isAuthenticated = $currentUser !== null;
$isAdmin = ($currentUser['role'] ?? null) === 'admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle; ?></title>
    <style>
        :root {
            --bg: #eef3f8;
            --panel: #ffffff;
            --panel-soft: #f8fafc;
            --panel-strong: #e2e8f0;
            --text: #0f172a;
            --muted: #5b6b7f;
            --accent: #0f766e;
            --accent-dark: #115e59;
            --accent-soft: #ecfdf5;
            --line: #d9e2ec;
            --danger: #b91c1c;
            --danger-soft: #fef2f2;
            --shadow: 0 16px 36px rgba(15, 23, 42, 0.08);
        }
        * {
            box-sizing: border-box;
        }
        body {
            margin: 0;
            font-family: "Segoe UI", "Helvetica Neue", Arial, sans-serif;
            background:
                radial-gradient(circle at top left, rgba(15, 118, 110, 0.10), transparent 22rem),
                linear-gradient(180deg, #f7fafc 0%, var(--bg) 100%);
            color: var(--text);
        }
        .shell {
            max-width: 1160px;
            margin: 0 auto;
            padding: 12px 12px 18px;
        }
        .app-shell {
            display: grid;
            gap: 10px;
        }
        .card {
            background: var(--panel);
            border: 1px solid rgba(217, 226, 236, 0.95);
            border-radius: 14px;
            box-shadow: var(--shadow);
            padding: 14px;
        }
        .topbar {
            display: flex;
            flex-wrap: nowrap;
            justify-content: space-between;
            gap: 10px;
            align-items: center;
            padding: 10px 12px;
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(217, 226, 236, 0.95);
            border-radius: 14px;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
        }
        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .brand h1,
        .brand p {
            margin: 0;
        }
        .brand h1 {
            font-size: 1.05rem;
            line-height: 1.1;
        }
        .brand p {
            color: var(--muted);
            font-size: 0.82rem;
        }
        .brand img {
            width: 46px;
            height: 46px;
            object-fit: contain;
            border-radius: 12px;
            background: #fff;
            padding: 4px;
            border: 1px solid rgba(217, 226, 236, 0.95);
        }
        .nav {
            display: flex;
            flex-wrap: nowrap;
            gap: 8px;
            align-items: center;
        }
        .topbar-right {
            display: flex;
            flex-wrap: nowrap;
            justify-content: flex-end;
            gap: 10px;
            align-items: center;
            min-width: 0;
        }
        .nav a {
            display: inline-flex;
            align-items: center;
            min-height: 34px;
            padding: 0 12px;
            border-radius: 9px;
            color: var(--muted);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 700;
            transition: background-color 0.2s ease, color 0.2s ease;
        }
        .nav a:hover {
            background: var(--panel-soft);
            color: var(--accent-dark);
        }
        .session-meta {
            display: flex;
            flex-wrap: nowrap;
            align-items: center;
            gap: 6px 10px;
            padding: 6px 10px;
            border: 1px solid var(--line);
            border-radius: 10px;
            background: var(--panel-soft);
            color: var(--muted);
            font-size: 0.84rem;
            white-space: nowrap;
        }
        .session-meta strong {
            color: var(--text);
        }
        .session-form {
            display: inline-grid;
            gap: 0;
        }
        .session-form button {
            min-height: 34px;
            padding: 0 12px;
            border-radius: 9px;
            border: 1px solid var(--line);
            background: #fff;
            color: var(--muted);
            font-size: 0.9rem;
            font-weight: 700;
            box-shadow: none;
        }
        .session-form button:hover {
            background: var(--panel-soft);
            border-color: var(--line);
            color: var(--accent-dark);
        }
        .page-head {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: flex-start;
            gap: 8px;
            margin-bottom: 10px;
        }
        .page-head h1,
        .page-head h2,
        .page-head p {
            margin: 0;
        }
        .page-head p,
        .lead,
        .muted {
            color: var(--muted);
        }
        .flash {
            margin: 0;
            padding: 9px 11px;
            border-radius: 10px;
            border: 1px solid #bfdbfe;
            background: #eff6ff;
            color: #1e3a8a;
        }
        .flash + .flash {
            margin-top: 8px;
        }
        .section-stack {
            display: grid;
            gap: 10px;
        }
        .stack-sm {
            display: grid;
            gap: 8px;
        }
        .panel {
            background: var(--panel-soft);
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 10px 12px;
        }
        .form-grid,
        form {
            display: grid;
            gap: 10px;
        }
        .form-grid.two-col {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
        label,
        .field-label {
            font-size: 14px;
            font-weight: 600;
            color: #334155;
        }
        input, button, select, textarea {
            font: inherit;
            width: 100%;
            padding: 8px 10px;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            background: #fff;
            color: var(--text);
        }
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #38bdf8;
            box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.16);
        }
        button {
            cursor: pointer;
            width: auto;
            background: var(--accent);
            border-color: var(--accent);
            color: #fff;
            font-weight: 700;
            transition: background-color 0.2s ease, border-color 0.2s ease;
        }
        button:hover {
            background: var(--accent-dark);
            border-color: var(--accent-dark);
        }
        .btn-row,
        .link-row,
        .action-row {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            align-items: center;
        }
        a {
            color: #0f5f8c;
            text-decoration: none;
            font-weight: 600;
        }
        a:hover {
            text-decoration: underline;
        }
        .btn-link,
        .chip-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 32px;
            padding: 0 12px;
            border-radius: 8px;
            border: 1px solid var(--line);
            background: #fff;
            color: #0f5f8c;
            text-decoration: none;
            font-weight: 700;
            font-size: 0.9rem;
        }
        .btn-link:hover,
        .chip-link:hover {
            text-decoration: none;
            background: #f8fafc;
        }
        .chip-link {
            min-height: 30px;
            padding: 0 10px;
            font-size: 0.84rem;
        }
        .chip-link.is-disabled,
        .btn-link.is-disabled {
            pointer-events: none;
            opacity: 0.55;
            background: #f1f5f9;
            color: #64748b;
        }
        .helper-text {
            margin: 2px 0 0;
            font-size: 0.78rem;
            color: var(--muted);
        }
        .field-error {
            margin: 4px 0 0;
            color: var(--danger);
            font-size: 0.78rem;
            font-weight: 600;
        }
        .input-error,
        .input-error:focus {
            border-color: #dc2626;
            box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.14);
        }
        .action-stack {
            display: grid;
            gap: 3px;
            align-content: start;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0;
            min-width: 640px;
        }
        th, td {
            text-align: left;
            padding: 8px 10px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: top;
        }
        th {
            font-size: 0.76rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #64748b;
            background: #f8fafc;
        }
        .table-wrap {
            overflow-x: auto;
            border: 1px solid var(--line);
            border-radius: 12px;
            background: #fff;
        }
        .detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 8px;
            margin: 8px 0;
        }
        .profile-summary-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
            align-items: stretch;
        }
        .detail-grid div {
            background: var(--panel-soft);
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 10px;
        }
        .profile-summary-grid div {
            min-height: 92px;
            padding: 10px 12px;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
        }
        .card-section {
            border-top: 1px solid var(--line);
            padding-top: 12px;
            margin-top: 12px;
        }
        @media (max-width: 1100px) {
            .profile-summary-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
        @media (max-width: 640px) {
            .profile-summary-grid {
                grid-template-columns: 1fr;
            }
        }
        dt {
            font-weight: 700;
            margin-bottom: 3px;
            font-size: 0.74rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            color: #64748b;
        }
        dd {
            margin: 0;
            font-size: 0.94rem;
            font-weight: 600;
        }
        .metric-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 8px;
        }
        .metric-card {
            padding: 12px;
            border-radius: 12px;
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            border: 1px solid var(--line);
        }
        .metric-card strong {
            display: block;
            font-size: 1.25rem;
            line-height: 1.1;
            margin-top: 4px;
        }
        .status-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 2px 8px;
            border-radius: 999px;
            background: #e2e8f0;
            color: #334155;
            font-size: 0.74rem;
            font-weight: 700;
            text-transform: capitalize;
            white-space: nowrap;
        }
        .status-badge.pending,
        .status-badge.scheduled {
            background: #fef3c7;
            color: #92400e;
        }
        .status-badge.draft {
            background: #fef3c7;
            color: #92400e;
        }
        .status-badge.posted,
        .status-badge.active,
        .status-badge.open {
            background: #dcfce7;
            color: #166534;
        }
        .status-badge.under_evaluation {
            background: #dbeafe;
            color: #1d4ed8;
        }
        .status-badge.awarded,
        .status-badge.contracted,
        .status-badge.completed {
            background: #ede9fe;
            color: #6d28d9;
        }
        .status-badge.closed {
            background: #fee2e2;
            color: #991b1b;
        }
        .status-badge.expired,
        .status-badge.archived,
        .status-badge.inactive {
            background: #e5e7eb;
            color: #374151;
        }
        .status-badge.admin {
            background: #dbeafe;
            color: #1d4ed8;
        }
        .status-badge.author {
            background: #ede9fe;
            color: #6d28d9;
        }
        .text-danger {
            color: var(--danger);
        }
        .danger-form button {
            background: var(--danger);
            border-color: var(--danger);
        }
        .danger-form button:hover {
            background: #991b1b;
            border-color: #991b1b;
        }
        .inline-form {
            display: inline-grid;
            gap: 6px;
        }
        @media (max-width: 760px) {
            .shell {
                padding: 10px 10px 16px;
            }
            .topbar,
            .card {
                padding: 12px;
            }
            .topbar {
                flex-wrap: wrap;
            }
            .topbar-right,
            .nav,
            .session-meta {
                flex-wrap: wrap;
            }
            .form-grid.two-col {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="shell">
        <div class="app-shell">
            <header class="topbar">
                <div class="brand">
                    <img src="<?= ViewHelper::escape(ResponseHelper::url('assets/logo-nfa-da.jpg')); ?>" alt="Agency logo">
                    <div>
                        <h1><?= ViewHelper::escape($appName); ?></h1>
                        <p>Official procurement posting and lifecycle management</p>
                    </div>
                </div>
                <div class="topbar-right">
                    <nav class="nav">
                        <?php if ($isAuthenticated): ?>
                            <div class="session-meta">
                                <span>Signed in as <strong><?= ViewHelper::escape($currentUser['username'] ?? ''); ?></strong></span>
                            </div>
                            <a href="<?= ViewHelper::escape(ResponseHelper::url('dashboard')); ?>">Dashboard</a>
                            <a href="<?= ViewHelper::escape(ResponseHelper::url('notices')); ?>">Notices</a>
                            <a href="<?= ViewHelper::escape(ResponseHelper::url('profile')); ?>">Profile</a>
                            <?php if ($isAdmin): ?>
                                <a href="<?= ViewHelper::escape(ResponseHelper::url('users')); ?>">Admin Users</a>
                            <?php endif; ?>
                            <form method="POST" action="<?= ViewHelper::escape(ResponseHelper::url('logout')); ?>" class="session-form">
                                <input type="hidden" name="_token" value="<?= ViewHelper::escape(SecurityHelper::csrfToken()); ?>">
                                <button type="submit">Logout</button>
                            </form>
                        <?php else: ?>
                            <a href="<?= ViewHelper::escape(ResponseHelper::url('login')); ?>">Login</a>
                            <a href="<?= ViewHelper::escape(ResponseHelper::url('register')); ?>">Register</a>
                        <?php endif; ?>
                    </nav>
                </div>
            </header>
            <?php require app('app.views_path') . '/partials/flash.php'; ?>
            <main class="card">
                <?= $content; ?>
            </main>
        </div>
    </div>
</body>
</html>
