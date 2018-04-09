<?php

use app\models\User;
use app\tests\fixtures\UserFixture;

class LoginCest
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
        $I->amOnRoute('auth/login');
    }

    public function openLoginPage(\FunctionalTester $I)
    {
        $I->see('Login');
    }

    // demonstrates `amLoggedInAs` method
    public function internalLoginById(\FunctionalTester $I)
    {
        $I->amLoggedInAs(1);
        $I->amOnPage('/');
        $I->see('Logout');
    }

    // demonstrates `amLoggedInAs` method
    public function internalLoginByInstance(\FunctionalTester $I)
    {
        $user = $I->grabFixture('users', 'neo');
        $I->amLoggedInAs(User::findOne(['email' => $user->email]));
        $I->amOnPage('/');
        $I->see('Logout');
    }

    public function loginWithEmptyCredentials(\FunctionalTester $I)
    {
        $I->submitForm('#login-form', []);
        $I->expectTo('see validations errors');
        $I->see('Email cannot be blank.');
        $I->see('Password cannot be blank.');
    }

    public function loginWithWrongCredentials(\FunctionalTester $I)
    {
        $I->submitForm('#login-form', [
            'DynamicModel[email]' => 'wrong',
            'DynamicModel[password]' => 'wrong',
        ]);
        $I->expectTo('see validations errors');
        $I->see('Incorrect email or password.');
    }

    public function loginWithUnconfirmedAccount(\FunctionalTester $I)
    {
        $I->submitForm('#login-form', [
            'DynamicModel[email]' => 'neo2',
            'DynamicModel[password]' => 'neo',
        ]);
        $I->expectTo('see validations errors');
        $I->see('address has not been confirmed');
    }

    public function loginSuccessfully(\FunctionalTester $I)
    {
        $I->submitForm('#login-form', [
            'DynamicModel[email]' => 'neo',
            'DynamicModel[password]' => 'neo',
        ]);
        $I->see('Logout');
        $I->dontSeeElement('form#login-form');              
    }
}