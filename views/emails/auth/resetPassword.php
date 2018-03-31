<?php

/** @var yii\web\View $this */
/** @var app\models\PasswordReset $passwordReset */
/** @var string $resetUrl */

?>
<p>Hello <?= $passwordReset->user->email ?>.</p>
<p>Click here to reset your password:</p>

<p><a href="<?= $resetUrl ?>">Reset password</a></p>
<p><?= $resetUrl ?></p>