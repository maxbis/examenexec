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
        
        $sql="SELECT  s.naam,  f.werkproces, COUNT(distinct g.formid) cnt FROM gesprek g
            INNER JOIN student s ON s.id=g.studentid
            INNER JOIN form f ON f.id = g.formid
            INNER JOIN examen e ON e.id=f.examenid
            WHERE e.actief=1
            GROUP BY 1,2
            ORDER BY 1,2";
        $progres = Yii::$app->db->createCommand($sql)->queryAll();  // [ 0 => [ 'naam' => 'Achraf Rida ', 'werkproces' => 'B1-K1-W1', 'cnt' => '3'], 1 => .... ]

       // dd($werkproces);
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
            $dataSet[$item['naam']][$item['werkproces']]['status']=$item['cnt'];
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

    function actionResult($studentid, $wp){
        $examen=Examen::find()->where(['actief'=>1])->asArray()->one();
        $werkproces=Werkproces::find()->where(['id'=>$wp])->asArray()->one();
        $student=Student::find()->where(['id'=>$studentid])->asArray()->one();

        $sql="
            SELECT  v.mappingid, r.formid, f.omschrijving fnaam,  c.omschrijving cnaam, c.nul, c.een, c.twee, c.drie, c.cruciaal, sum(score) score
            FROM results r
            INNER JOIN form f ON f.id=r.formid
            INNER JOIN vraag v ON v.id=r.vraagid
            INNER JOIN examen e ON e.id = f.examenid
            INNER JOIN criterium c ON c.id=v.mappingid
            WHERE e.actief=1
            AND r.studentid=:studentid
            AND f.werkproces=:werkproces
            GROUP BY 1,2,3,4,5,6,7,8,9
            ORDER BY 1,2
        ";
        $params = [':studentid'=> $studentid,'werkproces'=>$wp];
        $results = Yii::$app->db->createCommand($sql)->bindValues($params)->queryAll();

        //dd($results);

        return $this->render('results', [
            'examen' => $examen,
            'werkproces' =>$werkproces,
            'formWpCount' =>  $this->formWpCount(), // formcount per wp
            'student' => $student,
            'results' => $results, 
        ]);

        //$beoordeling=Beoordeling::find()->select('formid, opmerking')->joinWith('form')->where(['beoordeling.studentid'=>$student])->andWhere(['form.werkproces'=>$wp])->asArray()->all();

        //foreach($beoordeling as $form) {
            
        //}

        dd($beoordeling);

        d($student);
        dd($wp);

    }

}

