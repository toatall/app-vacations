<?php
namespace app\components;

use Yii;
use yii\base\Component;

/**
 * Управление ролями
 */
class Roles extends Component
{

    /**
     * Объект управления ролями
     * @return \Yii\rbac\ManagerInterface
     */
    private function getAuthManager(): yii\rbac\ManagerInterface
    {
        return Yii::$app->authManager;
    }

    /**
     * Список всех ролей в системе
     * @return array|\Generator
     */
    public function allList()
    {
        $authManager = $this->getAuthManager();   
        return $authManager->getRoles();
    }

    /**
     * Список ролей подключенных пользователю $userId
     * @param int $userId
     * @return array
     */
    public function userList(int $userId)
    {
        $authManager = $this->getAuthManager();
        return array_values(array_map(fn($item) => $item->name, $authManager->getRolesByUser($userId)));
    }

    public function userListDescription(int $userId)
    {
        $authManager = $this->getAuthManager();
        return array_values(array_map(fn($item) => $item->description, $authManager->getRolesByUser($userId)));
    }

    /**
     * Список ролей текущего пользователя
     * @return array|\Generator
     */
    public function currentUserList()
    {
        if (Yii::$app->user->isGuest) {
            return [];
        }
        return $this->userList(Yii::$app->user->id);
    }

    /**
     * Обновление ролей у пользователя
     * @param int $userId идентификатор пользователя 
     * @param array $roles список ролей
     * @return void
     */
    public function update(int $userId, array $roles)
    {
        // обновление ролей может выполнять только пользователь с ролью admin
        if (!Yii::$app->user->can('admin')) {
            return;
        }
        
        // подключенные роли
        $assignedRole = $this->userList($userId);

        // роли для подключения 
        $rolesForAdd = array_diff($roles, $assignedRole);        
        if ($rolesForAdd) {
            $this->add($userId, $rolesForAdd);
        }

        // роли для удаления
        $rolesForRemove = array_diff($assignedRole, $roles);
        if ($rolesForRemove) {
            $this->remove($userId, $rolesForRemove);
        }
    }

    /**
     * Подключение ролей пользователю
     * @param int $userId идентификатор пользователя
     * @param array $roles список ролей
     * @return void
     */
    private function add(int $userId, array $roles)
    {
        $authManager = $this->getAuthManager();
        foreach((array)$roles as $roleName) {
            $role = $authManager->getRole($roleName);
            if ($role) {
                $authManager->assign($role, $userId);
            }
        }
    }

    /**
     * Отключение ролей у пользователя
     * @param int $userId идентификатор пользователя
     * @param array $roles список ролей
     * @return void
     */
    private function remove(int $userId, array $roles)
    {
        $authManager = $this->getAuthManager();
        foreach((array)$roles as $roleName) {
            $role = $authManager->getRole($roleName);
            if ($role) {
                $authManager->revoke($role, $userId);
            }
        }
    }

}