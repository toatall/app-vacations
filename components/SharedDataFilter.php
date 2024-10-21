<?php

namespace app\components;

use app\models\Organization;
use app\models\User;
use Yii;
use yii\base\Action;
use yii\base\ActionFilter;
use yii\base\InvalidConfigException;

class SharedDataFilter extends ActionFilter
{
    /**
     * @param Action $action
     * @return bool
     * @throws InvalidConfigException
     * @throws \Throwable
     */
    public function beforeAction($action)
    {       
        $shared = [
            'auth' => [
                'user' => $this->getUser(),
                'useWindowsAuthenticate' => Yii::$app->params['useWindowsAuthenticate'],                
            ],
            'flash' => $this->getFlashMessages(),
            'errors' => $this->getErrors(),
            'filters' => [
                'search' => null,
                'trashed' => null
            ]
        ];

        Yii::$app->get('inertia')->share($shared);

        return true;
    }

    /**
     * @return array|null
     * @throws \Throwable
     */
    private function getUser()
    {
        $webUser = Yii::$app->getUser();
        if ($webUser->isGuest) {
            return null;
        }
                    
        /** @var User */
        $user = $webUser->getIdentity();

        $return = [
            'id' => $user->id,
            'username' => $user->username,
            'full_name' => $user->full_name,
            'email' => $user->email,          
            'roles' => Yii::$app->roles->currentUserList(),
            'org_code_select' => $user->org_code_select,
            'available_organizations' => Organization::findActual()->select('code')->asArray()->all(),
        ];                    

        return $return;
    }

    /**
     * @return array
     */
    private function getFlashMessages()
    {
        $flash = [
            'success' => null,
            'error' => null,
        ];
        if (Yii::$app->session->hasFlash('success')) {
            $flash['success'] = Yii::$app->session->getFlash('success');
        }
        if (Yii::$app->session->hasFlash('error')) {
            $flash['error'] = Yii::$app->session->getFlash('error');
        }
        return $flash;
    }

    /**
     * @return object
     */
    private function getErrors()
    {
        $errors = [];
        if (Yii::$app->session->hasFlash('errors')) {
            $errors = (array)Yii::$app->session->getFlash('errors');
        }
        return (object) $errors;
    }

}
