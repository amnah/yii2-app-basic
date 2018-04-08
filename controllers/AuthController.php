<?php

namespace app\controllers;

use Yii;
use yii\base\DynamicModel;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\components\EmailManager;
use app\models\User;
use app\models\PasswordReset;

class AuthController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Perform login
     * @param User $user
     * @param bool $rememberMe
     * @return array
     */
    protected function performLogin($user, $rememberMe = true)
    {
        $duration = $rememberMe ? 2592000 : 0; // 30 days
        Yii::$app->user->login($user, $duration);
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $attributes = ['email' => '', 'password' => '', 'rememberMe' => true];
        $model = new DynamicModel($attributes);
        $model->addRule(['email', 'password'], 'required')
            ->addRule(['rememberMe'], 'boolean');

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $field = filter_var($model->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
            $user = User::findOne([$field => trim($model->email)]);
            if (!$user || !$user->validatePassword($model->password)) {
                $model->addError('email', Yii::t('app', 'Incorrect email or password.'));
            } elseif ($user->confirmation) {
                $model->addError('email', Yii::t('app', 'Email address has not been confirmed - please check your email.'));
            } else {
                $this->performLogin($user, $model->rememberMe);
                return $this->goBack();
            }
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Register
     */
    public function actionRegister()
    {
        $success = false;
        $user = new User;
        $user->setScenario(User::SCENARIO_REGISTER);
        if ($user->loadPostAndValidate()) {
            /** @var EmailManager $emailManager */
            $user->setConfirmationToken();
            $emailManager = Yii::$app->emailManager;
            $emailManager->sendConfirmationEmail($user);
            $success = true;
        }

        return $this->render('register', [
            'success' => $success,
            'user' => $user,
        ]);
    }

    /**
     * Confirm
     */
    public function actionConfirm($email, $confirmation)
    {
        // find and confirm user
        $user = User::findOne(['email' => $email, 'confirmation' => $confirmation]);
        if ($user) {
            $user->clearConfirmationToken();
            $this->performLogin($user, true);
            Yii::$app->session->setFlash('success', Yii::t('app', 'Email confirmed.'));
            return $this->goHome();
        }

        return $this->render('confirm');
    }

    /**
     * Forgot password
     * @throws \yii\base\Exception
     */
    public function actionForgot()
    {
        $defaultAttributes = ['email' => ''];
        $model = new DynamicModel($defaultAttributes);
        $model->addRule(['email'], 'required')
            ->addRule(['email'], 'email');

        // find user and generate $passwordReset token
        $success = false;
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $user = User::findOne(['email' => trim($model->email)]);
            if (!$user) {
                $model->addError('email', Yii::t('app', 'Email address not found.'));
            } else {
                /** @var EmailManager $emailManager */
                $passwordReset = PasswordReset::setTokenForUser($user->id);
                $emailManager = Yii::$app->emailManager;
                $emailManager->sendResetEmail($passwordReset);
                $success = true;
            }
        }

        return $this->render('forgot', [
            'success' => $success,
            'model' => $model,
        ]);
    }

    /**
     * Reset password
     */
    public function actionReset($token)
    {
        $passwordReset = PasswordReset::getByToken($token);
        if (!$passwordReset) {
            return $this->render('reset', [
                'error' => Yii::t('app', 'Invalid token.'),
            ]);
        }

        $user = $passwordReset->user;
        $user->clearPassword()->setScenario(User::SCENARIO_RESET);
        if ($user->loadPostAndSave()) {
            // clear confirmation, consume $passwordReset, and login
            $user->clearConfirmationToken();
            $passwordReset->consume();
            $this->performLogin($user);
            Yii::$app->session->setFlash('success', Yii::t('app', 'Password has been reset.'));
            return $this->goHome();
        }

        return $this->render('reset', [
            'user' => $user,
        ]);
    }

}
