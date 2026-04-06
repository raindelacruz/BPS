<?php

use App\Helpers\ResponseHelper;
use App\Helpers\ViewHelper;

$appName = (string) app('app.name', 'Bid Posting System');
$pageTitle = isset($title) ? ViewHelper::escape($title) . ' | ' . ViewHelper::escape($appName) : ViewHelper::escape($appName);
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
            --text: #0f172a;
            --muted: #5b6b7f;
            --accent: #0f766e;
            --accent-dark: #115e59;
            --accent-soft: #ecfdf5;
            --line: #d9e2ec;
            --shadow: 0 16px 36px rgba(15, 23, 42, 0.08);
        }
        * {
            box-sizing: border-box;
        }
        body {
            margin: 0;
            font-family: "Segoe UI", "Helvetica Neue", Arial, sans-serif;
            background:
                radial-gradient(circle at top right, rgba(15, 118, 110, 0.10), transparent 24rem),
                linear-gradient(180deg, #f8fafc 0%, var(--bg) 100%);
            color: var(--text);
        }
        .shell {
            max-width: 1180px;
            margin: 0 auto;
            padding: 18px 18px 28px;
        }
        .hero {
            display: grid;
            grid-template-columns: 84px 1fr auto;
            gap: 12px;
            align-items: center;
            background: rgba(255, 255, 255, 0.92);
            border: 1px solid rgba(217, 226, 236, 0.95);
            border-radius: 18px;
            padding: 14px 16px;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
            margin-bottom: 14px;
        }
        .hero img {
            width: 100%;
            max-width: 72px;
            height: auto;
            object-fit: contain;
            justify-self: center;
            border-radius: 14px;
            background: #fff;
            padding: 6px;
            border: 1px solid rgba(217, 226, 236, 0.95);
        }
        .hero h1 {
            margin: 0 0 4px;
            font-size: 1.35rem;
            color: var(--text);
            line-height: 1.15;
        }
        .hero p {
            margin: 0;
            color: var(--muted);
            font-size: 0.95rem;
        }
        .hero-meta {
            display: inline-flex;
            align-items: center;
            padding: 8px 12px;
            border-radius: 999px;
            background: var(--accent-soft);
            color: var(--accent-dark);
            font-size: 0.84rem;
            font-weight: 700;
            white-space: nowrap;
        }
        .card {
            background: var(--panel);
            border: 1px solid rgba(217, 226, 236, 0.95);
            border-radius: 18px;
            box-shadow: var(--shadow);
            padding: 18px;
        }
        .flash {
            margin: 0 0 12px;
            padding: 11px 13px;
            border-radius: 12px;
            border: 1px solid #bfdbfe;
            background: #eff6ff;
            color: #1e3a8a;
        }
        form {
            display: grid;
            gap: 12px;
        }
        label {
            font-size: 14px;
            font-weight: 600;
            color: #334155;
        }
        input, button, select, textarea {
            font: inherit;
            width: 100%;
            padding: 10px 12px;
            border-radius: 10px;
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
        }
        .field-error {
            margin: 4px 0 0;
            color: #b91c1c;
            font-size: 0.78rem;
            font-weight: 600;
        }
        .input-error,
        .input-error:focus {
            border-color: #dc2626;
            box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.14);
        }
        a {
            color: #0f5f8c;
            text-decoration: none;
            font-weight: 600;
        }
        a:hover {
            text-decoration: underline;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0;
            min-width: 700px;
        }
        th, td {
            text-align: left;
            padding: 11px 12px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: top;
        }
        th {
            font-size: 0.82rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #64748b;
            background: #f8fafc;
        }
        .detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 10px;
            margin: 12px 0;
        }
        .detail-grid div {
            background: #f8fafc;
            border-radius: 12px;
            border: 1px solid var(--line);
            padding: 12px;
        }
        dt {
            font-weight: 700;
            margin-bottom: 4px;
        }
        dd {
            margin: 0;
        }
        .public-tools {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: end;
            margin-top: 0;
            padding: 14px;
            border: 1px solid var(--line);
            border-radius: 14px;
            background: var(--panel-soft);
        }
        .public-tools .field {
            display: grid;
            gap: 6px;
            min-width: 180px;
            flex: 1 1 180px;
        }
        .public-tools .field.search {
            flex: 2 1 280px;
        }
        .public-tools .actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .public-tools .actions a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 42px;
            padding: 0 14px;
            border-radius: 10px;
            border: 1px solid var(--line);
            background: #fff;
        }
        .section-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
        }
        .section-head h2 {
            margin: 0;
            font-size: 1.2rem;
            color: var(--text);
        }
        .section-head p {
            margin: 0;
            color: var(--muted);
            font-size: 0.95rem;
        }
        .public-table-wrap {
            overflow-x: auto;
            border: 1px solid var(--line);
            border-radius: 14px;
            background: #fff;
        }
        .public-table-wrap table {
            margin-top: 0;
        }
        .status-pill {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 999px;
            background: var(--accent-soft);
            color: #166534;
            font-size: 0.82rem;
            font-weight: 700;
        }
        .results-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin: 10px 0 14px;
        }
        .results-meta span {
            display: inline-flex;
            align-items: center;
            min-height: 32px;
            padding: 0 10px;
            border-radius: 999px;
            background: #f1f5f9;
            color: #475569;
            font-size: 0.84rem;
            font-weight: 600;
        }
        @media (max-width: 700px) {
            .hero {
                grid-template-columns: 1fr;
                text-align: center;
            }
            .section-head {
                align-items: start;
                flex-direction: column;
            }
            .hero-meta {
                justify-self: center;
            }
            .public-tools .actions {
                width: 100%;
            }
            .public-tools .actions button,
            .public-tools .actions a {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="shell">
        <section class="hero">
            <img src="<?= ViewHelper::escape(ResponseHelper::url('assets/logo-nfa-da.jpg')); ?>" alt="Department of Agriculture and National Food Authority logo">
            <div>
                <h1><?= ViewHelper::escape($appName); ?></h1>
                <p>Official public posting page for bid notices and related procurement documents.</p>
            </div>
            <div class="hero-meta">Public access</div>
        </section>
        <?php require app('app.views_path') . '/partials/flash.php'; ?>
        <main class="card">
            <?= $content; ?>
        </main>
    </div>
</body>
</html>
