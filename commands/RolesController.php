<?php

namespace app\commands;

use app\models\User;
use Exception;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\BaseConsole;
use yii\helpers\Console;

/**
 * Управление ролями
 * 
 * yii roles/init - инициализация ролей
 * yii roles/assign - интерактивное предоставление роли пользователю
 * yii roles/revoke - интерактивный отзыв роли у пользователя
 */
class RolesController extends Controller
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
     * Инициализация ролей
     * @return void
     */
    public function actionInit()
    {
        $auth = Yii::$app->authManager;
        $admin = $auth->createRole('admin');
        $admin->description = 'Администратор';
        $auth->add($admin);
    }

    /**
     * Добавление роли пользователю
     * @return mixed
     */
    public function actionAssign(?string $username, ?string $role)
    {
        if ($username === null) {
            $username = $this->input('username');
        }
        if ($role === null) {
            $role = $this->input('role');
        }

        $userModel = $this->findUser($username);
        if ($userModel == null) {
            $this->stdout("Пользователь $username не найден!", Console::FG_RED);
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $auth = Yii::$app->authManager;
        $roleModel = $auth->getRole($role);
        if ($roleModel == null) {
            $this->stdout("Роль $role не найдена!", Console::FG_RED);
            return ExitCode::UNSPECIFIED_ERROR;
        }

        if (count($auth->getRolesByUser($userModel->id)) > 0) {
            $this->stdout("Пользователь $username уже подключен к роли $role!", Console::FG_GREEN);
            return ExitCode::OK;
        }

        if ($auth->assign($roleModel, $userModel->id)) {
            $this->stdout("Пользователь $username успешно подключен к роли $role!", Console::FG_GREEN);
            return ExitCode::OK;
        }
    }

    /**
     * Отзыв роли у пользователя
     * @return mixed
     */
    public function actionRevoke()
    {
        $username = $this->input('username');
        $role = $this->input('role');

        $userModel = $this->findUser($username);
        if ($userModel == null) {
            $this->stdout("Пользователь $username не найден!", Console::FG_RED);
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $auth = Yii::$app->authManager;
        $roleModel = $auth->getRole($role);
        if ($roleModel == null) {
            $this->stdout("Роль $role не найдена!", Console::FG_RED);
            return ExitCode::UNSPECIFIED_ERROR;
        }

        if (count($auth->getRolesByUser($userModel->id)) == 0) {
            $this->stdout("Пользователь $username не подключен к роли $role!", Console::FG_GREEN);
            return ExitCode::OK;
        }

        if ($auth->revoke($roleModel, $userModel->id)) {
            $this->stdout("Пользователь $username успешно отключен от роли $role!", Console::FG_GREEN);
            return ExitCode::OK;
        } else {
            $this->stdout("Отключение пользователя $username от роли $role не выполнено!", Console::FG_RED);
            return ExitCode::UNSPECIFIED_ERROR;
        }

    }

    /**
     * @param string $username
     * @return User|null
     */
    private function findUser(string $username)
    {
        return User::findOne(['username' => $username]);
    }


}