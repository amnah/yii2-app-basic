<?php

namespace app\models;

use Yii;
use yii\db\Expression;
use app\components\BaseModel;

/**
 * This is the model class for table "{{%password_reset}}".
 *
 * @property int $id
 * @property int $user_id
 * @property string $token
 * @property string $created_at
 * @property string $consumed_at
 *
 * @property User $user
 */
class PasswordReset extends BaseModel
{
    /**
     * @var int Number of minutes before token expires
     */
    const EXPIRE_MINUTES = 60;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['timestamp']['updatedAtAttribute'] = false;
        return $behaviors;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'token' => Yii::t('app', 'Token'),
            'created_at' => Yii::t('app', 'Created At'),
            'consumed_at' => Yii::t('app', 'Consumed At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id'])->inverseOf('passwordResets');
    }

    /**
     * Get model by token
     * @param string $token
     * @return static
     */
    public static function getByToken($token)
    {
        // check for token that hasn't been consumed and hasn't expired yet
        /** @var static $model */
        $expireMinutes = static::EXPIRE_MINUTES;
        $model = static::find()
            ->where(['token' => $token])
            ->andWhere(['consumed_at' => null])
            ->andWhere(new Expression("created_at > DATE_SUB(now(), INTERVAL $expireMinutes MINUTE)"))
            ->one();
        return $model;
    }

    /**
     * Update or create token for user
     * @param int $userId
     * @return static
     * @throws \yii\base\Exception
     */
    public static function setTokenForUser($userId)
    {
        return static::updateOrCreate([
            'user_id' => $userId,
            'consumed_at' => null,
        ], [
            'token' => Yii::$app->security->generateRandomString(),
            'created_at' => static::getTimestampValue(),
        ]);
    }

    /**
     * Consume password reset
     */
    public function consume()
    {
        $this->consumed_at = $this->getTimestampValue();
        $this->save(false);
        return $this;
    }
}
