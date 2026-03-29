<?php

use App\Helpers\ResponseHelper;
use App\Helpers\SecurityHelper;
use App\Helpers\ViewHelper;
?>
<div class="page-head">
    <div>
        <h1>User Management</h1>
        <p>Admin-only controls for user details, activation, role assignment, and safe deletion with notice reassignment.</p>
    </div>
</div>

<?php if (empty($users)): ?>
    <p>No users found.</p>
<?php else: ?>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Full Name</th>
                    <th>Region</th>
                    <th>Branch</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td>
                            <strong><?= ViewHelper::escape(trim($user['firstname'] . ' ' . ($user['middle_initial'] ? $user['middle_initial'] . '. ' : '') . $user['lastname'])); ?></strong>
                            <div class="muted"><?= ViewHelper::escape($user['email']); ?></div>
                        </td>
                        <td><?= ViewHelper::escape($user['region']); ?></td>
                        <td><?= ViewHelper::escape($user['branch'] ?? 'No branch assigned'); ?></td>
                        <td>
                            <div class="stack-sm">
                                <span class="status-badge <?= (int) $user['is_active'] === 1 ? 'active' : 'inactive'; ?>"><?= (int) $user['is_active'] === 1 ? 'Active' : 'Inactive'; ?></span>
                                <span class="status-badge <?= ViewHelper::escape((string) $user['role']); ?>"><?= ViewHelper::escape($user['role']); ?></span>
                            </div>
                        </td>
                        <td>
                            <div class="stack-sm">
                                <details>
                                    <summary><a href="#" onclick="return false;">Edit</a></summary>
                                    <div class="panel" style="margin-top: 10px; min-width: 300px;">
                                        <form method="POST" action="<?= ViewHelper::escape(ResponseHelper::url('users/' . (int) $user['id'] . '/update')); ?>" class="form-grid">
                                            <input type="hidden" name="_token" value="<?= ViewHelper::escape(SecurityHelper::csrfToken()); ?>">

                                            <div>
                                                <label for="username-<?= (int) $user['id']; ?>">Username</label>
                                                <input id="username-<?= (int) $user['id']; ?>" name="username" type="text" value="<?= ViewHelper::escape($user['username']); ?>" required>
                                            </div>

                                            <div>
                                                <label for="firstname-<?= (int) $user['id']; ?>">First name</label>
                                                <input id="firstname-<?= (int) $user['id']; ?>" name="firstname" type="text" value="<?= ViewHelper::escape($user['firstname']); ?>" required>
                                            </div>

                                            <div>
                                                <label for="middle-<?= (int) $user['id']; ?>">Middle initial</label>
                                                <input id="middle-<?= (int) $user['id']; ?>" name="middle_initial" type="text" maxlength="1" value="<?= ViewHelper::escape($user['middle_initial'] ?? ''); ?>">
                                            </div>

                                            <div>
                                                <label for="lastname-<?= (int) $user['id']; ?>">Last name</label>
                                                <input id="lastname-<?= (int) $user['id']; ?>" name="lastname" type="text" value="<?= ViewHelper::escape($user['lastname']); ?>" required>
                                            </div>

                                            <?php
                                            $regionFieldId = 'region-' . (int) $user['id'];
                                            $branchFieldId = 'branch-' . (int) $user['id'];
                                            $selectedRegion = $user['region'] ?? '';
                                            $selectedBranch = $user['branch'] ?? '';
                                            require __DIR__ . '/../partials/region_branch_fields.php';
                                            ?>

                                            <div>
                                                <label for="email-<?= (int) $user['id']; ?>">Email</label>
                                                <input id="email-<?= (int) $user['id']; ?>" name="email" type="email" value="<?= ViewHelper::escape($user['email']); ?>" required>
                                            </div>

                                            <div>
                                                <label for="role-<?= (int) $user['id']; ?>">Role</label>
                                                <select id="role-<?= (int) $user['id']; ?>" name="role" required>
                                                    <?php foreach ($roles as $role): ?>
                                                        <option value="<?= ViewHelper::escape($role); ?>" <?= $user['role'] === $role ? 'selected' : ''; ?>>
                                                            <?= ViewHelper::escape($role); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>

                                            <div class="btn-row">
                                                <button type="submit">Save</button>
                                            </div>
                                        </form>
                                    </div>
                                </details>

                                <form method="POST" action="<?= ViewHelper::escape(ResponseHelper::url('users/' . (int) $user['id'] . '/toggle-active')); ?>" class="inline-form">
                                    <input type="hidden" name="_token" value="<?= ViewHelper::escape(SecurityHelper::csrfToken()); ?>">
                                    <button type="submit"><?= (int) $user['is_active'] === 1 ? 'Deactivate' : 'Activate'; ?></button>
                                </form>

                                <form method="POST" action="<?= ViewHelper::escape(ResponseHelper::url('users/' . (int) $user['id'] . '/delete')); ?>" class="inline-form danger-form">
                                    <input type="hidden" name="_token" value="<?= ViewHelper::escape(SecurityHelper::csrfToken()); ?>">
                                    <button type="submit">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/../partials/region_branch_script.php'; ?>
