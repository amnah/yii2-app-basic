<?php

/** @var yii\web\View $this */
/** @var yii\base\DynamicModel $model */
/** @var bool $success */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Forgot Password';

?>
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading"><?= Html::encode($this->title) ?></div>
                <div class="panel-body">

                    <?php if ($success): ?>

                        <div class="alert alert-success">
                            We have e-mailed your password reset link to  <strong><?= $model->email ?></strong>
                        </div>

                    <?php else: ?>

                        <?= Html::beginForm('', 'post', ['class' => 'form-horizontal']) ?>

                        <?php $field = 'email'; ?>
                        <div class="form-group <?= $model->hasErrors($field) ? 'has-error' : '' ?>">
                            <?= Html::activeLabel($model, $field, ['class' => 'col-md-4 control-label']) ?>
                            <div class="col-md-6">
                                <?= Html::activeTextInput($model, $field, [
                                    'class' => 'form-control',
                                    'required' => true,
                                    'type' => 'email',
                                ]); ?>
                                <span class="help-block">
                                    <strong><?= Html::error($model, $field) ?></strong>
                                </span>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-8 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">Send Password Reset Link</button>
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