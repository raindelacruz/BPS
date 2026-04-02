<?php

use App\Helpers\ResponseHelper;
use App\Helpers\SecurityHelper;
use App\Helpers\ViewHelper;
?>
<div class="page-head">
    <div>
        <h1>My Account</h1>
        <p>Update your account details and password.</p>
    </div>
</div>

<?php if (!empty($errors)): ?>
    <div class="flash">
        <?php foreach ($errors as $error): ?>
            <div><?= ViewHelper::escape($error); ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if (!empty($profileErrors)): ?>
    <div class="flash">
        <?php foreach ($profileErrors as $error): ?>
            <div><?= ViewHelper::escape($error); ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

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
            <input id="email" name="email" type="email" value="<?= ViewHelper::escape($user['email']); ?>" required>
        </div>

        <div></div>

        <?php
        $regionFieldId = 'region';
        $branchFieldId = 'branch';
        $selectedRegion = $user['region'] ?? '';
        $selectedBranch = $user['branch'] ?? '';
        require __DIR__ . '/../partials/region_branch_fields.php';
        ?>

        <div class="btn-row" style="grid-column: 1 / -1;">
            <button type="submit">Update profile</button>
        </div>
    </form>
</div>

<?php if (!empty($passwordErrors)): ?>
    <div class="flash">
        <?php foreach ($passwordErrors as $error): ?>
            <div><?= ViewHelper::escape($error); ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

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
            <input id="password" name="password" type="password" required>
        </div>

        <div>
            <label for="password_confirmation">Confirm new password</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required>
        </div>

        <div class="btn-row" style="grid-column: 1 / -1;">
            <button type="submit">Update password</button>
        </div>
    </form>
</div>

<?php require __DIR__ . '/../partials/region_branch_script.php'; ?>
