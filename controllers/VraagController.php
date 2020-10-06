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

    public function actionForm($formid, $studentid=0, $rolspelerid=0, $gesprekid=0, $compleet=0)
    {
        $vragen = vraag::find()->where(['formid' => $formid])->orderBy( ['volgnr' => SORT_ASC, ] )->all();
        $student = student::find()->where(['id' => $studentid])->one();
        $form = form::find()->where(['id'=>$formid])->one();
        $rolspeler = Rolspeler::find()->where(['id' => $rolspelerid])->one();

        // update gesprek(gesprekid) status=1
        $sql="update gesprek set status=1 where id = :id";
        $params = array(':id'=> $gesprekid);
        Yii::$app->db->createCommand($sql)->bindValues($params)->execute();

        if ($compleet) {
            $beoordeling = beoordeling::find()->where(['gesprekid' => $gesprekid])->one();
            $resultaat = json_decode($beoordeling->resultaat, true);
            return $this->render('antwoordform', [
                'vragen' => $vragen,
                'student' => $student,
                'form' => $form,
                'rolspeler' => $rolspeler,
                'gesprekid' => $gesprekid,
                'resultaat' => $resultaat,
                'tijd' => $beoordeling->timestamp,
                'opmerking' => $beoordeling->opmerking,
            ]);

        }

        return $this->render('vraagform', [
            'vragen' => $vragen,
            'student' => $student,
            'form' => $form,
            'rolspelerid' => $rolspelerid,
            'gesprekid' => $gesprekid,
        ]);
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
