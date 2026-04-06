<?php

use App\Helpers\ResponseHelper;
use App\Helpers\SecurityHelper;
use App\Helpers\ValidationHelper;
use App\Helpers\ViewHelper;
?>
<div class="page-head">
    <div>
        <h1>Login</h1>
        <p>Use your account credentials to access the internal dashboard.</p>
    </div>
</div>

<div class="panel stack-sm" style="max-width: 460px;">
    <form method="POST" action="<?= ViewHelper::escape(ResponseHelper::url('login')); ?>">
        <input type="hidden" name="_token" value="<?= ViewHelper::escape(SecurityHelper::csrfToken()); ?>">

        <label for="username">Username</label>
        <input id="username" name="username" type="text" value="<?= ViewHelper::escape($old['username'] ?? ''); ?>" class="<?= ViewHelper::escape(ValidationHelper::inputClass($errors, 'username')); ?>" required>
        <?php if (ValidationHelper::first($errors, 'username')): ?>
            <div class="field-error"><?= ViewHelper::escape((string) ValidationHelper::first($errors, 'username')); ?></div>
        <?php endif; ?>

        <label for="password">Password</label>
        <input id="password" name="password" type="password" class="<?= ViewHelper::escape(ValidationHelper::inputClass($errors, 'password')); ?>" required>
        <?php if (ValidationHelper::first($errors, 'password')): ?>
            <div class="field-error"><?= ViewHelper::escape((string) ValidationHelper::first($errors, 'password')); ?></div>
        <?php endif; ?>

        <?php if (ValidationHelper::first($errors, '_global')): ?>
            <div class="field-error"><?= ViewHelper::escape((string) ValidationHelper::first($errors, '_global')); ?></div>
        <?php endif; ?>

        <button type="submit">Login</button>
    </form>

    <div class="muted">Need access first time?</div>
    <a href="<?= ViewHelper::escape(ResponseHelper::url('register')); ?>">Create an account</a>
</div>
