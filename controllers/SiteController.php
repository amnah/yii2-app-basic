<?php

namespace app\controllers;

use Yii;
use yii\base\DynamicModel;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\components\EmailManager;
use app\models\User;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

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
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $attributes = ['username' => '', 'password' => '', 'rememberMe' => true];
        $model = new DynamicModel($attributes);
        $model->addRule(['username', 'password'], 'required')
            ->addRule(['rememberMe'], 'boolean');

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $user = User::findByUsername(trim($model->username));
            if (!$user || !$user->validatePassword($model->password)) {
                $model->addError('username', 'Incorrect username or password.');
            } else {
                Yii::$app->user->login($user, $model->rememberMe ? 3600*24*30 : 0);
                return $this->goBack();
            }
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
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

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            /** @var EmailManager $emailManager */
            $emailManager = Yii::$app->emailManager;
            $emailManager->sendContactEmail($model);
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
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
}
