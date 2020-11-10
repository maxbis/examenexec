<?php

namespace app\controllers;

use Yii;
use app\models\Gesprek;
use app\models\GesprekSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use app\models\Student;
use app\models\Form;
use app\models\Rolspeler;
use app\models\Beoordeling;

use yii\filters\AccessControl;

/**
 * GesprekController implements the CRUD actions for Gesprek model.
 */
class GesprekController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    // when logged in, any user
                    [ 'actions' => ['student','create'],
                        'allow' => true,
                    ],
                    [ 'actions' => [ 'rolspeler', 'update' ],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return (Yii::$app->user->identity->role == 'rolspeler');
                        }
                     ],
                    [ 'actions' => [],
                        'allow' => true,
                        'roles' => ['@'],
                         'matchCallback' => function ($rule, $action) {
                            return (Yii::$app->user->identity->role == 'admin');
                        }
                    ],
                ],
            ],
           
        ];
    }

    /**
     * Lists all Gesprek models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new GesprekSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $rolspeler = Rolspeler::find()->where(['actief' => '1'])->all();
        $form = Form::find()->where(['actief' => '1'])->all();
     
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'rolspeler' => $rolspeler,
            'form' => $form,
            'alleGesprekken' => Gesprek::find()->all(),
        ]);
    }

    /**
     * Displays a single Gesprek model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $beoordeling = Beoordeling::find()->where(['gesprekid' => $id])->one();
 
        return $this->render('view', [
            'beoordeling' => $beoordeling,
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Gesprek model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Gesprek();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['student', 'nummer' => $model->student->nummer]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Gesprek model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $formModel = Form::find()->all();

        return $this->render('update', [
            'model' => $model,
            'formModel' => $formModel,
        ]);
    }

    public function actionUpdateStatus($id, $status, $rolspelerid) {
        $model = $this->findModel($id);
        $model->status=$status;
        $model->rolspelerid=$rolspelerid;
        $model->save();
    }


    /**
     * Deletes an existing Gesprek model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        // delete all beoordelingen belonging to this gesprek
        Yii::$app->db->createCommand()->delete('beoordeling', 'gesprekid = '.$id)->execute();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Gesprek model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Gesprek the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Gesprek::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    // Show student screen (after login)
    public function actionStudent($id=0, $nummer=0) {

        if ( Yii::$app->request->post() ) {
            $nummer=Yii::$app->request->post();
            isset($nummer['nummer']) ? $nummer=$nummer['nummer'] : $nummer=0;
        }

        if ( $id==0 && $nummer==0 && isset($_COOKIE['student']) ) {
            $id=$_COOKIE['student'];
       }
        if ($id) {
            $student = Student::find()->where(['id' => $id])->one();
            if (empty($student)) {
                sleep(2);
                return $this->render('/student/login');
            }
        } elseif ($nummer) { 
            $student = Student::find()->where(['nummer' => $nummer])->one();
            if (empty($student)) {
                sleep(2);
                return $this->render('/student/login');
            }
            $id=$student->id;
        } 
        if (! $id) {
            return $this->render('/student/login');
        } else {
            setcookie("student", $id, time()+7200, "/");
        }

        $newGesprek = new Gesprek(); 
        $formModel = Form::find()->select(['form.id id','omschrijving','nr','examenid','form.actief actief', 'instructie'])
                        ->joinWith('examen',true,'INNER JOIN')
                        ->where(['form.actief'=>1])
                        ->andWhere(['examen.actief'=>1])->all();
        // ToDo show only gesprekken van current examen!
        // $gesprekken = Gesprek::find()->where(['studentid' => $id])->all(); // this is code before examen
        $sql = "select * FROM gesprek
                INNER JOIN form ON gesprek.formid=form.id
                INNER JOIN examen ON form.examenid=examen.id
                WHERE gesprek.studentid=:id
                AND examen.actief=1";
        $gesprekken = Gesprek::findBySql($sql, [':id' => $id])->all();

        $alleGesprekken = Gesprek::find()->all();

        return $this->render('student',[
            'gesprekken' => $gesprekken,
            'alleGesprekken' => $alleGesprekken,
            'newGesprek' => $newGesprek,
            'student' => $student,
            'formModel' => $formModel,
        ]);
    }

    public function actionRolspeler($id=0,$token="",$gesprekid=0)
    {
        // only if not admin, becasue admin needs easy access via GET token=ABC
        if ( Yii::$app->user->identity->role == 'rolspeler') {
            if ( isset($_COOKIE['rolspeler']) ) $id = $_COOKIE['rolspeler'];
        }   

        if ($id) {
            $rolspeler = Rolspeler::find()->where(['id' => $id])->andWhere(['not', ['token' => null]])->one();
        } elseif($token) {
            $rolspeler = Rolspeler::find()->where(['token' => $token])->andWhere(['not', ['token' => null]])->one();
        } else {
            return $this->render('rolspeler');
        }

        if ($gesprekid) { // we came here via a cancelled gesprek
            // set status terug naar 0
            $sql="update gesprek set status=0 where id = :id";
            $params = array(':id'=> $gesprekid);
            Yii::$app->db->createCommand($sql)->bindValues($params)->execute();
        }
       
        if (!empty($rolspeler)) {
            $gesprekken = Gesprek::find()->where(['rolspelerid' => $rolspeler->id])->orderby(['status' => 'ASC', 'id' => 'DESC'])->all();
            $alleGesprekken = Gesprek::find()->all();
            setcookie("rolspeler", $rolspeler->id, time()+7200, "/");
            return $this->render('/gesprek/rolspeler', [
                'alleGesprekken' => $alleGesprekken,
                'gesprekken' => $gesprekken,
                'rolspeler' => $rolspeler,
            ]);
        }

        return $this->render('rolspeler');
    }
}
