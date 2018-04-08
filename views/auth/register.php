<?php

/** @var yii\web\View $this */
/** @var app\models\User $user */
/** @var $success bool */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Register';

?>
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading"><?= Html::encode($this->title) ?></div>
                <div class="panel-body">

                    <?php if ($success): ?>

                        <div class="alert alert-success">
                            <p>User <strong><?= $user->email ?></strong> registered - Please check your email to confirm your address.</p>
                        </div>

                    <?php else: ?>

                        <?= Html::beginForm('', 'post', ['id' => 'register-form', 'class' => 'form-horizontal']) ?>

                        <?php $field = 'email'; ?>
                        <div class="form-group <?= $user->hasErrors($field) ? 'has-error' : '' ?>">
                            <?= Html::activeLabel($user, $field, ['class' => 'col-md-4 control-label']) ?>
                            <div class="col-md-6">
                                <?= Html::activeTextInput($user, $field, [
                                    'class' => 'form-control',
                                    'required' => true,
                                    'type' => 'email',
                                ]); ?>
                                <span class="help-block">
                                    <strong><?= Html::error($user, $field) ?></strong>
                                </span>
                            </div>
                        </div>

                        <?php $field = 'username'; ?>
                        <div class="form-group <?= $user->hasErrors($field) ? 'has-error' : '' ?>">
                            <?= Html::activeLabel($user, $field, ['class' => 'col-md-4 control-label']) ?>
                            <div class="col-md-6">
                                <?= Html::activeTextInput($user, $field, [
                                    'class' => 'form-control',
                                    'required' => true,
                                ]); ?>
                                <span class="help-block">
                                    <strong><?= Html::error($user, $field) ?></strong>
                                </span>
                            </div>
                        </div>

                        <?php $field = 'password'; ?>
                        <div class="form-group <?= $user->hasErrors($field) ? 'has-error' : '' ?>">
                            <?= Html::activeLabel($user, $field, ['class' => 'col-md-4 control-label']) ?>
                            <div class="col-md-6">
                                <?= Html::activePasswordInput($user, $field, [
                                    'class' => 'form-control',
                                    'required' => true,
                                ]); ?>
                                <span class="help-block">
                                    <strong><?= Html::error($user, $field) ?></strong>
                                </span>
                            </div>
                        </div>

                        <?php $field = 'confirm_password'; ?>
                        <div class="form-group <?= $user->hasErrors($field) ? 'has-error' : '' ?>">
                            <?= Html::activeLabel($user, $field, ['class' => 'col-md-4 control-label']) ?>
                            <div class="col-md-6">
                                <?= Html::activePasswordInput($user, $field, [
                                    'class' => 'form-control',
                                    'required' => true,
                                ]); ?>
                                <span class="help-block">
                                    <strong><?= Html::error($user, $field) ?></strong>
                                </span>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-8 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">Register</button>
                                <a class="btn btn-link" href="<?= Url::to('/auth/login') ?>">Login</a>
                            </div>
                        </div>

                        <?= Html::endForm() ?>

                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>