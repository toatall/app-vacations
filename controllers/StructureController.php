<?php

namespace app\controllers;

use app\components\SharedDataFilter;
use app\models\Vacation;
use tebe\inertia\web\Controller;
use Yii;
use yii\filters\AccessControl;
use yii\web\Response;

/**
 * Табель отпусков
 */
class StructureController extends Controller
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
                        'roles' => ['@']
                    ]
                ]
            ],
            [
                'class' => SharedDataFilter::class
            ]
        ];
    }

    /**
     * @return array|string
     */
    public function actionIndex()
    {
        return $this->inertia('Structure/Index', []);
    }

    /**
     * Данные для дерева
     * @param string $code_org
     * @param string $year
     * @return array
     */
    public function actionData(string $code_org, string $year)
    {
        $data = Vacation::mergedVacationsPerDayByDepartments($code_org, $year);
        $result = [];
        foreach($data as $item) {

            $keyDepartment = $item['sort_index_department'] . $item['id_department'];
            $keyEmployee = $item['sort_index_employee'] . $item['id_employee'];

            if (!isset($result[$keyDepartment])) {
                $result[$keyDepartment] = [
                    'key' => $keyDepartment,                    
                    'label' => $item['department'],
                    'data' => [
                        'name' => $item['department'], 
                        'type' => 'department',
                    ],
                    'sortIndex' => $item['sort_index_department'],   
                    'children' => [],                 
                ];
            }
            if (!isset($result[$keyDepartment]['children'][$keyEmployee])) {
                $result[$keyDepartment]['children'][$keyEmployee] = [
                    'key' => $keyEmployee,
                    'label' => $item['full_name'],
                    'data' => [
                        'type' => 'employee',
                        'full_name' => $item['full_name'],
                        'post' => $item['post'],
                    ],
                    'sortIndex' => $item['sort_index_employee'],
                    'children' => [],
                ];
            }
            $result[$keyDepartment]['children'][$keyEmployee]['children'][] = [
                'key' => null,
                'label' => 'Отпуск',                
                'data' => [
                    'type' => 'vacation',
                    'date_from' => $item['date_from'],
                    'date_to' => $item['date_to'],
                ],                
                // 'sortIndex' => $item['date']
            ];

        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $result;
    }
    
}
