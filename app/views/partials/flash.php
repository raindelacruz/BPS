<?php

use App\Helpers\SessionHelper;
use App\Helpers\ViewHelper;

$success = SessionHelper::pullFlash('success');
$error = SessionHelper::pullFlash('error');

if ($success): ?>
    <div class="flash"><?= ViewHelper::escape($success); ?></div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="flash"><?= ViewHelper::escape($error); ?></div>
<?php endif; ?>
