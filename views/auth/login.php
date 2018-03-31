<?php

/** @var yii\web\View $this */
/** @var yii\base\DynamicModel $model */
/** @var string $returnUrl */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Login';

?>
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading"><?= Html::encode($this->title) ?></div>
                <div class="panel-body">

                    <?php if (Yii::$app->user->getReturnUrl() != Yii::$app->getHomeUrl()): ?>
                        <p>After logging in, you will be redirected to <strong><?= Yii::$app->user->getReturnUrl() ?></strong></p>
                    <?php endif; ?>

                    <?= Html::beginForm('', 'post', ['id' => 'login-form', 'class' => 'form-horizontal']) ?>

                    <?php $field = 'email'; ?>
                    <div class="form-group <?= $model->hasErrors($field) ? 'has-error' : '' ?>">
                        <?= Html::activeLabel($model, $field, ['class' => 'col-md-4 control-label']) ?>
                        <div class="col-md-6">
                            <?= Html::activeTextInput($model, $field, [
                                'class' => 'form-control',
                                'required' => true,
                            ]); ?>
                            <span class="help-block">
                                <strong><?= Html::error($model, $field) ?></strong>
                            </span>
                        </div>
                    </div>

                    <?php $field = 'password'; ?>
                    <div class="form-group <?= $model->hasErrors($field) ? 'has-error' : '' ?>">
                        <?= Html::activeLabel($model, $field, ['class' => 'col-md-4 control-label']) ?>
                        <div class="col-md-6">
                            <?= Html::activePasswordInput($model, $field, [
                                'class' => 'form-control',
                                'required' => true,
                            ]); ?>
                            <span class="help-block">
                                <strong><?= Html::error($model, $field) ?></strong>
                            </span>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6 col-md-offset-4">
                            <div class="checkbox"><?= Html::activeCheckbox($model, 'rememberMe') ?></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-8 col-md-offset-4">
                            <button type="submit" class="btn btn-primary" name="login-button">Login</button>
                            <a class="btn btn-link" href="<?= Url::to('/auth/forgot') ?>">Forgot Your Password?</a>
                        </div>
                    </div>

                    <?= Html::endForm() ?>

                </div>
            </div>
        </div>
    </div>
</div>