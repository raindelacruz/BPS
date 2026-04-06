<?php

use App\Helpers\ResponseHelper;
use App\Helpers\SecurityHelper;
use App\Helpers\ValidationHelper;
use App\Helpers\ViewHelper;
?>
<div class="page-head">
    <div>
        <h1>Register</h1>
        <p>Create an internal eBPS author account using your official government email.</p>
    </div>
</div>

<div class="panel stack-sm">
    <form method="POST" action="<?= ViewHelper::escape(ResponseHelper::url('register')); ?>" class="form-grid two-col">
        <input type="hidden" name="_token" value="<?= ViewHelper::escape(SecurityHelper::csrfToken()); ?>">

        <div>
            <label for="username">Username</label>
            <input id="username" name="username" type="text" value="<?= ViewHelper::escape($old['username'] ?? ''); ?>" class="<?= ViewHelper::escape(ValidationHelper::inputClass($errors, 'username')); ?>" required>
            <?php if (ValidationHelper::first($errors, 'username')): ?>
                <div class="field-error"><?= ViewHelper::escape((string) ValidationHelper::first($errors, 'username')); ?></div>
            <?php endif; ?>
        </div>

        <div>
            <label for="email">Government email</label>
            <input id="email" name="email" type="email" value="<?= ViewHelper::escape($old['email'] ?? ''); ?>" class="<?= ViewHelper::escape(ValidationHelper::inputClass($errors, 'email')); ?>" required>
            <?php if (ValidationHelper::first($errors, 'email')): ?>
                <div class="field-error"><?= ViewHelper::escape((string) ValidationHelper::first($errors, 'email')); ?></div>
            <?php endif; ?>
        </div>

        <div>
            <label for="firstname">First name</label>
            <input id="firstname" name="firstname" type="text" value="<?= ViewHelper::escape($old['firstname'] ?? ''); ?>" class="<?= ViewHelper::escape(ValidationHelper::inputClass($errors, 'firstname')); ?>" required>
            <?php if (ValidationHelper::first($errors, 'firstname')): ?>
                <div class="field-error"><?= ViewHelper::escape((string) ValidationHelper::first($errors, 'firstname')); ?></div>
            <?php endif; ?>
        </div>

        <div>
            <label for="lastname">Last name</label>
            <input id="lastname" name="lastname" type="text" value="<?= ViewHelper::escape($old['lastname'] ?? ''); ?>" class="<?= ViewHelper::escape(ValidationHelper::inputClass($errors, 'lastname')); ?>" required>
            <?php if (ValidationHelper::first($errors, 'lastname')): ?>
                <div class="field-error"><?= ViewHelper::escape((string) ValidationHelper::first($errors, 'lastname')); ?></div>
            <?php endif; ?>
        </div>

        <div>
            <label for="middle_initial">Middle initial</label>
            <input id="middle_initial" name="middle_initial" type="text" maxlength="1" value="<?= ViewHelper::escape($old['middle_initial'] ?? ''); ?>" class="<?= ViewHelper::escape(ValidationHelper::inputClass($errors, 'middle_initial')); ?>">
            <?php if (ValidationHelper::first($errors, 'middle_initial')): ?>
                <div class="field-error"><?= ViewHelper::escape((string) ValidationHelper::first($errors, 'middle_initial')); ?></div>
            <?php endif; ?>
        </div>

        <div>
            <label for="password">Password</label>
            <input id="password" name="password" type="password" class="<?= ViewHelper::escape(ValidationHelper::inputClass($errors, 'password')); ?>" required>
            <?php if (ValidationHelper::first($errors, 'password')): ?>
                <div class="field-error"><?= ViewHelper::escape((string) ValidationHelper::first($errors, 'password')); ?></div>
            <?php endif; ?>
        </div>

        <?php
        $regionFieldId = 'region';
        $branchFieldId = 'branch';
        $selectedRegion = $old['region'] ?? '';
        $selectedBranch = $old['branch'] ?? '';
        require __DIR__ . '/../partials/region_branch_fields.php';
        ?>

        <div>
            <label for="password_confirmation">Confirm password</label>
            <input id="password_confirmation" name="password_confirmation" type="password" class="<?= ViewHelper::escape(ValidationHelper::inputClass($errors, 'password_confirmation')); ?>" required>
            <?php if (ValidationHelper::first($errors, 'password_confirmation')): ?>
                <div class="field-error"><?= ViewHelper::escape((string) ValidationHelper::first($errors, 'password_confirmation')); ?></div>
            <?php endif; ?>
        </div>

        <?php if (ValidationHelper::first($errors, '_global')): ?>
            <div class="field-error" style="grid-column: 1 / -1;"><?= ViewHelper::escape((string) ValidationHelper::first($errors, '_global')); ?></div>
        <?php endif; ?>

        <div class="btn-row" style="grid-column: 1 / -1;">
            <button type="submit">Register</button>
            <a class="btn-link" href="<?= ViewHelper::escape(ResponseHelper::url('login')); ?>">Already have an account?</a>
        </div>
    </form>
</div>

<?php require __DIR__ . '/../partials/region_branch_script.php'; ?>
