<?php

use App\Helpers\ResponseHelper;
use App\Helpers\SecurityHelper;
use App\Helpers\ValidationHelper;
use App\Helpers\ViewHelper;

$filters = is_array($filters ?? null) ? $filters : [];
$activeFilters = array_filter([
    'search' => trim((string) ($filters['search'] ?? '')),
    'region' => trim((string) ($filters['region'] ?? '')),
    'role' => trim((string) ($filters['role'] ?? '')),
    'status' => trim((string) ($filters['status'] ?? '')),
], static fn (string $value): bool => $value !== '');
?>
<div class="page-head">
    <div>
        <h1>User Management</h1>
        <p>Admin-only controls for user account location and access visibility.</p>
    </div>
</div>

<form method="GET" action="<?= ViewHelper::escape(ResponseHelper::url('users')); ?>" class="panel user-filter-panel">
    <div class="form-grid user-filter-grid">
        <div>
            <label for="filter-search">Search name</label>
            <input id="filter-search" name="search" type="search" value="<?= ViewHelper::escape($filters['search'] ?? ''); ?>" placeholder="Enter name or username">
        </div>
        <div>
            <label for="filter-region">Region</label>
            <select id="filter-region" name="region">
                <option value="">All regions</option>
                <?php foreach ($regions as $region): ?>
                    <option value="<?= ViewHelper::escape($region); ?>" <?= ($filters['region'] ?? '') === $region ? 'selected' : ''; ?>>
                        <?= ViewHelper::escape($region); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label for="filter-role">Role</label>
            <select id="filter-role" name="role">
                <option value="">All roles</option>
                <?php foreach ($roles as $role): ?>
                    <option value="<?= ViewHelper::escape($role); ?>" <?= ($filters['role'] ?? '') === $role ? 'selected' : ''; ?>>
                        <?= ViewHelper::escape(ucfirst($role)); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label for="filter-status">Status</label>
            <select id="filter-status" name="status">
                <option value="">All statuses</option>
                <?php foreach ($statuses as $statusValue => $statusLabel): ?>
                    <option value="<?= ViewHelper::escape($statusValue); ?>" <?= ($filters['status'] ?? '') === $statusValue ? 'selected' : ''; ?>>
                        <?= ViewHelper::escape($statusLabel); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="btn-row">
        <button type="submit">Apply Filters</button>
        <?php if ($activeFilters !== []): ?>
            <a class="btn-link" href="<?= ViewHelper::escape(ResponseHelper::url('users')); ?>">Clear</a>
        <?php endif; ?>
    </div>
</form>

<?php if (empty($users)): ?>
    <div class="panel">
        <strong>No users found.</strong>
        <p class="helper-text">Adjust the filters and try again.</p>
    </div>
