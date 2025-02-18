<?php

namespace app\controllers;

use app\components\LdapAuthenticate;
use app\components\LdapFinder;
use app\components\SharedDataFilter;
use app\models\LoginForm;
use app\models\UserFactory;
use tebe\inertia\web\Controller;
use Yii;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\ServerErrorHttpException;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            [
                'class' => AccessControl::class,
                'only' => ['index', 'logout'],
                'rules' => [
                    [
                        'actions' => ['index', 'logout'],
                        'allow' => true,
                        'roles' => ['@']
                    ]
                ],
            ],
            [
                'class' => SharedDataFilter::class
            ]
        ];
    }

    /**
     * @return string
     */
    public function actionError()
    {
        $this->layout = 'error';
        if (($exception = Yii::$app->getErrorHandler()->exception) === null) {
            $exception = new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }
        Yii::$app->getResponse()->setStatusCodeByException($exception);
        return $this->render('error', [
            'name' => $exception->getName(),
            'message' => $exception->getMessage(),
            'exception' => $exception,
        ]);
    }

    /**
     * Главная страница
     * @return string
     */
    public function actionIndex()
    {
        return $this->inertia('Dashboard/Index');
    }    

    /**
     * Смена организации
     * @return void|boolean
     */
    public function actionChangeOrganization()
    {        
        $code = Yii::$app->request->post('code');
        if (Yii::$app->user->isGuest) {
            return false;
        }
        /** @var \app\models\User $user */
        $user = Yii::$app->user->identity;       
        $user->org_code_select = $code;        
        $user->save(false, ['org_code_select']);
        Yii::$app->session->remove('user');
        Yii::$app->session->setFlash('success', 'Организация изменена');
    }

    /**
     * Вход
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        // Аутентификация без ввода логина и пароля (на сервере должна быть включена windows-аутентификация)
        if (Yii::$app->params['useWindowsAuthenticate'] && isset($_SERVER['AUTH_USER'])) {
            $username = $this->removeDomainInUsername($_SERVER['AUTH_USER']);
            $ldapAuthenticate = new LdapAuthenticate(new LdapFinder(Yii::$app->params['ldapConfig']), new UserFactory());
            if ($ldapAuthenticate->login($username)) {
                return $this->goBack();
            }
            else {
                // return not found page
                return $this->inertia('Auth/NotFound', [
                    'username' => $username,
                ]);
            }
        }

        $postData = Yii::$app->request->post();
        $model = new LoginForm();
        if ($model->load($postData, '')) {
            if ($model->login()) {
                return $this->goBack();
            }
            Yii::$app->session->setFlash('errors', $model->getErrors());
            return $this->redirect(['login']);
        }

        return $this->inertia('Auth/Login');        
    }

    /**
     * Удаление домена из имени пользователя
     * @param string $fullName
     * @return string
     */
    protected function removeDomainInUsername($fullName)
    {        
        $parts = preg_split("/\\\/", $fullName);
        return $parts[1] ?? $parts[0];
    }

    /**
     * Выход
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function action500()
    {
        sleep(1);
        throw new ServerErrorHttpException('An unexpected error happened.');
    }
}
