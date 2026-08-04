<?php

use App\Helpers\ResponseHelper;
use App\Helpers\SecurityHelper;
use App\Helpers\ValidationHelper;
use App\Helpers\ViewHelper;
?>
<div class="page-head">
    <div>
        <h1>My Account</h1>
        <p>Update your account details and password.</p>
    </div>
</div>

<dl class="detail-grid profile-summary-grid">
    <div>
        <dt>Username</dt>
        <dd><?= ViewHelper::escape($user['username']); ?></dd>
    </div>
    <div>
        <dt>Name</dt>
        <dd><?= ViewHelper::escape(trim($user['firstname'] . ' ' . $user['lastname'])); ?></dd>
    </div>
    <div>
        <dt>Role</dt>
        <dd><?= ViewHelper::escape($user['role']); ?></dd>
    </div>
    <div>
        <dt>Status</dt>
        <dd><?= (int) $user['is_active'] === 1 ? 'Active' : 'Inactive'; ?></dd>
    </div>
</dl>

<div class="card-section stack-sm">
    <div class="page-head">
        <div>
            <h2>Account Details</h2>
            <p>Update your email, region, and branch assignment.</p>
        </div>
    </div>
    <form method="POST" action="<?= ViewHelper::escape(ResponseHelper::url('profile')); ?>" class="form-grid two-col">
        <input type="hidden" name="_token" value="<?= ViewHelper::escape(SecurityHelper::csrfToken()); ?>">

        <div>
            <label for="email">Email</label>
            <input id="email" name="email" type="email" value="<?= ViewHelper::escape($profileOld['email'] ?? $user['email']); ?>" class="<?= ViewHelper::escape(ValidationHelper::inputClass($profileErrors, 'email')); ?>" required>
            <?php if (ValidationHelper::first($profileErrors, 'email')): ?>
                <div class="field-error"><?= ViewHelper::escape((string) ValidationHelper::first($profileErrors, 'email')); ?></div>
            <?php endif; ?>
        </div>

        <div></div>

        <?php
        $regionFieldId = 'region';
        $branchFieldId = 'branch';
        $selectedRegion = $profileOld['region'] ?? ($user['region'] ?? '');
        $selectedBranch = $profileOld['branch'] ?? ($user['branch'] ?? '');
        require __DIR__ . '/../partials/region_branch_fields.php';
        ?>

        <div class="btn-row" style="grid-column: 1 / -1;">
            <button type="submit">Update profile</button>
        </div>
        <?php if (ValidationHelper::first($profileErrors, '_global')): ?>
            <div class="field-error" style="grid-column: 1 / -1;"><?= ViewHelper::escape((string) ValidationHelper::first($profileErrors, '_global')); ?></div>
        <?php endif; ?>
    </form>
</div>

<div class="card-section stack-sm">
    <div class="page-head">
        <div>
            <h2>Change Password</h2>
            <p>Keep your account secure by updating your password when needed.</p>
        </div>
    </div>
    <form method="POST" action="<?= ViewHelper::escape(ResponseHelper::url('profile/password')); ?>" class="form-grid two-col">
        <input type="hidden" name="_token" value="<?= ViewHelper::escape(SecurityHelper::csrfToken()); ?>">

        <div>
            <label for="password">New password</label>
            <input id="password" name="password" type="password" class="<?= ViewHelper::escape(ValidationHelper::inputClass($passwordErrors, 'password')); ?>" required>
            <?php if (ValidationHelper::first($passwordErrors, 'password')): ?>
                <div class="field-error"><?= ViewHelper::escape((string) ValidationHelper::first($passwordErrors, 'password')); ?></div>
            <?php endif; ?>
        </div>

        <div>
            <label for="password_confirmation">Confirm new password</label>
            <input id="password_confirmation" name="password_confirmation" type="password" class="<?= ViewHelper::escape(ValidationHelper::inputClass($passwordErrors, 'password_confirmation')); ?>" required>
            <?php if (ValidationHelper::first($passwordErrors, 'password_confirmation')): ?>
                <div class="field-error"><?= ViewHelper::escape((string) ValidationHelper::first($passwordErrors, 'password_confirmation')); ?></div>
            <?php endif; ?>
        </div>

        <div class="btn-row" style="grid-column: 1 / -1;">
            <button type="submit">Update password</button>
        </div>
        <?php if (ValidationHelper::first($passwordErrors, '_global')): ?>
            <div class="field-error" style="grid-column: 1 / -1;"><?= ViewHelper::escape((string) ValidationHelper::first($passwordErrors, '_global')); ?></div>
        <?php endif; ?>
    </form>
</div>

<?php require __DIR__ . '/../partials/region_branch_script.php'; ?>
