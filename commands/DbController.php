<?php

namespace app\commands;

use app\models\Organization;
use app\models\User;
use Yii;
use yii\console\Controller;
use Faker\Factory;

/**
 * Загрузка начальных данных в БД
 * - организации
 * - пользователи
 * Перед загрузкой выполняется предварительное удаление всех данных
 */
class DbController extends Controller
{

    /**
     * @var \Faker\Generator;
     */
    private $faker;

    /**
     * {@inheritDoc}
     */
    public function init()
    {
        parent::init();
        $this->faker = Factory::create('ru_RU');
    }

    /**
     * @return Yii\db\Connection
     */
    protected function getDb()
    {
        return Yii::$app->db;
    }

    /**
     * Загрузка данных
     * @return void
     */
    public function actionSeed()
    {
        $this->createOrganizations();
        $this->createLogins();
    }

    /**
     * Очистка всех данных
     * @return void
     */
    public function actionTruncate()
    {
        $this->truncateTables();
    }

    /**
     * Создание фейковых организаций
     * @return void
     */
    private function createOrganizations()
    {
        for ($i = 0; $i <= 3; $i++) {
            $code = sprintf("%'04d", $i);
            $nowDbDate = new \yii\db\Expression('NOW()');

            $organization = new Organization([
                'code' => $code,
                'name' => "Organization {$code}",
                'created_at' => $nowDbDate,
                'updated_at' => $nowDbDate,
            ]);
            if ($organization->save()) {
                $this->createUsers($code);
            }
        }
    }

    /**
     * Создание фейковых пользователей
     * @param string $orgCode
     * @param int $count
     * @return void
     */
    private function createUsers(string $orgCode, int $count = 5)
    {
        $nowDbDate = new \yii\db\Expression('NOW()');
        $password = Yii::$app->security->generatePasswordHash('123456789');

        for ($i = 0; $i < $count; $i++) {
            $user = new User([
                'username' => $this->faker->userName(),
                'password' => $password,
                'full_name' => $this->faker->name(),
                'position' => $this->faker->randomElement(['Юрист', 'Бухгалтер', 'Администратор', 'Специалист', 'Программист']),
                'email' => $this->faker->email(),
                'org_code' => $orgCode,
                'org_code_select' => $orgCode,
                'created_at' => $nowDbDate,
                'updated_at' => $nowDbDate,
            ]);
            if ($user->save()) {
                // 
            }
        }
    }

    /**
     * Полная очистка данных
     * @return void
     */
    private function truncateTables()
    {
        $this->getDb()->createCommand('TRUNCATE TABLE "vacations_kind" CASCADE;')->execute();
        $this->getDb()->createCommand('TRUNCATE TABLE "vacations_merged" CASCADE;')->execute();
        $this->getDb()->createCommand('TRUNCATE TABLE "vacations"')->execute();
        $this->getDb()->createCommand('TRUNCATE TABLE "employees" CASCADE;')->execute();
        $this->getDb()->createCommand('TRUNCATE TABLE "departments" CASCADE;')->execute();
        $this->getDb()->createCommand('TRUNCATE TABLE "users" CASCADE;')->execute();
        $this->getDb()->createCommand('TRUNCATE TABLE "organizations" CASCADE;')->execute();

        $this->getDb()->createCommand('TRUNCATE TABLE "auth_assignment" CASCADE;')->execute();
        $this->getDb()->createCommand('TRUNCATE TABLE "auth_item_child" CASCADE;')->execute();
        $this->getDb()->createCommand('TRUNCATE TABLE "auth_rule" CASCADE;')->execute();
        $this->getDb()->createCommand('TRUNCATE TABLE "auth_item" CASCADE;')->execute();

        $this->getDb()->createCommand('TRUNCATE TABLE "load_history" CASCADE;')->execute();
    }

    /**
     * Создание УЗ администратора и пользователя
     * @return void
     */
    private function createLogins()
    {
        $nowDbDate = new \yii\db\Expression('NOW()');
        $orgCode = $this->getDb()->createCommand("SELECT [[code]] FROM {{%organizations}} ORDER BY RANDOM() LIMIT 1")->queryScalar();

        (new User([
            'username' => 'admin',
            'password' => Yii::$app->security->generatePasswordHash('secret'),
            'full_name' => 'Администратор',
            'position' => 'Админ',
            'email' => 'admin@example.com',
            'org_code' => $orgCode,
            'org_code_select' => $orgCode,
            'created_at' => $nowDbDate,
            'updated_at' => $nowDbDate,
        ]))->save();

        (new User([
            'username' => 'user',
            'password' => Yii::$app->security->generatePasswordHash('secret'),
            'full_name' => 'Пользователь',
            'position' => 'Пользователь',
            'email' => 'user@example.com',
            'org_code' => $orgCode,
            'org_code_select' => $orgCode,
            'created_at' => $nowDbDate,
            'updated_at' => $nowDbDate,
        ]))->save();
    }

}
