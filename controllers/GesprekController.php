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
        return $this->render('view', [
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

    public function actionLogin()
    {
        if (isset($_GET['nummer']) && $_GET['nummer']!=0 ) {
            $nummer = $_GET['nummer'];
            $student = Student::find()->where(['nummer' => $nummer])->one();

            if (!empty($student)) {

                //$gesprek = Gesprek::find()->where(['studentid'=>$student->id])->all();
                // if (count($gesprek) > 0) {
                //   return $this->redirect(['/gesprek/student', 'id' => $student->id, 'nummer' => $student->nummer]);
                //};

                $model = new Gesprek();
                $formModel = Form::find()->all();

                if ($model->load(Yii::$app->request->post()) && $model->save()) {
                    return $this->redirect(['student', 'id' => $student->id]);
                }
        
                return $this->render('create', [
                    'model' => $model,
                    'student' => $student,
                    'formModel' => $formModel,
                ]);

                // echo $student->id;
            }
        }

        return $this->render('login');
    }

    public function actionStudent($id=0, $nummer=0) {
        if (! $id) {
            $student = Student::find()->where(['nummer' => $nummer])->one();
            if (empty($student)) {
                sleep(2);
                return $this->render('login');
            }
            $id=$student->id;
        } else {
            $student = Student::find()->where(['id' => $id])->one();
        }

        $newGesprek = new Gesprek(); 
        $formModel = Form::find()->where(['actief'=>1])->all();       
        $gesprekken = Gesprek::find()->where(['studentid' => $id])->all();
        $alleGesprekken = Gesprek::find()->all();

        return $this->render('student',[
            'gesprekken' => $gesprekken,
            'alleGesprekken' => $alleGesprekken,
            'newGesprek' => $newGesprek,
            'student' => $student,
            'formModel' => $formModel,
        ]);
    }

    public function actionRolspeler($id=0,$token=0,$gesprekid=0)
    {
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
            return $this->render('overzicht',[
                'alleGesprekken' => $alleGesprekken,
                'gesprekken' => $gesprekken,
                'rolspeler' => $rolspeler,
            ]);
        }

        return $this->render('rolspeler');
    }
}
