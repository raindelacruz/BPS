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
            position: relative;
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
        .menu-toggle {
            display: none;
            align-items: center;
            justify-content: center;
            width: 42px;
            min-width: 42px;
            height: 42px;
            padding: 0;
            border-radius: 10px;
            border: 1px solid var(--line);
            background: #fff;
            color: var(--text);
        }
        .menu-toggle:hover {
            background: var(--panel-soft);
            border-color: var(--line);
            color: var(--accent-dark);
        }
        .menu-toggle span,
        .menu-toggle::before,
        .menu-toggle::after {
            display: block;
            width: 20px;
            height: 2px;
            border-radius: 999px;
            background: currentColor;
            content: "";
        }
        .menu-toggle span {
            position: relative;
        }
        .menu-toggle::before {
            transform: translateY(-7px);
            position: absolute;
        }
        .menu-toggle::after {
            transform: translateY(7px);
            position: absolute;
        }
        .topbar-right {
            display: flex;
            flex-wrap: nowrap;
            justify-content: flex-end;
            gap: 10px;
            align-items: center;
            min-width: 0;
        }
        .mobile-menu-head,
        .mobile-menu-backdrop {
            display: none;
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
        .table-pagination {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            padding: 10px;
            border-top: 1px solid var(--line);
            background: #fff;
        }
        .table-pagination-info {
            color: var(--muted);
            font-size: 0.84rem;
            font-weight: 600;
        }
        .table-pagination-controls {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            align-items: center;
            justify-content: flex-end;
        }
        .table-pagination button {
            min-width: 34px;
            min-height: 34px;
            padding: 0 10px;
            border-radius: 8px;
            border: 1px solid var(--line);
            background: #fff;
            color: var(--muted);
            font-size: 0.86rem;
            font-weight: 700;
        }
        .table-pagination button:hover:not([disabled]),
        .table-pagination button.is-active {
            background: var(--accent);
            border-color: var(--accent);
            color: #fff;
        }
        .table-pagination button[disabled] {
            cursor: not-allowed;
            opacity: 0.55;
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
        .status-badge.success,
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
        .status-badge.failure {
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
        .btn-secondary {
            background: #ffffff;
            border-color: var(--line);
            color: var(--muted);
        }
        .btn-secondary:hover {
            background: var(--panel-soft);
            border-color: var(--line);
            color: var(--accent-dark);
        }
        .btn-loading,
        button[disabled] {
            cursor: wait;
            opacity: 0.7;
        }
        .btn-secondary[disabled] {
            color: #94a3b8;
            background: #f8fafc;
        }
        .modal {
            position: fixed;
            inset: 0;
            z-index: 1055;
            display: none;
            padding: 18px 12px;
            overflow-y: auto;
            background: rgba(15, 23, 42, 0.54);
        }
        .modal.is-open {
            display: block;
        }
        .modal-dialog {
            width: min(100%, 900px);
            margin: 0 auto;
            min-height: calc(100vh - 36px);
            display: flex;
            align-items: center;
        }
        .modal-content {
            width: 100%;
            background: #ffffff;
            border: 1px solid rgba(217, 226, 236, 0.95);
            border-radius: 16px;
            box-shadow: 0 20px 44px rgba(15, 23, 42, 0.22);
            overflow: hidden;
        }
        .modal-header,
        .modal-body,
        .modal-footer {
            padding: 16px 18px;
        }
        .modal-header,
        .modal-footer {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            border-bottom: 1px solid var(--line);
        }
        .modal-footer {
            border-top: 1px solid var(--line);
            border-bottom: 0;
        }
        .modal-title {
            margin: 0;
            font-size: 1.1rem;
        }
        .modal-subtitle {
            margin: 4px 0 0;
            color: var(--muted);
            font-size: 0.92rem;
        }
        .btn-close {
            width: 40px;
            min-width: 40px;
            min-height: 40px;
            padding: 0;
            border-radius: 999px;
            border: 1px solid var(--line);
            background: #fff;
            color: var(--muted);
            font-size: 1.35rem;
            line-height: 1;
        }
        .btn-close:hover {
            background: var(--panel-soft);
            color: var(--accent-dark);
        }
        .confirm-summary {
            display: grid;
            gap: 14px;
        }
        .confirm-summary-group {
            border: 1px solid var(--line);
            border-radius: 12px;
            background: #fff;
            overflow: hidden;
        }
        .confirm-summary-group-title {
            margin: 0;
            padding: 10px 14px;
            background: var(--panel-soft);
            border-bottom: 1px solid var(--line);
            font-size: 0.88rem;
            letter-spacing: 0.03em;
            text-transform: uppercase;
            color: #475569;
        }
        .confirm-summary-table {
            width: 100%;
            min-width: 0;
        }
        .confirm-summary-table th {
            width: 34%;
            background: transparent;
        }
        .confirm-summary-table td {
            white-space: pre-wrap;
            word-break: break-word;
        }
        .confirm-verification {
            display: grid;
            gap: 6px;
            width: 100%;
            padding: 12px 14px;
            border-radius: 12px;
            border: 1px solid var(--line);
            background: var(--panel-soft);
        }
        .confirm-verification label {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-weight: 700;
            color: var(--text);
        }
        .confirm-verification input[type="checkbox"] {
            width: 18px;
            min-width: 18px;
            height: 18px;
            margin-top: 2px;
            padding: 0;
        }
        .confirm-verification p {
            margin: 0;
            color: var(--muted);
            font-size: 0.82rem;
        }
        .modal-open {
            overflow: hidden;
        }
        body.mobile-menu-open {
            overflow: hidden;
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
                flex-wrap: nowrap;
            }
            .brand {
                min-width: 0;
                flex: 1 1 calc(100% - 54px);
            }
            .brand h1 {
                font-size: 0.98rem;
            }
            .brand p {
                font-size: 0.76rem;
            }
            .menu-toggle {
                display: inline-flex;
                position: relative;
                z-index: 1042;
            }
            .topbar-right {
                display: flex;
                position: fixed;
                top: 0;
                left: 0;
                z-index: 1041;
                width: min(82vw, 320px);
                height: 100dvh;
                min-width: 0;
                padding: 12px;
                background: #ffffff;
                border-right: 1px solid var(--line);
                box-shadow: 18px 0 42px rgba(15, 23, 42, 0.24);
                transform: translateX(-105%);
                transition: transform 0.22s ease;
                flex-direction: column;
                align-items: stretch;
                justify-content: flex-start;
                overflow-y: auto;
            }
            .topbar.menu-open .topbar-right {
                transform: translateX(0);
            }
            .mobile-menu-backdrop {
                position: fixed;
                inset: 0;
                z-index: 1040;
                display: block;
                background: rgba(15, 23, 42, 0.54);
                border: 0;
                border-radius: 0;
                padding: 0;
                opacity: 0;
                pointer-events: none;
                transition: opacity 0.2s ease;
            }
            body.mobile-menu-open .mobile-menu-backdrop {
                opacity: 1;
                pointer-events: auto;
            }
            .mobile-menu-head {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 10px;
                padding-bottom: 10px;
                border-bottom: 1px solid var(--line);
                color: var(--text);
                font-size: 0.9rem;
                font-weight: 800;
            }
            .menu-close {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 34px;
                min-width: 34px;
                height: 34px;
                padding: 0;
                border-radius: 8px;
                border: 1px solid var(--line);
                background: #fff;
                color: var(--muted);
                font-size: 1.25rem;
                line-height: 1;
            }
            .menu-close:hover {
                background: var(--panel-soft);
                border-color: var(--line);
                color: var(--accent-dark);
            }
            .topbar-right,
            .nav,
            .session-meta {
                flex-wrap: wrap;
            }
            .nav,
            .session-meta,
            .session-form,
            .session-form button,
            .nav a {
                width: 100%;
            }
            .nav {
                align-items: stretch;
                gap: 8px;
            }
            .nav a,
            .session-form button {
                justify-content: flex-start;
                min-height: 42px;
            }
            .table-pagination {
                align-items: stretch;
            }
            .table-pagination-info,
            .table-pagination-controls {
                width: 100%;
            }
            .table-pagination-controls {
                justify-content: center;
            }
            .form-grid.two-col {
                grid-template-columns: 1fr;
            }
            .modal {
                padding: 12px 8px;
            }
            .modal-dialog {
                min-height: calc(100vh - 24px);
            }
            .modal-header,
            .modal-body,
            .modal-footer {
                padding: 14px;
            }
            .modal-footer .btn-row {
                width: 100%;
            }
            .modal-footer .btn-row button {
                width: 100%;
            }
            .confirm-summary-table th,
            .confirm-summary-table td {
                display: block;
                width: 100%;
            }
            .confirm-summary-table th {
                padding-bottom: 2px;
                border-bottom: 0;
            }
            .confirm-summary-table td {
                padding-top: 0;
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
                <button class="menu-toggle" type="button" aria-label="Open menu" aria-expanded="false" aria-controls="primary-navigation">
                    <span></span>
                </button>
                <button class="mobile-menu-backdrop" type="button" aria-label="Close menu"></button>
                <div class="topbar-right">
                    <div class="mobile-menu-head">
                        <span>Menu</span>
                        <button class="menu-close" type="button" aria-label="Close menu">&times;</button>
                    </div>
                    <nav class="nav" id="primary-navigation">
                        <?php if ($isAuthenticated): ?>
                            <div class="session-meta">
                                <span>Signed in as <strong><?= ViewHelper::escape($currentUser['username'] ?? ''); ?></strong></span>
                            </div>
                            <a href="<?= ViewHelper::escape(ResponseHelper::url('dashboard')); ?>">Dashboard</a>
                            <a href="<?= ViewHelper::escape(ResponseHelper::url('notices')); ?>">Notices</a>
                            <a href="<?= ViewHelper::escape(ResponseHelper::url('profile')); ?>">Profile</a>
                            <?php if ($isAdmin): ?>
                                <a href="<?= ViewHelper::escape(ResponseHelper::url('users')); ?>">Admin Users</a>
                                <a href="<?= ViewHelper::escape(ResponseHelper::url('login-logs')); ?>">Login Logs</a>
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
    <?php require app('app.views_path') . '/partials/form_confirmation_modal.php'; ?>
    <script src="<?= ViewHelper::escape(ResponseHelper::url('assets/js/form-confirmation.js')); ?>" defer></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var topbar = document.querySelector('.topbar');
            var menuToggle = document.querySelector('.menu-toggle');
            var menuClose = document.querySelector('.menu-close');
            var menuBackdrop = document.querySelector('.mobile-menu-backdrop');
            var mobileMenuLinks = document.querySelectorAll('#primary-navigation a');

            if (topbar && menuToggle) {
                function setMenuOpen(isOpen) {
                    topbar.classList.toggle('menu-open', isOpen);
                    document.body.classList.toggle('mobile-menu-open', isOpen);
                    menuToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                    menuToggle.setAttribute('aria-label', isOpen ? 'Close menu' : 'Open menu');
                }

                menuToggle.addEventListener('click', function () {
                    setMenuOpen(!topbar.classList.contains('menu-open'));
                });

                if (menuClose) {
                    menuClose.addEventListener('click', function () {
                        setMenuOpen(false);
                    });
                }

                if (menuBackdrop) {
                    menuBackdrop.addEventListener('click', function () {
                        setMenuOpen(false);
                    });
                }

                mobileMenuLinks.forEach(function (link) {
                    link.addEventListener('click', function () {
                        setMenuOpen(false);
                    });
                });

                document.addEventListener('keydown', function (event) {
                    if (event.key === 'Escape') {
                        setMenuOpen(false);
                    }
                });
            }

            document.querySelectorAll('.table-wrap').forEach(function (wrap) {
                var table = wrap.querySelector('table');
                if (!table || table.classList.contains('confirm-summary-table')) {
                    return;
                }

                var tbody = table.tBodies[0];
                if (!tbody) {
                    return;
                }

                var rows = Array.prototype.slice.call(tbody.rows);
                var primaryRows = rows.filter(function (row) {
                    return !row.classList.contains('user-manage-row');
                });
                var rowsPerPage = 10;
                var totalPages = Math.ceil(primaryRows.length / rowsPerPage);
                var currentPage = 1;

                if (totalPages <= 1) {
                    return;
                }

                var pagination = document.createElement('div');
                pagination.className = 'table-pagination';
                var info = document.createElement('div');
                info.className = 'table-pagination-info';
                var controls = document.createElement('div');
                controls.className = 'table-pagination-controls';
                pagination.appendChild(info);
                pagination.appendChild(controls);
                wrap.appendChild(pagination);

                function rowGroup(row) {
                    var group = [row];
                    var next = row.nextElementSibling;
                    if (next && next.classList.contains('user-manage-row')) {
                        group.push(next);
                    }
                    return group;
                }

                function render() {
                    var start = (currentPage - 1) * rowsPerPage;
                    var end = start + rowsPerPage;

                    rows.forEach(function (row) {
                        row.hidden = true;
                    });

                    primaryRows.slice(start, end).forEach(function (row) {
                        rowGroup(row).forEach(function (groupRow) {
                            groupRow.hidden = false;
                        });
                    });

                    info.textContent = 'Showing ' + (start + 1) + '-' + Math.min(end, primaryRows.length) + ' of ' + primaryRows.length;
                    controls.innerHTML = '';
                    addButton('Previous', currentPage - 1, currentPage === 1);

                    for (var page = 1; page <= totalPages; page += 1) {
                        addButton(String(page), page, false, page === currentPage);
                    }

                    addButton('Next', currentPage + 1, currentPage === totalPages);
                }

                function addButton(label, page, disabled, active) {
                    var button = document.createElement('button');
                    button.type = 'button';
                    button.textContent = label;
                    button.disabled = disabled;
                    if (active) {
                        button.className = 'is-active';
                        button.setAttribute('aria-current', 'page');
                    }
                    button.addEventListener('click', function () {
                        currentPage = Math.max(1, Math.min(totalPages, page));
                        render();
                    });
                    controls.appendChild(button);
                }

                render();
            });
        });
    </script>
</body>
</html>
