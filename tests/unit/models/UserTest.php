<?php

namespace tests\models;

use app\models\User;

class UserTest extends \Codeception\Test\Unit
{
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
    public function testValidateUser($user)
    {
        $user = User::findOne(['email' => 'neo@neo.com']);

        expect_that($user->validatePassword('neo'));
        expect_not($user->validatePassword('123456'));
    }

}
