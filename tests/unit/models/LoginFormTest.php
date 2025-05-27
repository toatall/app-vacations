<?php

namespace tests\unit\models;

use app\fixtures\UserFixture;
use app\models\auth\LoginForm;

class LoginFormTest extends \Codeception\Test\Unit
{
    private $model;

    public function _fixtures()
    {
        return [
            'user' => [
                'class' => UserFixture::class,
                'dataFile' => codecept_data_dir() . 'user.php'
            ]
        ];
    }
    

    protected function _after()
    {
        \Yii::$app->user->logout();
    }

    public function testLoginNoUser()
    {
        $this->model = new LoginForm([
            'email' => 'not_existing_email',
            'password' => 'not_existing_password',
        ]);

        $this->assertFalse($this->model->login());
        $this->assertTrue(\Yii::$app->user->isGuest);
    }

    public function testLoginWrongPassword()
    {
        $this->model = new LoginForm([
            'email' => 'demo',
            'password' => 'wrong_password',
        ]);

        $this->assertFalse($this->model->login());
        $this->assertTrue(\Yii::$app->user->isGuest);        
        $this->assertArrayHasKey('password', $this->model->errors);
    }

    public function testLoginCorrect()
    {       
        $this->model = new LoginForm([
            'email' => 'admin@admin.com',
            'password' => 'password',
        ]);
        $this->model->login();
        $this->assertTrue($this->model->login());
        $this->assertFalse(\Yii::$app->user->isGuest);
        $this->assertArrayNotHasKey('password', $this->model->errors);
    }

}
