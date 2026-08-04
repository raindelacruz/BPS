<?php

use App\Helpers\ResponseHelper;
use App\Helpers\SecurityHelper;
use App\Helpers\ValidationHelper;
use App\Helpers\ViewHelper;
?>
<div class="page-head">
    <div>
        <h1>Forgot Password</h1>
        <p>Enter your account email to receive a password reset link.</p>
    </div>
</div>

<div class="panel stack-sm" style="max-width: 460px;">
    <form method="POST" action="<?= ViewHelper::escape(ResponseHelper::url('password/forgot')); ?>" data-confirmation="off">
        <input type="hidden" name="_token" value="<?= ViewHelper::escape(SecurityHelper::csrfToken()); ?>">

        <label for="email">Email address</label>
        <input id="email" name="email" type="email" value="<?= ViewHelper::escape($old['email'] ?? ''); ?>" class="<?= ViewHelper::escape(ValidationHelper::inputClass($errors, 'email')); ?>" required>
        <?php if (ValidationHelper::first($errors, 'email')): ?>
            <div class="field-error"><?= ViewHelper::escape((string) ValidationHelper::first($errors, 'email')); ?></div>
        <?php endif; ?>

        <?php if (ValidationHelper::first($errors, '_global')): ?>
            <div class="field-error"><?= ViewHelper::escape((string) ValidationHelper::first($errors, '_global')); ?></div>
        <?php endif; ?>

        <div class="btn-row">
            <button type="submit">Send reset link</button>
            <a class="btn-link" href="<?= ViewHelper::escape(ResponseHelper::url('login')); ?>">Back to login</a>
        </div>
    </form>
</div>
