<?php

namespace app\controllers;

use Yii;
use app\models\Beoordeling;
use app\models\BeoordelingSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;


use app\models\Rolspeler;
/**
 * BeoordelingController implements the CRUD actions for Beoordeling model.
 */
class BeoordelingController extends Controller
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
     * Lists all Beoordeling models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new BeoordelingSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Beoordeling model.
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
     * Creates a new Beoordeling model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Beoordeling();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Beoordeling model.
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

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Beoordeling model.
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

    public function actionFormpost($totaalString, $statusString, $formId, $studentid, $rolspelerid) {
        // $totaalString contains the values of the answers 1-3-2 (1 point, 3 points, 2 points for question 1,2,and 3)
        // $statusString contains the answers 1-3-2 (Yes, No, Sometimes, for question 1,2,and 3)
        $result = [ 'studentid' => $studentid,
                    'formid' => $formId, 'rolspelerid' => $rolspelerid,
                    'answers' => explode("-",$statusString), 'points' => explode("-",$totaalString),
                    'totaalscore' => array_sum(explode("-",$totaalString))];
        // d($result);
        $model = new Beoordeling();
        $model->studentid = $studentid;
        $model->formid = $formId;
        $model->rolspelerid = $rolspelerid;
        $model->resultaat = json_encode($result);

        if ($model->save()) {
            $sql="update gesprek set status=2 where studentid=:studentid and formid=:formid and rolspelerid=:rolspelerid";
            $params = [ ':studentid'=> $studentid, ':formid' => $formId, ':rolspelerid' => $rolspelerid];
            $result = Yii::$app->db->createCommand($sql)->bindValues($params)->execute();

            $token = Rolspeler::find()->where(['id' => $rolspelerid])->one();
            return $this->redirect(['/gesprek/rolspeler', 'token' => $token->token]);
        } else {
            // somehow the results are not stored in the db
            echo "Error, resutls are not saved, save this page!";
            dd($result);
            exit;
        };

       
        //ToDo store reuslts in DB (studentid needs to be passed)
    }
    
    /**
     * Finds the Beoordeling model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Beoordeling the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Beoordeling::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
