<?php

use App\Helpers\ResponseHelper;
use App\Helpers\SecurityHelper;
use App\Helpers\ViewHelper;
?>
<h1>Verify Account</h1>
<p>Enter the verification code sent to your government email address.</p>

<?php if (!empty($errors)): ?>
    <div class="flash">
        <?php foreach ($errors as $error): ?>
            <div><?= ViewHelper::escape($error); ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form method="POST" action="<?= ViewHelper::escape(ResponseHelper::url('verify')); ?>">
    <input type="hidden" name="_token" value="<?= ViewHelper::escape(SecurityHelper::csrfToken()); ?>">

    <label for="email">Email</label>
    <input id="email" name="email" type="email" value="<?= ViewHelper::escape($old['email'] ?? ''); ?>" required>

    <label for="code">Verification code</label>
    <input id="code" name="code" type="text" inputmode="numeric" maxlength="6" value="<?= ViewHelper::escape($old['code'] ?? ''); ?>" required>

    <button type="submit">Verify</button>
</form>

<p><a href="<?= ViewHelper::escape(ResponseHelper::url('login')); ?>">Back to login</a></p>
