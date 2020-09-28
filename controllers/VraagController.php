<?php

namespace app\controllers;

use Yii;
use app\models\vraag;
use app\models\VraagSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use app\models\form;

/**
 * VraagController implements the CRUD actions for vraag model.
 */
class VraagController extends Controller
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
     * Lists all vraag models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new VraagSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single vraag model.
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

    public function actionForm($id)
    {
        $query = vraag::find()
        ->where(['formid' => $id])
        ->orderBy( ['volgnr' => SORT_ASC, ] );

        $vragen = $query->all();

        return $this->render('vraagform', [
            'vragen' => $vragen,
        ]);
    }

    public function actionFormpost($totaalString, $statusString, $formId) {
        // $totaalString contains the values of the answers 1-3-2 (1 point, 3 points, 2 points for question 1,2,and 3)
        // $statusString contains the answers 1-3-2 (Yes, No, Sometimes, for question 1,2,and 3)
        echo $totaalString, "<br>", $statusString;
        echo "<br>";
        echo array_sum(explode("-",$totaalString));
        echo "<br>";
        echo $formId;
        //ToDo store reuslts in DB (studentid needs to be passed)
    }

    /**
     * Creates a new vraag model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new vraag();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect('index');
        }
        
        $formModel = form::find()->all();

        return $this->render('create', [
            'model' => $model,
            'formModel' => $formModel,
        ]);
    }

    /**
     * Updates an existing vraag model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect('index');
        }

        $formModel = form::find()->all();

        return $this->render('update', [
            'model' => $model,
            'formModel' => $formModel,
        ]);
    }

    /**
     * Deletes an existing vraag model.
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
     * Finds the vraag model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return vraag the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = vraag::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
