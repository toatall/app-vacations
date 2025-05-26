<?php

namespace app\commands;

use app\models\User;
use Yii;
use yii\console\Controller;
use yii\console\Exception;
use yii\console\widgets\Table;
use yii\helpers\BaseConsole;
use yii\helpers\Console;

/**
 * Управление пользователями
 * 
 * yii user/create - создание пользователя
 * yii user/table - список (таблица) пользователей
 */
class UserController extends Controller
{
    /**
     * {@inheritDoc}
     */
    public $color = true;

    /**
     * @param string $field
     * @param bool $required
     * @throws \yii\console\Exception
     * @return string
     */
    private function input(string $field, bool $required = true): string
    {
        $inputText = BaseConsole::input("Input $field: ");
        if ($required && empty($inputText)) {
            throw new Exception("Field '$field' cannot be empty!");
        }
        return $inputText;
    }

    /**
     * Интерактивное создание пользователя
     * @throws \yii\console\Exception
     * @return void
     */
    public function actionCreate()
    {
        $username = $this->input('username');        
        $password = $this->input('password');
        $email = $this->input('email');        

        $model = User::findOne(['username' => $username]);
        if ($model !== null) {
            throw new Exception('User already exists. Change username.');
        }

        $model = new User([
            'username' => $username,
            'newPassword' => $password,
            'email' => $email,
        ]);
        if ($model->save()) {
            $this->stdout("User $username created successfully!". PHP_EOL, Console::FG_GREEN);
        }
        else {
            $this->stdout("Failed to create user $username!". PHP_EOL, Console::FG_RED);
        }
    }

    /**
     * Список пользователей
     * @param string|null $search
     * @return void
     */
    public function actionTable($search = null)
    {
        $users = User::find()->filterWhere(['username' => $search])->all();
        $data = [];
        foreach($users as $user) {
            $data[] = [
                $user['id'],
                $user['username'],
                $user['full_name'],
                $user['created_at'],
            ];
        }
        echo Table::widget([
            'headers' => ['ИД', 'Имя пользователя', 'ФИО', 'Дата создания'],
            'rows' => $data,
        ]);
    }
    

}