<?php else: ?>
    <div class="table-wrap">
        <table class="user-management-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Region</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <?php
                    $isViewing = (int) ($editingUserId ?? 0) === (int) $user['id'];
                    $rowOld = $isViewing ? ($editState['old'] ?? []) : [];
                    $rowErrors = $isViewing ? ($editState['errors'] ?? []) : [];
                    $fullName = trim($user['firstname'] . ' ' . ($user['middle_initial'] ? $user['middle_initial'] . '. ' : '') . $user['lastname']);
                    $viewQuery = http_build_query(array_merge($activeFilters, ['view' => (int) $user['id']]));
                    ?>
                    <tr>
                        <td>
                            <strong><?= ViewHelper::escape($fullName); ?></strong>
                            <div class="muted"><?= ViewHelper::escape($user['username']); ?></div>
                        </td>
                        <td><?= ViewHelper::escape($user['region']); ?></td>
                        <td><span class="status-badge <?= ViewHelper::escape((string) $user['role']); ?>"><?= ViewHelper::escape($user['role']); ?></span></td>
                        <td><span class="status-badge <?= (int) $user['is_active'] === 1 ? 'active' : 'inactive'; ?>"><?= (int) $user['is_active'] === 1 ? 'Active' : 'Inactive'; ?></span></td>
                        <td>
                            <?php if ($isViewing): ?>
                                <a class="btn-link" href="<?= ViewHelper::escape(ResponseHelper::url('users' . ($activeFilters === [] ? '' : '?' . http_build_query($activeFilters)))); ?>">Close</a>
                            <?php else: ?>
                                <a class="btn-link" href="<?= ViewHelper::escape(ResponseHelper::url('users?' . $viewQuery)); ?>">View</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php if ($isViewing): ?>
                        <tr class="user-manage-row">
                            <td colspan="5">
                                <div class="panel">
                                    <div class="page-head">
                                        <div>
                                            <h2>Manage User</h2>
                                            <p><?= ViewHelper::escape($fullName); ?> &middot; <?= ViewHelper::escape($rowOld['email'] ?? $user['email']); ?></p>
                                        </div>
                                    </div>
                                    <form method="POST" action="<?= ViewHelper::escape(ResponseHelper::url('users/' . (int) $user['id'] . '/update')); ?>" class="form-grid two-col">
                                        <input type="hidden" name="_token" value="<?= ViewHelper::escape(SecurityHelper::csrfToken()); ?>">
                                        <input type="hidden" name="_filter_search" value="<?= ViewHelper::escape($filters['search'] ?? ''); ?>">
                                        <input type="hidden" name="_filter_region" value="<?= ViewHelper::escape($filters['region'] ?? ''); ?>">
                                        <input type="hidden" name="_filter_role" value="<?= ViewHelper::escape($filters['role'] ?? ''); ?>">
                                        <input type="hidden" name="_filter_status" value="<?= ViewHelper::escape($filters['status'] ?? ''); ?>">
                                        <div>
                                            <label for="username-<?= (int) $user['id']; ?>">Username</label>
                                            <input id="username-<?= (int) $user['id']; ?>" name="username" type="text" value="<?= ViewHelper::escape($rowOld['username'] ?? $user['username']); ?>" class="<?= ViewHelper::escape(ValidationHelper::inputClass($rowErrors, 'username')); ?>" required>
                                            <?php if (ValidationHelper::first($rowErrors, 'username')): ?>
                                                <div class="field-error"><?= ViewHelper::escape((string) ValidationHelper::first($rowErrors, 'username')); ?></div>
                                            <?php endif; ?>
                                        </div>

                                        <?php
                                        $regionFieldId = 'region-' . (int) $user['id'];
                                        $branchFieldId = 'branch-' . (int) $user['id'];
                                        $selectedRegion = $rowOld['region'] ?? ($user['region'] ?? '');
                                        $selectedBranch = $rowOld['branch'] ?? ($user['branch'] ?? '');
                                        $errors = $rowErrors;
                                        require __DIR__ . '/../partials/region_branch_fields.php';
                                        ?>

                                        <div>
                                            <label for="email-<?= (int) $user['id']; ?>">Email address</label>
                                            <input id="email-<?= (int) $user['id']; ?>" name="email" type="email" value="<?= ViewHelper::escape($rowOld['email'] ?? $user['email']); ?>" class="<?= ViewHelper::escape(ValidationHelper::inputClass($rowErrors, 'email')); ?>" required>
                                            <?php if (ValidationHelper::first($rowErrors, 'email')): ?>
                                                <div class="field-error"><?= ViewHelper::escape((string) ValidationHelper::first($rowErrors, 'email')); ?></div>
                                            <?php else: ?>
                                                <p class="helper-text">Email changes use the existing verification flow.</p>
                                            <?php endif; ?>
                                        </div>

                                        <div>
                                            <label for="role-<?= (int) $user['id']; ?>">Role</label>
                                            <select id="role-<?= (int) $user['id']; ?>" name="role" class="<?= ViewHelper::escape(ValidationHelper::inputClass($rowErrors, 'role')); ?>" required>
                                                <?php foreach ($roles as $role): ?>
                                                    <option value="<?= ViewHelper::escape($role); ?>" <?= ($rowOld['role'] ?? $user['role']) === $role ? 'selected' : ''; ?>>
                                                        <?= ViewHelper::escape(ucfirst($role)); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <?php if (ValidationHelper::first($rowErrors, 'role')): ?>
                                                <div class="field-error"><?= ViewHelper::escape((string) ValidationHelper::first($rowErrors, 'role')); ?></div>
                                            <?php endif; ?>
                                        </div>

                                        <div>
                                            <label for="status-<?= (int) $user['id']; ?>">Status</label>
                                            <?php $selectedStatus = $rowOld['status'] ?? ((int) $user['is_active'] === 1 ? 'active' : 'inactive'); ?>
                                            <select id="status-<?= (int) $user['id']; ?>" name="status" class="<?= ViewHelper::escape(ValidationHelper::inputClass($rowErrors, 'status')); ?>" required>
                                                <?php foreach ($statuses as $statusValue => $statusLabel): ?>
                                                    <option value="<?= ViewHelper::escape($statusValue); ?>" <?= $selectedStatus === $statusValue ? 'selected' : ''; ?>>
                                                        <?= ViewHelper::escape($statusLabel); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <?php if (ValidationHelper::first($rowErrors, 'status')): ?>
                                                <div class="field-error"><?= ViewHelper::escape((string) ValidationHelper::first($rowErrors, 'status')); ?></div>
                                            <?php endif; ?>
                                        </div>

                                        <div>
                                            <label for="password-<?= (int) $user['id']; ?>">New password</label>
                                            <input id="password-<?= (int) $user['id']; ?>" name="password" type="password" class="<?= ViewHelper::escape(ValidationHelper::inputClass($rowErrors, 'password')); ?>" autocomplete="new-password">
                                            <?php if (ValidationHelper::first($rowErrors, 'password')): ?>
                                                <div class="field-error"><?= ViewHelper::escape((string) ValidationHelper::first($rowErrors, 'password')); ?></div>
                                            <?php else: ?>
                                                <p class="helper-text">Leave blank to keep the current password.</p>
                                            <?php endif; ?>
                                        </div>

                                        <div>
                                            <label for="password-confirmation-<?= (int) $user['id']; ?>">Confirm new password</label>
                                            <input id="password-confirmation-<?= (int) $user['id']; ?>" name="password_confirmation" type="password" class="<?= ViewHelper::escape(ValidationHelper::inputClass($rowErrors, 'password_confirmation')); ?>" autocomplete="new-password">
                                            <?php if (ValidationHelper::first($rowErrors, 'password_confirmation')): ?>
                                                <div class="field-error"><?= ViewHelper::escape((string) ValidationHelper::first($rowErrors, 'password_confirmation')); ?></div>
                                            <?php endif; ?>
                                        </div>

                                        <?php if (ValidationHelper::first($rowErrors, '_global')): ?>
                                            <div class="field-error user-form-wide"><?= ViewHelper::escape((string) ValidationHelper::first($rowErrors, '_global')); ?></div>
                                        <?php endif; ?>

                                        <div class="btn-row user-form-wide">
                                            <button type="submit">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<style>
    .user-filter-panel {
        margin-bottom: 10px;
    }

    .user-filter-grid {
        grid-template-columns: minmax(220px, 1.3fr) repeat(3, minmax(150px, 1fr));
        margin-bottom: 10px;
    }

    .user-management-table {
        min-width: 760px;
    }

    .user-manage-row td {
        background: #f8fafc;
    }

    .user-manage-row .panel {
        box-shadow: none;
    }

    .user-form-wide {
        grid-column: 1 / -1;
    }

    @media (max-width: 900px) {
        .user-filter-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 640px) {
        .user-filter-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<?php require __DIR__ . '/../partials/region_branch_script.php'; ?>
