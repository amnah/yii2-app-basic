<?php

use app\tests\fixtures\PasswordResetFixture;
use app\tests\fixtures\UserFixture;

class AccountCest
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

    public function _before(\FunctionalTester $I)
    {
        $user = $I->grabFixture('users', 'neo');
        $I->amLoggedInAs($user);
        $I->amOnRoute('user');
    }

    public function openPage(\FunctionalTester $I)
    {
        $I->see('account');
        $I->see('username');
        $I->see('update');
    }

    public function checkEmptyUsername(\FunctionalTester $I)
    {
        $I->submitForm('#account-form', [
            'User[username]' => '',
        ]);
        $I->expectTo('see validations errors');
        $I->see('cannot be blank');
    }

    public function checkExistingUsername(\FunctionalTester $I)
    {
        $user = $I->grabFixture('users', 'neo2');
        $I->submitForm('#account-form', [
            'User[username]' => $user->username,
        ]);
        $I->expectTo('see validations errors');
        $I->see('has already been taken');
    }

    public function checkOwnUsername(\FunctionalTester $I)
    {
        $user = $I->grabFixture('users', 'neo');
        $I->submitForm('#account-form', [
            'User[username]' => $user->username,
        ]);
        $I->see('account updated');
    }

    public function checkNewUsername(\FunctionalTester $I)
    {
        $I->submitForm('#account-form', [
            'username' => 'neo4',
        ]);
        $I->see('account updated');
    }
}