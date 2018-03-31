<?php

/** @var yii\web\View $this */
/** @var app\models\User $user */
/** @var string $confirmUrl */

?>
<p>Hello <?= $user->email ?>.</p>
<p>Please confirm your email address.</p>

<p><a href="<?= $confirmUrl ?>">Confirm email address</a></p>
<p><?= $confirmUrl ?></p>