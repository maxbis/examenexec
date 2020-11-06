<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['login'],
                        'allow' => true,
                        'ips' => ['111.111.111.*', '222.222.222.222'],
                        'roles' => ['?'],
                    ],
                ],
            ],
            //'verbs' => [
            //    'class' => VerbFilter::className(),
            //    'actions' => [
            //        'logout' => ['post'],
            //    ],
            //],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }


    public function actionIndex()
    {
        // where do we start depends on the role...
        if ( ! isset(Yii::$app->user->identity->role) ) {
            return $this->redirect(['/student/login']);
        } elseif ( Yii::$app->user->identity->role == 'admin')  {
            return $this->redirect(['/gesprek/index']);
        } elseif ( Yii::$app->user->identity->role == 'rolspeler')  {
            return $this->redirect(['/rolspeler/login']);
        }
    }


    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {

        $this->checkIP();
        
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    private function checkIP() {
        if ( $_SERVER['REMOTE_ADDR'] == '::1' ) return; // php yii server
    
        $file = "../config/ipAllowed.txt";
    
        try { // read file and if not readble raise error and stop
            $lines = file($file);
         } catch (Exception $e) {
            $string = "Cannot acces IP Allowed file ($file) in config";
            writeLog($string);
            echo $string;
            exit;
         }
    
         $ipAllowed=[]; // all lines vlaidated will be put in this array
         for($i=0; $i<count($lines); $i++) {
            $ip = explode(' ',trim($lines[$i]))[0]; // we want teh first word
            if(filter_var(explode('/',$ip)[0], FILTER_VALIDATE_IP)) { // and we want anything beofre the / (note ip = xxx.xxx.xxx.xxx/xx)
                $ipAllowed[] = $ip; // ipnumber validate (note that subnet mask is not validated)
            }
            
         }
         //for($i=0; $i<count($ipAllowed); $i++) {
         //   $a =  $this->ipRange($ipAllowed[$i]);
         //   d($a);
         //}
    
        $weAreOK=false;
        foreach ($ipAllowed as $item) {
            $ipRange = $this->ipRange($item);
            if ( (int)$_SERVER['REMOTE_ADDR'] >= (int)$ipRange[0] && (int)$_SERVER['REMOTE_ADDR'] <= (int)$ipRange[1] ) {
                $weAreOK=true;
            }
        }
        if ( $weAreOK == false ) {
            $string = "Permission denied for ". $_SERVER['REMOTE_ADDR'];
            writeLog($string);
            echo $string;
            exit;
        }
    }

    private function ipRange($cidr)
    {
        $range = array();
        $cidr = explode('/', $cidr);
        $range[0] = long2ip((ip2long($cidr[0])) & ((-1 << (32 - (int)$cidr[1]))));
        $range[1] = long2ip((ip2long($range[0])) + pow(2, (32 - (int)$cidr[1])) - 1);
        return $range;
    }


    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        setcookie("student", "0", time()-7200, "/");
        setcookie("rolspeler", "0", time()-7200, "/");

        Yii::$app->user->logout();

        return $this->redirect(['/student/login']);
    }

    public function actionClear()
    {
        setcookie("student", "0", time(), "/");
        setcookie("rolspeler", "0", time(), "/");

        areWeOK("clear");

        return $this->render('/student/login');
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
}
