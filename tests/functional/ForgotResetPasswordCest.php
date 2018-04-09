<?php

use app\tests\fixtures\PasswordResetFixture;
use app\tests\fixtures\UserFixture;

class ForgotResetPasswordCest
{
    public function _fixtures()
    {
        return [
            'users' => [
                'class' => UserFixture::class,
            ],
            'passwordResets' => [
                'class' => PasswordResetFixture::class,
            ],
        ];
    }

    // --------------------------------------------------
    // Forgot page
    // --------------------------------------------------
    public function openForgotPage(\FunctionalTester $I)
    {
        $I->amOnRoute('auth/forgot');
        $I->see('Forgot password');
    }

    public function checkForgotEmpty(\FunctionalTester $I)
    {
        $I->amOnRoute('auth/forgot');
        $I->submitForm('#forgot-form', []);
        $I->expectTo('see validations errors');
        $I->see('Email cannot be blank');
    }

    public function checkForgotNonEmail(\FunctionalTester $I)
    {
        $I->amOnRoute('auth/forgot');
        $I->submitForm('#forgot-form', [
            'DynamicModel[email]' => 'not-an-email'
        ]);
        $I->expectTo('see validations errors');
        $I->see('not a valid email');
    }

    public function checkForgotNonExistingAccount(\FunctionalTester $I)
    {
        $I->amOnRoute('auth/forgot');
        $I->submitForm('#forgot-form', [
            'DynamicModel[email]' => 'fake@test.com'
        ]);
        $I->expectTo('see validations errors');
        $I->see('email address not found');
    }

    public function checkForgotSuccessful(\FunctionalTester $I)
    {
        $I->amOnRoute('auth/forgot');
        $I->submitForm('#forgot-form', [
            'DynamicModel[email]' => 'neo@neo.com',
        ]);

        $I->seeEmailIsSent();
        $I->see('We have e-mailed your password reset link');
    }

    // --------------------------------------------------
    // Reset page
    // --------------------------------------------------
    public function checkResetInvalidToken(\FunctionalTester $I)
    {
        $I->amOnRoute('auth/reset', [
            'token' => 'invalid_token'
        ]);
        $I->see('invalid token');
    }

    public function checkResetExpiredOrConsumedToken(\FunctionalTester $I)
    {
        $passwordReset = $I->grabFixture('passwordResets', 'expired');
        $I->amOnRoute('auth/reset', [
            'token' => $passwordReset->token,
        ]);
        $I->see('invalid token');

        $passwordReset = $I->grabFixture('passwordResets', 'consumed');
        $I->amOnRoute('auth/reset', [
            'token' => $passwordReset->token,
        ]);
        $I->see('invalid token');
    }

    public function checkResetValidToken(\FunctionalTester $I)
    {
        $passwordReset = $I->grabFixture('passwordResets', 'valid');

        $I->amOnRoute('auth/reset', [
            'token' => $passwordReset->token,
        ]);

        $I->see('password');
        $I->see('confirm password');
    }

    public function checkResetValidTokenBadPassword(\FunctionalTester $I)
    {
        $passwordReset = $I->grabFixture('passwordResets', 'valid');

        // check empty password
        $I->amOnRoute('auth/reset', [
            'token' => $passwordReset->token,
        ]);
        $I->submitForm('#reset-form', [
            'User[password]' => '',
        ]);
        $I->expectTo('see validations errors');
        $I->see('cannot be blank');

        // check short password
        $I->amOnRoute('auth/reset', [
            'token' => $passwordReset->token,
        ]);
        $I->submitForm('#reset-form', [
            'User[password]' => 'zz',
        ]);
        $I->expectTo('see validations errors');
        $I->see('Password should contain at least 3 characters');

        // check different password
        $I->amOnRoute('auth/reset', [
            'token' => $passwordReset->token,
        ]);
        $I->submitForm('#reset-form', [
            'User[password]' => 'neo',
            'User[confirm_password]' => '',
        ]);
        $I->expectTo('see validations errors');
        $I->see('Password must be equal to');
    }

    public function checkResetSuccessfully(\FunctionalTester $I)
    {
        $passwordReset = $I->grabFixture('passwordResets', 'valid');

        // check empty password
        $I->amOnRoute('auth/reset', [
            'token' => $passwordReset->token,
        ]);
        $I->submitForm('#reset-form', [
            'User[password]' => 'neo',
            'User[confirm_password]' => 'neo',
        ]);
        $I->see('password has been reset');
    }
}