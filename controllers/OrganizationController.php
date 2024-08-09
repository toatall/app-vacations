<?php

namespace app\controllers;

use app\components\SharedDataFilter;
use app\models\Organization;
use tebe\inertia\web\Controller;
use Yii;
use yii\filters\AccessControl;
use yii\web\HttpException;
use yii\web\Response;

class OrganizationController extends Controller
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
                        'allow' => true,
                        'roles' => ['admin']
                    ]
                ]
            ],
            [
                'class' => SharedDataFilter::class
            ]
        ];
    }

    /**
     * @param string $search
     * @param string $trashed
     * @param string $remember
     * @param int $page
     * @return array|string
     * @todo Реализовать поддержку trashed во frontend
     */
    public function actionIndex($search = null, $trashed = null, $remember = null, $page = 1)
    {
        if ($remember === 'forget') {
            $search = null;
            $trashed = null;
        }

        $dataProvider = Organization::findByParams($search, $trashed);

        return $this->inertia('Organizations/Index', [
            'filters' => [
                'search' => $search,
                'trashable' => $trashed,
            ],
            'organizations' => [
                'data' => $dataProvider->getModels(),                
            ]
        ]);
    }

    /**
     * @return array|string
     */
    public function actionCreate()
    {
        return $this->inertia('Organizations/Create', [
            'labels' => Organization::attributeLabelsStatic(),
        ]);
    }

    /**
     * @param int $code
     * @return array|string
     * @throws HttpException
     */
    public function actionEdit($code)
    {
        $organization = Organization::findByCode($code);
        if (is_null($organization)) {
            throw new HttpException(404);
        }
        return $this->inertia('Organizations/Edit', [
            'organization' => $organization,            
            'labels' => Organization::attributeLabelsStatic(),
        ]);
    }

    /**
     * @return Response
     */
    public function actionInsert()
    {
        $params = Yii::$app->request->post();
        $organization = Organization::fromArray($params);
        if ($organization->save()) {
            Yii::$app->session->setFlash('success', 'Организация создана.');
            return $this->redirect(['organization/index']);
        }
        Yii::$app->session->setFlash('errors', $organization->getErrors());
        return $this->redirect(['organization/create']);
    }

    /**
     * @param int $code
     * @return Response
     * @throws HttpException
     */
    public function actionUpdate($code)
    {
        $organization = Organization::findOne($code);
        if (is_null($organization)) {
            throw new HttpException(404);
        }
        $organization->attributes = Yii::$app->request->post();
        if ($organization->save()) {
            Yii::$app->session->setFlash('success', 'Организация обновлена.');
            return $this->redirect(['organization/edit', 'code' => $code]);
        }
        Yii::$app->session->setFlash('errors', $organization->getErrors());
        return $this->redirect(['organization/edit', 'code' => $code]);
    }

    /**
     * @param int $code
     * @return Response
     * @throws HttpException
     */
    public function actionDelete($code)
    {
        $organization = Organization::findOne($code);
        if (is_null($organization)) {
            throw new HttpException(404);
        }
        if ($organization->delete() > 0) {
            Yii::$app->session->setFlash('success', 'Организация удалена.');
        }
        return $this->redirect(['organization/edit', 'code' => $code]);
    }

    /**
     * @param int $code
     * @return Response
     * @throws HttpException
     */
    public function actionRestore($code)
    {
        $organization = Organization::findOne($code);
        if (is_null($organization)) {
            throw new HttpException(404);
        }
        if ($organization->restore() > 0) {
            Yii::$app->session->setFlash('success', 'Организация восстановлена.');
        }
        return $this->redirect(['organization/edit', 'code' => $code]);
    }

}
