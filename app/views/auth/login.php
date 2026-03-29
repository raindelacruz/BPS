<?php

use App\Helpers\ResponseHelper;
use App\Helpers\SecurityHelper;
use App\Helpers\ViewHelper;
?>
<div class="page-head">
    <div>
        <h1>Login</h1>
        <p>Use your account credentials to access the internal dashboard.</p>
    </div>
</div>

<?php if (!empty($errors)): ?>
    <div class="flash">
        <?php foreach ($errors as $error): ?>
            <div><?= ViewHelper::escape($error); ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="panel stack-sm" style="max-width: 460px;">
    <form method="POST" action="<?= ViewHelper::escape(ResponseHelper::url('login')); ?>">
        <input type="hidden" name="_token" value="<?= ViewHelper::escape(SecurityHelper::csrfToken()); ?>">

        <label for="username">Username</label>
        <input id="username" name="username" type="text" value="<?= ViewHelper::escape($old['username'] ?? ''); ?>" required>

        <label for="password">Password</label>
        <input id="password" name="password" type="password" required>

        <button type="submit">Login</button>
    </form>

    <div class="muted">Need access first time?</div>
    <a href="<?= ViewHelper::escape(ResponseHelper::url('register')); ?>">Create an account</a>
</div>
