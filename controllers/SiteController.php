<?php

namespace app\controllers;

use Yii;
use yii\base\DynamicModel;
use yii\web\Controller;
use yii\web\Response;
use app\components\EmailManager;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $attributes = ['name' => '', 'email' => '', 'subject' => '', 'body' => '', 'verificationCode' => ''];
        $model = new DynamicModel($attributes);
        $model->addRule(['name', 'email', 'subject', 'body'], 'required')
            ->addRule(['email'], 'email')
            ->addRule(['verificationCode'], 'captcha', ['captchaAction' => $this->getUniqueId() . '/captcha']);

        $success = false;
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            /** @var EmailManager $emailManager */
            $emailManager = Yii::$app->emailManager;
            $emailManager->sendContactEmail($model);
            $success = true;
        }
        return $this->render('contact', [
            'success' => $success,
            'model' => $model,
        ]);
    }
}
