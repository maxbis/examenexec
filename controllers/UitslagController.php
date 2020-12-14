<?php

namespace app\controllers;

use Yii;
use app\models\Beoordeling;
use app\models\BeoordelingSearch;
use app\models\Vraag;

use app\models\Examen;
use app\models\Werkproces;
use app\models\Student;
use app\models\Results;
use app\models\Gesprek;
use app\models\Uitslag;
use app\models\Rolspeler;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use yii\filters\AccessControl;

use yii\helpers\ArrayHelper;

/**
 * BeoordelingController implements the CRUD actions for Beoordeling model.
 */
class UitslagController extends Controller
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

    private function getScore($max, $punten) {
        // K1-W1..W4 17, 10, 19, 6 max punten
        $score=90*$punten/$max+10;
        if ($score>=80) return "G";
        if ($score>=55) return "V";
        return "O";
    }

    public function actionIndex() {
        // SPL uses wierd round up; it will always round up to the next 0.1 so 3.01 -> 3.1
        $sql="
        select naam, studentid, klas, formnaam werkproces, round( ((greatest(0,sum(score))  /maxscore*9+1))+0.049 ,1)  cijfer
            from (
                SELECT s.naam naam, s.id studentid, s.klas klas, f.werkproces formnaam, v.mappingid mappingid, 
                round(sum(r.score)/10,0) score
                FROM results r
                INNER JOIN student s on s.id=r.studentid
                INNER JOIN vraag v on v.formid = r.formid
                INNER JOIN form f on f.id=v.formid
                INNER JOIN examen e on e.id=f.examenid
                WHERE v.volgnr = r.vraagnr
                AND e.actief=1
                GROUP BY 1,2,3,4,5
                ORDER BY 1,2
            ) as sub
        INNER JOIN werkproces w ON w.id=formnaam
        group by naam, studentid, klas, formnaam, maxscore
        order by 1
        ";
        
        $result = Yii::$app->db->createCommand($sql)->queryAll();

        // print status
        //$sql2="select s.naam naam, p.werkprocesId werkproces, p.status status from beoordeling.printwerkproces p
        //        join student s on s.nummer=p.studentnummer";
        // $result2 = Yii::$app->db->createCommand($sql2)->queryAll();

        $formWpCount = $this->formWpCount();
        
        $sql="SELECT  s.naam,  f.werkproces, u.ready ready, COUNT(distinct g.formid) cnt
            FROM gesprek g
            INNER JOIN student s ON s.id=g.studentid
            INNER JOIN form f ON f.id = g.formid
            INNER JOIN examen e ON e.id=f.examenid
            LEFT JOIN uitslag u ON u.studentid=g.studentid AND u.werkproces=f.werkproces
            WHERE e.actief=1
            GROUP BY 1,2,3
            ORDER BY 1,2";
        $progres = Yii::$app->db->createCommand($sql)->queryAll();  // [ 0 => [ 'naam' => 'Achraf Rida ', 'werkproces' => 'B1-K1-W1', 'cnt' => '3'], 1 => .... ]

        //dd($progres);
        $wp=[];
        foreach($formWpCount as $key => $value) {
            $wp[]=$key;
        }

        $dataSet=[];
        foreach($progres as $item) { // init
            foreach($wp as $thisWp) {
                $dataSet[$item['naam']][$thisWp]['result']=['', ''];
                $dataSet[$item['naam']][$thisWp]['status']='';
            }
            $dataSet[$item['naam']]['studentid']="";
        }

        foreach($progres as $item) {
            if ( $item['ready'] ) {
                $dataSet[$item['naam']][$item['werkproces']]['status']=99;
            } else {
                $dataSet[$item['naam']][$item['werkproces']]['status']=$item['cnt'];
            }
           
        }

        foreach($result as $item) {
            $dataSet[$item['naam']][$item['werkproces']]['result']=[ $item['cijfer'], $this->rating($item['cijfer']) ];
            $dataSet[$item['naam']]['studentid']=$item['studentid'];
            $dataSet[$item['naam']]['groep']=$item['klas'];
        }
        //d($wp);
        //d($werkproces);
        //dd($dataSet);

        return $this->render('index', [
            'dataSet' => $dataSet,
            'formWpCount' =>$formWpCount, // formcount per wp
            'wp' => $wp,
        ]);
    }

    private function formWpCount() {
        $sql="  SELECT werkproces, COUNT(*) cnt FROM form f
                INNER JOIN examen e ON f.examenid=e.id 
                WHERE e.actief=1
                GROUP BY 1";
        $formWpCount = Yii::$app->db->createCommand($sql)->queryAll();
        $formWpCount = Arrayhelper::map($formWpCount,'werkproces','cnt'); // output [ 'B1-K1-W1' => '3', 'B1-K1-W2' => '2', ... ]
        return($formWpCount);
    }

    private function rating($cijfer) {
        if ( $cijfer > 8 ) return "G"; 
        if ( $cijfer >= 5.5 ) return "V";
        return "O";
    }

    // show filled in (SPL) form for 2nd beoordeelaar (form is HTML variant of the fial PDF version)
    function actionResult($studentid, $wp){
        $examen=Examen::find()->where(['actief'=>1])->asArray()->one();
        $werkproces=Werkproces::find()->where(['id'=>$wp])->asArray()->one();
        $student=Student::find()->where(['id'=>$studentid])->asArray()->one();

        $sql="
            SELECT  v.mappingid mappingid, r.formid formid, r.studentid studentid, f.omschrijving fnaam, c.omschrijving cnaam, c.nul, c.een, c.twee, c.drie, c.cruciaal, sum(score) score
            FROM results r
            INNER JOIN form f ON f.id=r.formid
            INNER JOIN vraag v ON v.id=r.vraagid
            INNER JOIN examen e ON e.id = f.examenid
            INNER JOIN criterium c ON c.id=v.mappingid
            WHERE e.actief=1
            AND r.studentid=:studentid
            AND f.werkproces=:werkproces
            GROUP BY 1,2,3,4,5,6,7,8,9,10
            ORDER BY 1,2
        ";
        $params = [':studentid'=> $studentid,':werkproces'=>$wp];
        $results = Yii::$app->db->createCommand($sql)->bindValues($params)->queryAll();

        //dd($results);

        $uitslag=Uitslag::find()->where(['and', ['studentid'=>$studentid], ['werkproces'=>$wp], ['examenid'=>$examen['id']] ])->one();

        $rolspelers = Rolspeler::find()->where(['actief'=>1])->orderBy(['naam'=>SORT_ASC])->all();

        if (! $uitslag ) { // if uitslag is not checked/ready or empty, get all remarks
            $uitslag = new Uitslag();
            $sql="
                SELECT GROUP_CONCAT(CONCAT('[',f.omschrijving,']: ', opmerking, '\n')) opmerkingen
                FROM beoordeling b
                INNER JOIN form f ON f.id=b.formid
                INNER JOIN examen e ON e.id = f.examenid
                WHERE studentid=:studentid
                AND f.werkproces=:werkproces
                AND opmerking != '';
            ";
            $params = [':studentid'=> $studentid,':werkproces'=>$wp];
            $commentaar = Yii::$app->db->createCommand($sql)->bindValues($params)->queryAll()[0]['opmerkingen'];
            $uitslag->commentaar = str_replace(',[', '[', $commentaar);

            $sql="
                SELECT rolspelerid
                FROM gesprek g
                INNER JOIN form f ON f.id=g.formid
                WHERE studentid=:studentid
                AND werkproces=:werkproces
                ORDER BY g.id DESC
                LIMIT 1
            ";
            $params = [':studentid'=> $studentid,':werkproces'=>$wp];
            $rolspeler1 = Yii::$app->db->createCommand($sql)->bindValues($params)->queryAll()[0]['rolspelerid'];
            $uitslag->beoordeelaar1id = $rolspeler1;
            //dd($rolspeler1);
           
            $uitslag->studentid = $studentid;
            $uitslag->werkproces = $wp;
            $uitslag->examenid = $examen['id'];
        }

        return $this->render('results', [
            'examen' => $examen,
            'werkproces' =>$werkproces,
            'student' => $student,
            'results' => $results, 
            'model' => $uitslag,
            'rolspelers' => $rolspelers,
        ]);

    }

    // with studentid and formid get the most recent gesprek
    function actionGetForm($studentid, $formid) {
        $gesprek = Gesprek::find()->Where(['formid'=>$formid])->andWhere(['studentid'=>$studentid])->orderby(['id' => 'SORT_DESC'])->asArray()->one();
        return $this->redirect(['/vraag/form', 'gesprekid'=>$gesprek['id'] , 'compleet'=>1]);
    }

    function actionUpdate() {
        $postedModel = new Uitslag();

        $postedModel->load(Yii::$app->request->post());
        if ( $postedModel->id ) {
            $model = Uitslag::findOne($postedModel->id);
            $model->load(Yii::$app->request->post());
        } else {
            $model = $postedModel;
        }

        if ($model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->redirect(Yii::$app->request->referrer);
        }
    }
}

