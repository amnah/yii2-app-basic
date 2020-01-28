<?php

/** @var $this \yii\web\View */
/** @var $content string */

use yii\helpers\Html;
?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $this->title ?></title>

    <link rel="stylesheet" href="/css/site.css">
</head>
<body>
<?php $this->beginBody() ?>

<div class="topnav">
    <a class="active" href="/">Home</a>
    <a href="/site/about">About</a>
    <a href="/site/contact">Contact</a>
    <?php if (Yii::$app->user->isGuest): ?>
        <a href="/auth/login">Login</a>
        <a href="/auth/register">Register</a>
    <?php else: ?>
        <a href="/user">Account</a>
        <a>
            <?= Html::beginForm(['/auth/logout'], 'post') ?>
            <button type="submit" class="btn btn-link logout">Logout (<?= Yii::$app->user->identity->email ?>)</button>
            <?= Html::endForm() ?>
        </a>
    <?php endif; ?>
</div>

<div id="main-content">
    <?= $content ?>
</div>

<?php $this->endBody() ?>
</body>
</html>
