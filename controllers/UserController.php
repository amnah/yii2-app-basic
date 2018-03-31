<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use app\models\User;

class UserController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Account
     */
    public function actionIndex()
    {
        /** @var User $user */
        $success = false;
        $user = Yii::$app->user->identity;
        $user->setScenario(User::SCENARIO_ACCOUNT);
        if ($user->loadPostAndSave()) {
            $success = true;
        }

        return $this->render('index', [
            'success' => $success,
            'user' => $user,
        ]);
    }
}
