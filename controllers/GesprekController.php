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

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'rolspeler' => $rolspeler,
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
            return $this->redirect(['index']);
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

    public function actionStudent($id) {
        $gesprekken = Gesprek::find()->where(['studentid' => $id])->all();
        return $this->render('student',['gesprekken' => $gesprekken]);
    }

    public function actionRolspeler()
    {
        if (isset($_GET['token']) ) {
            $token = $_GET['token'];
            $rolspeler = Rolspeler::find()->where(['token' => $token])->one();
            if (!empty($rolspeler)) {
                $gesprekken = Gesprek::find()->where(['rolspelerid' => $rolspeler->id])->orderby(['status' => 'ASC', 'id' => 'DESC'])->all();
                return $this->render('overzicht',[
                    'gesprekken' => $gesprekken,
                    'rolspeler' => $rolspeler,
                ]);
            }
        }

        return $this->render('rolspeler');
    }
}
