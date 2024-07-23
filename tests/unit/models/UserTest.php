<?php

namespace tests\unit\models;

use app\fixtures\UserFixture;
use app\models\User;

class UserTest extends \Codeception\Test\Unit
{

    /**
     * @var \UnitTester
     */
    public $tester;
    
    public function _fixtures()
    {
        return [
            'user' => [
                'class' => UserFixture::class,
                'dataFile' => codecept_data_dir() . 'user.php'
            ]
        ];
    }

    public function testFindByCode()    
    {
        $model = User::findByEmail('admin@admin.com');
        $this->assertNotEmpty($model);
        $this->assertInstanceOf(User::class, $model);
        $this->assertEquals('admin', $model->username);
        $this->assertEquals('admin@admin.com', $model->email);
    }

    public function testFindIdentity()
    {
        $user1 = $this->tester->grabFixture('user', 'user1');
        $model = User::findById($user1->id);
        $this->assertEquals($user1->username, $model['username']);
        $this->assertEquals($user1->email, $model['email']);
    }

    public function testValidate()
    {
        $user = new User();

        // username and email has been required
        $this->assertFalse($user->save());
        $errors = $user->getErrors();
        $this->assertArrayHasKey('username', $errors);
        $this->assertArrayHasKey('email', $errors);

        // username has been unique
        $user->username = 'admin';
        $user->email = 'some@example.com';
        $this->assertFalse($user->save());
        $errors = $user->getErrors();
        $this->assertArrayHasKey('username', $errors);
        
        // email has been unique
        $user->username = 'new-user';
        $user->email = 'admin@admin.com';
        $this->assertFalse($user->save());
        $errors = $user->getErrors();
        $this->assertArrayHasKey('email', $errors);

        // valid
        $user->username = 'new-user';
        $user->email = 'some@example.com';
        $this->assertTrue($user->save());
        $errors = $user->getErrors();
        $this->assertEmpty($errors);
    }

    public function testValidatePassword()
    {
        $user = new User([
            'username' => 'same-user',
            'email' => 'same-user@example.com',
            'newPassword' => 'pass',
        ]);
        $this->assertTrue($user->save());
        $this->assertTrue($user->validatePassword('pass'));
        $this->assertFalse($user->validatePassword('qwerty'));
    }

    public function testFindUserById()
    {
        /** @var User $actual */
        $actual = $this->tester->grabFixture('user', 'user1');
        $user = User::findIdentity($actual->getId());
        expect($actual->username)->toEqual($user['username']);
        expect(User::findById(999))->toBeEmpty();
    }

    public function testFindByParams()
    {
        /** @var User $actual */        
        $actual = $this->tester->grabFixture('user', 'user2');
        $actualDeleted = $this->tester->grabFixture('user', 'user3');

        // find by username
        $query = User::findByParams($actual->username)->getModels();
        $this->assertCount(1, $query);
        $this->assertEquals([$actual->id, $actual->username, $actual->email], [$query[0]['id'], $query[0]['username'], $query[0]['email']]);
        $this->assertNotEquals($actualDeleted->id, $query[0]['id']);

        // find by full name
        $query = User::findByParams($actual->full_name)->getModels();
        $this->assertCount(1, $query);
        $this->assertEquals([$actual->id, $actual->username, $actual->email], [$query[0]['id'], $query[0]['username'], $query[0]['email']]);
        $this->assertNotEquals($actualDeleted->id, $query[0]['id']);

        // find only actual
        $query = User::findByParams()->getModels();
        $this->assertCount(2, $query);
        $this->assertContains('admin', array_column($query, 'username'));
        $this->assertContains('user', array_column($query, 'username'));
        $this->assertNotContains('test', array_column($query, 'username'));

        // find with trashed
        $query = User::findByParams(null, 'with')->getModels();
        $this->assertCount(3, $query);
        $this->assertContains('admin', array_column($query, 'username'));
        $this->assertContains('user', array_column($query, 'username'));
        $this->assertContains('test', array_column($query, 'username'));

        // find only trashed
        $query = User::findByParams(null, 'only')->getModels();
        $this->assertCount(1, $query);
        $this->assertNotContains('admin', array_column($query, 'username'));
        $this->assertNotContains('user', array_column($query, 'username'));
        $this->assertContains('test', array_column($query, 'username'));

    }

    public function testFromArray()
    {
        $attr = [
            'username' => 'toatall',
            'full_name' => 'Oleg',
            'email' => 'toatall@mail.ru',
        ];
        $model = User::fromArray($attr);
        $this->assertInstanceOf(User::class, $model);
        $this->assertEquals($attr['username'], $model->username);
        $this->assertEquals($attr['full_name'], $model->full_name);
        $this->assertEquals($attr['email'], $model->email);
    }
    

}
