<?php

namespace app\controllers;

use Yii;
use app\models\vraag;
use app\models\VraagSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use app\models\form;
use app\models\student;
use app\models\beoordeling;
use app\models\rolspeler;
use app\models\gesprek;

use kartik\mpdf\Pdf;

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
        $formModel = Form::find()->all();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'formModel' => $formModel,
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

    public function actionForm($gesprekid, $compleet=0)
    {
        $gesprek = gesprek::find()->where(['id'=>$gesprekid])->one();

        $vragen = vraag::find()->where(['formid' => $gesprek->formid])->orderBy( ['volgnr' => SORT_ASC, ] )->all();
        $student = student::find()->where(['id' => $gesprek->studentid])->one();
        $form = form::find()->where(['id'=>$gesprek->formid])->one();
        $rolspeler = Rolspeler::find()->where(['id' => $gesprek->rolspelerid])->one();

        // update gesprek(gesprekid) status=1
        $sql="update gesprek set status=1 where id = :id and status=0";
        $params = array(':id'=> $gesprekid);
        Yii::$app->db->createCommand($sql)->bindValues($params)->execute();

        if ($compleet) { // antwoordform

            $beoordeling = beoordeling::find()->where(['gesprekid' => $gesprekid])->one();
            $resultaat = json_decode($beoordeling->resultaat, true);

            return $this->render('antwoordform', [
                'vragen' => $vragen,
                'student' => $student,
                'form' => $form,
                'rolspeler' => $rolspeler,
                'resultaat' => $resultaat,
                'beoordeling' => $beoordeling,
            ]);

        } else { // vraag form

            return $this->render('vraagform', [
                'vragen' => $vragen,
                'student' => $student,
                'form' => $form,
                'rolspeler' => $rolspeler,
                'gesprek' => $gesprek,
            ]);

        }
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
