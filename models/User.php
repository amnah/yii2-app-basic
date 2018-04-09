<?php

namespace app\models;

use Yii;
use yii\web\IdentityInterface;
use app\components\BaseModel;

/**
 * This is the model class for table "{{%user}}".
 *
 * @property int $id
 * @property string $email
 * @property string $username
 * @property string $password
 * @property string $confirmation
 * @property string $auth_key
 * @property string $created_at
 * @property string $updated_at
 *
 * @property PasswordReset[] $passwordResets
 */
class User extends BaseModel implements IdentityInterface
{
    const SCENARIO_REGISTER = 'register';
    const SCENARIO_RESET = 'reset';
    const SCENARIO_ACCOUNT = 'account';

    /**
     * @var string
     */
    public $confirm_password;

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        return [
            static::SCENARIO_REGISTER => ['email', 'username', 'password', 'confirm_password'],
            static::SCENARIO_RESET => ['password', 'confirm_password'],
            static::SCENARIO_ACCOUNT=> ['username'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['email', 'username', 'password'], 'string', 'max' => 255],
            [['email', 'username', 'password'], 'required'],
            [['email', 'username'], 'trim'],
            [['email'], 'email'],
            [['email'], 'unique'],
            [['username'], 'unique'],
            [['username'], 'string', 'min' => 2],
            [['username'], 'match', 'pattern' => '/^[A-Za-z0-9_]+$/', 'message' => Yii::t('app', '{attribute} can contain only letters, numbers, and "_"')],
            [['password'], 'string', 'min' => 3],
            [['password'], 'compare', 'compareAttribute' => 'confirm_password'],
            [['confirm_password'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'email' => Yii::t('app', 'Email'),
            'username' => Yii::t('app', 'Username'),
            'password' => Yii::t('app', 'Password'),
            'confirmation' => Yii::t('app', 'Confirmation'),
            'auth_key' => Yii::t('app', 'Auth Key'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function fields()
    {
        return ["id", "email", "username"];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPasswordResets()
    {
        return $this->hasMany(PasswordReset::class, ['user_id' => 'id'])->inverseOf('user');
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
        //return static::findOne(["access_token" => $token]);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey;
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        if ($this->getIsNewRecord()) {
            $this->auth_key = Yii::$app->getSecurity()->generateRandomString();
        }
        if ($this->getDirtyAttributes(['password']) && $this->password) {
            // set lower cost for test environment
            $cost = YII_ENV_TEST ? 6 : null;
            $this->password = Yii::$app->getSecurity()->generatePasswordHash($this->password, $cost);
        }
        return true;
    }

    /**
     * Clear password (for form in password reset page)
     * @return static
     */
    public function clearPassword()
    {
        $this->password = '';
        return $this;
    }

    /**
     * Validate password
     * @param string $password
     * @return bool
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }

    /**
     * Set confirmation token
     * @return static
     */
    public function setConfirmationToken()
    {
        $this->confirmation = Yii::$app->getSecurity()->generateRandomString();
        $this->save(false);
        return $this;
    }

    /**
     * Clear confirmation token
     * @return static
     */
    public function clearConfirmationToken()
    {
        $this->confirmation = null;
        $this->save(false);
        return $this;
    }
}
