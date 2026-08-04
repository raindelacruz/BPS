<?php

use App\Helpers\ResponseHelper;
use App\Helpers\SecurityHelper;
use App\Helpers\ValidationHelper;
use App\Helpers\ViewHelper;
?>
<div class="page-head">
    <div>
        <h1>Reset Password</h1>
        <p>Choose a new password for your eBPS account.</p>
    </div>
</div>

<div class="panel stack-sm" style="max-width: 460px;">
    <form method="POST" action="<?= ViewHelper::escape(ResponseHelper::url('password/reset')); ?>" data-confirmation="off">
        <input type="hidden" name="_token" value="<?= ViewHelper::escape(SecurityHelper::csrfToken()); ?>">
        <input type="hidden" name="token" value="<?= ViewHelper::escape($old['token'] ?? ''); ?>">

        <label for="password">New password</label>
        <input id="password" name="password" type="password" class="<?= ViewHelper::escape(ValidationHelper::inputClass($errors, 'password')); ?>" required>
        <?php if (ValidationHelper::first($errors, 'password')): ?>
            <div class="field-error"><?= ViewHelper::escape((string) ValidationHelper::first($errors, 'password')); ?></div>
        <?php endif; ?>

        <label for="password_confirmation">Confirm new password</label>
        <input id="password_confirmation" name="password_confirmation" type="password" class="<?= ViewHelper::escape(ValidationHelper::inputClass($errors, 'password_confirmation')); ?>" required>
        <?php if (ValidationHelper::first($errors, 'password_confirmation')): ?>
            <div class="field-error"><?= ViewHelper::escape((string) ValidationHelper::first($errors, 'password_confirmation')); ?></div>
        <?php endif; ?>

        <?php if (ValidationHelper::first($errors, '_global')): ?>
            <div class="field-error"><?= ViewHelper::escape((string) ValidationHelper::first($errors, '_global')); ?></div>
        <?php endif; ?>

        <div class="btn-row">
            <button type="submit">Reset password</button>
            <a class="btn-link" href="<?= ViewHelper::escape(ResponseHelper::url('login')); ?>">Back to login</a>
        </div>
    </form>
</div>
