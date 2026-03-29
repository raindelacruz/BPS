<?php

use App\Helpers\ResponseHelper;
use App\Helpers\SecurityHelper;
use App\Helpers\ViewHelper;
?>
<div class="page-head">
    <div>
        <h1>My Account</h1>
        <p>View your account details and update your password.</p>
    </div>
</div>

<?php if (!empty($errors)): ?>
    <div class="flash">
        <?php foreach ($errors as $error): ?>
            <div><?= ViewHelper::escape($error); ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<dl class="detail-grid">
    <div>
        <dt>Username</dt>
        <dd><?= ViewHelper::escape($user['username']); ?></dd>
    </div>
    <div>
        <dt>Name</dt>
        <dd><?= ViewHelper::escape(trim($user['firstname'] . ' ' . $user['lastname'])); ?></dd>
    </div>
    <div>
        <dt>Email</dt>
        <dd><?= ViewHelper::escape($user['email']); ?></dd>
    </div>
    <div>
        <dt>Region</dt>
        <dd><?= ViewHelper::escape($user['region']); ?></dd>
    </div>
    <div>
        <dt>Branch</dt>
        <dd><?= ViewHelper::escape($user['branch'] ?? 'Not assigned'); ?></dd>
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
            <h2>Change Password</h2>
            <p>Keep your account secure by updating your password when needed.</p>
        </div>
    </div>
    <form method="POST" action="<?= ViewHelper::escape(ResponseHelper::url('profile/password')); ?>" class="form-grid two-col">
        <input type="hidden" name="_token" value="<?= ViewHelper::escape(SecurityHelper::csrfToken()); ?>">

        <div>
            <label for="current_password">Current password</label>
            <input id="current_password" name="current_password" type="password" required>
        </div>

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
