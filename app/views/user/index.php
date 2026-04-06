<?php

use App\Helpers\ResponseHelper;
use App\Helpers\SecurityHelper;
use App\Helpers\ValidationHelper;
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
                    <?php
                    $isEditing = (int) ($editingUserId ?? 0) === (int) $user['id'];
                    $rowOld = $isEditing ? ($editState['old'] ?? []) : [];
                    $rowErrors = $isEditing ? ($editState['errors'] ?? []) : [];
                    ?>
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
                                <details <?= $isEditing ? 'open' : ''; ?>>
                                    <summary><a href="#" onclick="return false;">Edit</a></summary>
                                    <div class="panel" style="margin-top: 10px; min-width: 300px;">
                                        <form method="POST" action="<?= ViewHelper::escape(ResponseHelper::url('users/' . (int) $user['id'] . '/update')); ?>" class="form-grid">
                                            <input type="hidden" name="_token" value="<?= ViewHelper::escape(SecurityHelper::csrfToken()); ?>">

                                            <div>
                                                <label for="username-<?= (int) $user['id']; ?>">Username</label>
                                                <input id="username-<?= (int) $user['id']; ?>" name="username" type="text" value="<?= ViewHelper::escape($rowOld['username'] ?? $user['username']); ?>" class="<?= ViewHelper::escape(ValidationHelper::inputClass($rowErrors, 'username')); ?>" required>
                                                <?php if (ValidationHelper::first($rowErrors, 'username')): ?>
                                                    <div class="field-error"><?= ViewHelper::escape((string) ValidationHelper::first($rowErrors, 'username')); ?></div>
                                                <?php endif; ?>
                                            </div>

                                            <div>
                                                <label for="firstname-<?= (int) $user['id']; ?>">First name</label>
                                                <input id="firstname-<?= (int) $user['id']; ?>" name="firstname" type="text" value="<?= ViewHelper::escape($rowOld['firstname'] ?? $user['firstname']); ?>" class="<?= ViewHelper::escape(ValidationHelper::inputClass($rowErrors, 'firstname')); ?>" required>
                                                <?php if (ValidationHelper::first($rowErrors, 'firstname')): ?>
                                                    <div class="field-error"><?= ViewHelper::escape((string) ValidationHelper::first($rowErrors, 'firstname')); ?></div>
                                                <?php endif; ?>
                                            </div>

                                            <div>
                                                <label for="middle-<?= (int) $user['id']; ?>">Middle initial</label>
                                                <input id="middle-<?= (int) $user['id']; ?>" name="middle_initial" type="text" maxlength="1" value="<?= ViewHelper::escape($rowOld['middle_initial'] ?? ($user['middle_initial'] ?? '')); ?>" class="<?= ViewHelper::escape(ValidationHelper::inputClass($rowErrors, 'middle_initial')); ?>">
                                                <?php if (ValidationHelper::first($rowErrors, 'middle_initial')): ?>
                                                    <div class="field-error"><?= ViewHelper::escape((string) ValidationHelper::first($rowErrors, 'middle_initial')); ?></div>
                                                <?php endif; ?>
                                            </div>

                                            <div>
                                                <label for="lastname-<?= (int) $user['id']; ?>">Last name</label>
                                                <input id="lastname-<?= (int) $user['id']; ?>" name="lastname" type="text" value="<?= ViewHelper::escape($rowOld['lastname'] ?? $user['lastname']); ?>" class="<?= ViewHelper::escape(ValidationHelper::inputClass($rowErrors, 'lastname')); ?>" required>
                                                <?php if (ValidationHelper::first($rowErrors, 'lastname')): ?>
                                                    <div class="field-error"><?= ViewHelper::escape((string) ValidationHelper::first($rowErrors, 'lastname')); ?></div>
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
                                                <label for="email-<?= (int) $user['id']; ?>">Email</label>
                                                <input id="email-<?= (int) $user['id']; ?>" name="email" type="email" value="<?= ViewHelper::escape($rowOld['email'] ?? $user['email']); ?>" class="<?= ViewHelper::escape(ValidationHelper::inputClass($rowErrors, 'email')); ?>" required>
                                                <?php if (ValidationHelper::first($rowErrors, 'email')): ?>
                                                    <div class="field-error"><?= ViewHelper::escape((string) ValidationHelper::first($rowErrors, 'email')); ?></div>
                                                <?php endif; ?>
                                            </div>

                                            <div>
                                                <label for="role-<?= (int) $user['id']; ?>">Role</label>
                                                <select id="role-<?= (int) $user['id']; ?>" name="role" class="<?= ViewHelper::escape(ValidationHelper::inputClass($rowErrors, 'role')); ?>" required>
                                                    <?php foreach ($roles as $role): ?>
                                                        <option value="<?= ViewHelper::escape($role); ?>" <?= ($rowOld['role'] ?? $user['role']) === $role ? 'selected' : ''; ?>>
                                                            <?= ViewHelper::escape($role); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <?php if (ValidationHelper::first($rowErrors, 'role')): ?>
                                                    <div class="field-error"><?= ViewHelper::escape((string) ValidationHelper::first($rowErrors, 'role')); ?></div>
                                                <?php endif; ?>
                                            </div>

                                            <?php if (ValidationHelper::first($rowErrors, '_global')): ?>
                                                <div class="field-error"><?= ViewHelper::escape((string) ValidationHelper::first($rowErrors, '_global')); ?></div>
                                            <?php endif; ?>

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
