<?php

namespace app\controllers;

use app\components\SharedDataFilter;
use app\models\LoadHistory;
use tebe\inertia\web\Controller;
use yii\filters\AccessControl;

class LoadHistoryController extends Controller
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
                ],
            ],
            [
                'class' => SharedDataFilter::class
            ]
        ];
    }

    /**
     * Главная страница
     * @return string
     */
    public function actionIndex()
    {
        $model = new LoadHistory();
        $data = $model->find()->orderBy(['created_at' => SORT_DESC])->asArray()->all();
        return $this->inertia('LoadHistory/Index', ['data' => $data, 'attributes' => $model->attributeLabels()]);
    }

}
