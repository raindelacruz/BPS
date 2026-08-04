<?php

use App\Helpers\ResponseHelper;
use App\Helpers\ViewHelper;

$filters = $filters ?? [];
$logs = $logs ?? [];
?>
<div class="section-stack">
    <div class="page-head">
        <div>
            <h1>Login Logs</h1>
            <p>Review login-related events to diagnose why users could not access the system.</p>
        </div>
    </div>

    <form method="GET" action="<?= ViewHelper::escape(ResponseHelper::url('login-logs')); ?>" class="panel">
        <div class="form-grid two-col">
            <div>
                <label for="filter-search">Search</label>
                <input id="filter-search" name="search" type="search" value="<?= ViewHelper::escape($filters['search'] ?? ''); ?>" placeholder="Username or IP address">
            </div>
            <div>
                <label for="filter-outcome">Outcome</label>
                <select id="filter-outcome" name="outcome">
                    <option value="">All outcomes</option>
                    <?php foreach (($outcomes ?? []) as $value => $label): ?>
                        <option value="<?= ViewHelper::escape($value); ?>" <?= ($filters['outcome'] ?? '') === $value ? 'selected' : ''; ?>>
                            <?= ViewHelper::escape($label); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="filter-event-type">Event</label>
                <select id="filter-event-type" name="event_type">
                    <option value="">All events</option>
                    <?php foreach (($eventTypes ?? []) as $value => $label): ?>
                        <option value="<?= ViewHelper::escape($value); ?>" <?= ($filters['event_type'] ?? '') === $value ? 'selected' : ''; ?>>
                            <?= ViewHelper::escape($label); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="filter-failure-reason">Reason</label>
                <select id="filter-failure-reason" name="failure_reason">
                    <option value="">All reasons</option>
                    <?php foreach (($failureReasons ?? []) as $value => $label): ?>
                        <option value="<?= ViewHelper::escape($value); ?>" <?= ($filters['failure_reason'] ?? '') === $value ? 'selected' : ''; ?>>
                            <?= ViewHelper::escape($label); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="btn-row">
            <button type="submit">Filter</button>
            <a class="btn-link" href="<?= ViewHelper::escape(ResponseHelper::url('login-logs')); ?>">Clear</a>
        </div>
    </form>

    <?php if ($logs === []): ?>
        <div class="panel">
            <strong>No login logs found.</strong>
            <p class="muted">New login-related events will appear here after users attempt to sign in.</p>
        </div>
    <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>User</th>
                        <th>Event</th>
                        <th>Outcome</th>
                        <th>Reason</th>
                        <th>Message</th>
                        <th>IP</th>
                        <th>Browser</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $log): ?>
                        <?php
                        $userLabel = trim((string) ($log['firstname'] ?? '') . ' ' . (string) ($log['lastname'] ?? ''));
                        $username = (string) ($log['username'] ?? $log['username_entered'] ?? '');
                        $context = json_decode((string) ($log['context'] ?? ''), true);
                        ?>
                        <tr>
                            <td><?= ViewHelper::escape($log['created_at'] ?? ''); ?></td>
                            <td>
                                <strong><?= ViewHelper::escape($username !== '' ? $username : 'Unknown'); ?></strong>
                                <?php if ($userLabel !== ''): ?>
                                    <div class="muted"><?= ViewHelper::escape($userLabel); ?></div>
                                <?php endif; ?>
                                <?php if (($log['username_entered'] ?? '') !== '' && ($log['username_entered'] ?? '') !== $username): ?>
                                    <div class="muted">Entered: <?= ViewHelper::escape($log['username_entered']); ?></div>
                                <?php endif; ?>
                            </td>
                            <td><?= ViewHelper::escape(str_replace('_', ' ', (string) ($log['event_type'] ?? ''))); ?></td>
                            <td><span class="status-badge <?= ViewHelper::escape((string) ($log['outcome'] ?? '')); ?>"><?= ViewHelper::escape((string) ($log['outcome'] ?? '')); ?></span></td>
                            <td><?= ViewHelper::escape(str_replace('_', ' ', (string) ($log['failure_reason'] ?? ''))); ?></td>
                            <td>
                                <?= ViewHelper::escape($log['message'] ?? ''); ?>
                                <?php if (is_array($context) && $context !== []): ?>
                                    <div class="muted"><?= ViewHelper::escape(json_encode($context, JSON_UNESCAPED_SLASHES)); ?></div>
                                <?php endif; ?>
                            </td>
                            <td><?= ViewHelper::escape($log['ip_address'] ?? ''); ?></td>
                            <td class="muted"><?= ViewHelper::escape($log['user_agent'] ?? ''); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
