<?php

use App\Helpers\ResponseHelper;
use App\Helpers\SecurityHelper;
use App\Helpers\ValidationHelper;
use App\Helpers\ViewHelper;
?>
<h1>Verify Account</h1>
<p>Enter the verification code sent to your government email address.</p>

<form method="POST" action="<?= ViewHelper::escape(ResponseHelper::url('verify')); ?>">
    <input type="hidden" name="_token" value="<?= ViewHelper::escape(SecurityHelper::csrfToken()); ?>">

    <label for="email">Email</label>
    <input id="email" name="email" type="email" value="<?= ViewHelper::escape($old['email'] ?? ''); ?>" class="<?= ViewHelper::escape(ValidationHelper::inputClass($errors, 'email')); ?>" required>
    <?php if (ValidationHelper::first($errors, 'email')): ?>
        <div class="field-error"><?= ViewHelper::escape((string) ValidationHelper::first($errors, 'email')); ?></div>
    <?php endif; ?>

    <label for="code">Verification code</label>
    <input id="code" name="code" type="text" inputmode="numeric" maxlength="6" value="<?= ViewHelper::escape($old['code'] ?? ''); ?>" class="<?= ViewHelper::escape(ValidationHelper::inputClass($errors, 'code')); ?>" required>
    <?php if (ValidationHelper::first($errors, 'code')): ?>
        <div class="field-error"><?= ViewHelper::escape((string) ValidationHelper::first($errors, 'code')); ?></div>
    <?php endif; ?>

    <?php if (ValidationHelper::first($errors, '_global')): ?>
        <div class="field-error"><?= ViewHelper::escape((string) ValidationHelper::first($errors, '_global')); ?></div>
    <?php endif; ?>

    <button type="submit">Verify</button>
</form>

<p><a href="<?= ViewHelper::escape(ResponseHelper::url('login')); ?>">Back to login</a></p>
