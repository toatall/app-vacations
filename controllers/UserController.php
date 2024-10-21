<?php

namespace app\controllers;

use app\components\SharedDataFilter;
use app\models\Organization;
use app\models\User;
use tebe\inertia\web\Controller;
use Yii;
use yii\filters\AccessControl;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;
use yii\web\Response;

class UserController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['edit', 'update'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['index', 'create', 'insert', 'delete', 'restore'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ]
            ],
            [
                'class' => SharedDataFilter::class,
            ]
        ];
    }

    /**
     * Главная страница
     * @param string|null $search
     * @param string|null $role
     * @param string|null $trashed
     * @param string|null $remember
     * @return array|string
     */
    public function actionIndex($search = null, $role = null, $trashed = null, $remember = null)
    {
        if ($remember === 'forget') {
            $search = null;
            $role = null;
            $trashed = null;
        }

        $dataProvider = User::findByParams($search, $trashed);

        return $this->inertia('Users/Index', [
            'filters' => [
                'search' => $search,
                'role' => $role,
                'trashed' => $trashed,
            ],
            'users' => $this->mapUsers($dataProvider->getModels()),
            'labels' => User::attributeLabelsStatic(),
        ]);
    }

    /**
     * Форма создания пользователя
     * @return array|string
     */
    public function actionCreate()
    {
        return $this->inertia('Users/Create', [
            'labels' => User::attributeLabelsStatic(),
            'organizations' => Organization::findActual()->asArray()->all(),
        ]);
    }

    /**
     * Сохранение нового пользователя
     * @return Response
     */
    public function actionInsert()
    {
        $params = Yii::$app->request->post();
        $user = User::fromArray($params);
        if ($user->save()) {
            Yii::$app->session->setFlash('success', 'Пользователь создан.');
            return $this->redirect(['user/index']);
        }
        Yii::$app->session->setFlash('errors', $user->getErrors());
        return $this->redirect(['user/create']);
    }

    /**
     * Форма редактирования пользователя
     * @param int $id
     * @return array|string
     * @throws HttpException
     */
    public function actionEdit(int $id)
    {
        $user = User::findById($id);

        if (is_null($user)) {
            throw new HttpException(404);
        }       

        if (!Yii::$app->user->can('admin')) {
            if ($user['id'] !== Yii::$app->user->id) {
                throw new ForbiddenHttpException();
            }
        }
        
        return $this->inertia('Users/Edit', [
            'user' => $user,
            'organizations' => Organization::findActual()->asArray()->all(),
            'labels' => User::attributeLabelsStatic(),
            'roles' => [
                'all' => Yii::$app->roles->allList(),
                'currentUser' => Yii::$app->roles->userList($id),
            ],
        ]);
    }

    /**
     * Сохранение редактируемого пользователя
     * @param int $id
     * @return Response
     * @throws HttpException
     */
    public function actionUpdate(int $id)
    {
        $user = User::findOne($id);
        
        if (is_null($user)) {
            throw new HttpException(404);
        }
        
        if (!Yii::$app->user->can('admin')) {
            if ($user['id'] !== Yii::$app->user->id) {
                throw new ForbiddenHttpException();
            }
        }
        else {
            // сохранение ролей
            $roles = (array)Yii::$app->request->post('roles');
            Yii::$app->roles->update($id, $roles);
        }

        $user->attributes = Yii::$app->request->post();        
        if ($user->save()) {
            Yii::$app->session->setFlash('success', 'Пользователь обновлен.');
            return $this->redirect(['user/edit', 'id' => $id]);
        }
        Yii::$app->session->setFlash('errors', $user->getErrors());
        return $this->redirect(['user/edit', 'id' => $id]);
    }

    /**
     * Удаление пользователя
     * @param int $id
     * @return Response
     * @throws HttpException
     */
    public function actionDelete(int $id)
    {
        $user = User::findOne($id);
        if (is_null($user)) {
            throw new HttpException(404);
        }        
        if ($user->delete() > 0) {
            Yii::$app->session->setFlash('success', 'User deleted.');
        }
        return $this->redirect(['user/edit', 'id' => $id]);
    }

    /**
     * Восстановление пользователя (ранее удаленного)
     * @param $id
     * @return Response
     * @throws HttpException
     */
    public function actionRestore(int $id)
    {
        $user = User::findOne($id);
        if (is_null($user)) {
            throw new HttpException(404);
        }
        if ($user->restore() > 0) {
            Yii::$app->session->setFlash('success', 'User restored.');
        }
        return $this->redirect(['user/edit', 'id' => $id]);
    }

    /**
     * @param array $users
     * @return array
     */
    private function mapUsers(array $users)
    {       
        return array_map(function($user) {
            return [
                'id' => $user['id'],
                'username' => $user['username'],
                'full_name' => $user['full_name'],
                'email' => $user['email'],
                'org_code' => $user['org_code'],
                'post' => $user['post'],
                'roles' => Yii::$app->roles->userListDescription($user['id']),
                'created_at' => $user['created_at'],
                'updated_at' => $user['updated_at'],
            ];
        }, $users);
    }
    
}
