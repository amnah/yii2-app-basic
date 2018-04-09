<?php

use app\tests\fixtures\UserFixture;

class RegisterConfirmCest
{
    public function _fixtures()
    {
        return [
            'users' => [
                'class' => UserFixture::class,
            ],
        ];
    }

    public function _before(\FunctionalTester $I)
    {
        $I->amOnRoute('auth/register');
    }

    public function openPage(\FunctionalTester $I)
    {
        $I->see('Register');
    }

    public function registerWithEmptyCredentials(\FunctionalTester $I)
    {
        $I->submitForm('#register-form', []);
        $I->expectTo('see validations errors');
        $I->see('Email cannot be blank.');
        $I->see('Username cannot be blank.');
        $I->see('Password cannot be blank.');
    }

    public function registerWithBadCredentials(\FunctionalTester $I)
    {
        $I->submitForm('#register-form', [
            'User[email]' => 'notAnEmail',
            'User[username]' => 'z', // too short
            'User[password]' => 'ne',
            'User[confirm_password]' => '',
        ]);
        $I->expectTo('see validations errors');
        $I->see('not a valid email address');
        $I->see('username should contain at least 2 characters');
        $I->see('Password should contain at least 3 characters');
    }

    public function registerWithExistingCredentialsAndDiffPasswords(\FunctionalTester $I)
    {
        $I->submitForm('#register-form', [
            'User[email]' => 'neo@neo.com',
            'User[username]' => 'neo',
            'User[password]' => 'neo',
            'User[confirm_password]' => '',
        ]);
        $I->expectTo('see validations errors');
        $I->see('"neo@neo.com" has already been taken');
        $I->see('"neo" has already been taken');
        $I->see('password must be equal to');
    }

    public function registerSuccessfully(\FunctionalTester $I)
    {
        $I->submitForm('#register-form', [
            'User[email]' => 'neo3@neo.com',
            'User[username]' => 'neo3',
            'User[password]' => 'neo',
            'User[confirm_password]' => 'neo',
        ]);
        $I->seeEmailIsSent();
        $I->see('please check your email');
    }

    public function checkConfirmation(\FunctionalTester $I)
    {
        $I->amOnRoute('auth/confirm', [
            'email' => 'neo2@neo.com',
            'confirmation' => 'invalid_token',
        ]);
        $I->see('invalid token');

        $user = $I->grabFixture('users', 'neo2');
        $I->amOnRoute('auth/confirm', [
            'email' => $user->email,
            'confirmation' => $user->confirmation,
        ]);
        $I->see('email confirmed');
    }
}