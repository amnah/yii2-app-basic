<?php

namespace app\tests\unit\models;

use app\models\User;
use app\tests\fixtures\UserFixture;

class UserTest extends \Codeception\Test\Unit
{
    public function _fixtures()
    {
        return [
            'users' => [
                'class' => UserFixture::class,
            ],
        ];
    }

    public function testFindUserById()
    {
        expect_that($user = User::findIdentity(1));
        expect($user->username)->equals('neo');
        expect($user->email)->equals('neo@neo.com');

        expect_not(User::findIdentity(999));
    }

    public function testFindUserByAccessToken()
    {
        expect_not(User::findIdentityByAccessToken('non-existing'));
    }

    /**
     * @depends testFindUserById
     */
    public function testValidateUser()
    {
        $user = $this->tester->grabFixture('users', 'neo');
        $user = User::findOne(['email' => $user->email]);

        expect_that($user->validatePassword('neo'));
        expect_not($user->validatePassword('123456'));
    }
}
