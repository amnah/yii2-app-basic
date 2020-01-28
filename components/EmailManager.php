<?php

namespace app\components;

use Yii;
use yii\base\BaseObject;
use yii\base\DynamicModel;
use yii\helpers\Url;
use yii\mail\BaseMailer;
use app\models\PasswordReset;
use app\models\User;

class EmailManager extends BaseObject
{
    /**
     *
     * @var BaseMailer The mailer component
     */
    public $mailer;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        $this->mailer = Yii::$app->mailer;
    }

    /**
     * Send contact email
     * @param DynamicModel $contactForm
     * @return bool
     */
    public function sendContactEmail($contactForm) {
        return $this->mailer->compose()
            ->setTo(Yii::$app->params['adminEmail'])
            ->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->params['senderName']])
            ->setReplyTo([$contactForm->email => $contactForm->name])
            ->setSubject($contactForm->subject)
            ->setTextBody($contactForm->body)
            ->send();
    }

    /**
     * Send confirmation email
     * @param User $user
     * @return bool
     */
    public function sendConfirmationEmail($user)
    {
        $confirmUrl = Url::to(['/auth/confirm', 'email' => $user->email, 'confirmation' => $user->confirmation], true);
        return $this->mailer->compose('auth/confirmEmail', compact('user', 'confirmUrl'))
            ->setTo($user->email)
            ->setSubject(Yii::t('app', 'Confirm Email'))
            ->send();
    }

    /**
     * Send reset email
     * @param PasswordReset $passwordReset
     * @return bool
     */
    public function sendResetEmail($passwordReset)
    {
        $resetUrl = Url::to(['auth/reset', 'token' => $passwordReset->token], true);
        return $this->mailer->compose('auth/resetPassword', compact('passwordReset', 'resetUrl'))
            ->setTo($passwordReset->user->email)
            ->setSubject(Yii::t('app', 'Reset Password'))
            ->send();
    }
}