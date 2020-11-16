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

    public function actionFormpost($totaalString, $statusString, $gesprekid, $formId, $studentid, $studentnr, $rolspelerid, $opmerking)
    {
        // $totaalString contains the values of the answers 1-3-2 (1 point, 3 points, 2 points for question 1,2,and 3)
        // $statusString contains the answers 1-3-2 (Yes, No, Sometimes, for question 1,2,and 3)
        $result = [ 'studentid' => $studentid,
                    'studentnr' => $studentnr,
                    'formid' => $formId,
                    'rolspelerid' => $rolspelerid,
                    'answers' => explode("-",$statusString),
                    'points' => explode("-",$totaalString),
                    'totaalscore' => array_sum(explode("-",$totaalString))];
        // ToDo hier moeten een mapping worden gemaakt tussen vragen en remote id
        // dus alle antwoorden moeten hier worden doorlopen om de punten per mapping te maken.
        // easiest is om alle foreign id's as hidden form variabele mee te sturen

        $model = new Beoordeling();
        $model->gesprekid = $gesprekid;
        $model->studentid = $studentid;
        $model->formid = $formId;
        $model->opmerking = $opmerking;
        $model->rolspelerid = $rolspelerid;
        $model->resultaat = json_encode($result);

        // JSON example: {"studentid":"5","studentnr":"2081428","formid":"1","rolspelerid":"7","answers":["1","1","1","1","1","1","1","1","1"],"points":["10","5","5","5","5","5","5","10","0"],"totaalscore":50}
        $sql="delete from results where studentid=:studentid and formid=:formid";
        $params = [ ':studentid'=> $studentid,  ':formid'=> $formId, ];
        $error = Yii::$app->db->createCommand($sql)->bindValues($params)->execute();

        for($i=0; $i<count($result['answers']); $i++) {
            
            $sql="insert into results (studentid, formid, vraagnr, antwoordnr, score)
                    values(:studentid, :formid, :vraagnr, :antwoordnr, :score)";
            $params = [ 'studentid'=> $studentid,
                        ':formid'=> $formId,
                        ':vraagnr'=> $i+1,  // vraag starts counting at 1
                        ':antwoordnr'=> $result['answers'][$i],
                        ':score'=> $result['points'][$i] ];
            $error = Yii::$app->db->createCommand($sql)->bindValues($params)->execute();
        }
        // END update resutls in table

        // delete old beoordeling
        // als status van een gesprek is terugegezet wordt een nieuwe beoordeling gesaved en worden
        // oude met zelfde gesprekid verwijderd. Er bestaat dus maar een beoordeling per gesprek
        // In de log is de oude beoordleing nog wel te vinden.
        $sql="delete from beoordeling where gesprekid=:gesprekid";
        $params = [ ':gesprekid'=> $gesprekid, ];
        $error = Yii::$app->db->createCommand($sql)->bindValues($params)->execute();

        writeLog("Beoordeling $studentid $gesprekid $formId $model->resultaat");

        if ($model->save()) {
            $sql="update gesprek set status=2 where id=:gesprekid";
            $params = [ ':gesprekid'=> $gesprekid, ];
            $error = Yii::$app->db->createCommand($sql)->bindValues($params)->execute();

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

    public function actionExport()
    {
        //$beoordeling = Beoordeling::find()->all();
        $sql = "select * FROM beoordeling
                INNER JOIN form ON beoordeling.formid=form.id
                INNER JOIN examen ON form.examenid=examen.id
                WHERE examen.actief=1";
        $beoordeling = Beoordeling::findBySql($sql)->all();
        $fileNaam=str_replace(' ', '_', $beoordeling[0]->form->examen->naam);
        $fileNaam.='_JSON_'.date("j_m_Y").'.txt';

        $output = [];
        foreach($beoordeling as $item) {
            $output[] = json_decode( $item['resultaat'], true);
        }
        header("Content-type: text/plain");
        header("Content-Disposition: attachment; filename=$fileNaam");
        echo json_encode($output);
    }

    public function actionExport2()
    {
        // temp function, in the future this needs to be integrated with the Kerntaakbeoordelingen App
        // for now the interface is manual -> output queries and manual exucutes them
        // 1-0 means form 1 question 0 maps to cirteriumID (in other app) 36
        // ToDo put mapping in vraag and change json to contain mapping
        $formMapping = [    '1-0' => 36, // Gesprek PvE1
                            '1-1' => 37, '1-2' => 37, '1-3' => 37, '1-4' => 37, '1-5' => 37,  '1-6' => 37, // Gesprek PvE1
                            '1-7' => 38, '1-8' => 38, // Gesprek PvE1
                            '2-0' => 42, '2-1' => 42, // Gesprek PvE2
                            '3-0' => 46, '3-1' => 46, // Gesprek PP
                            '4-0' => 50, '4-1' => 50, '4-2' => 50, // Gesprek FO
                            '5-0' => 53, '5-1' => 53, // Gesprek TO
                            '11-0' => 51, '11-1' => 51, '11-2' => 51, // Document TO (Schema e.d.)
                            '11-3' => 52, '11-4' => 52, '11-5' => 52, '11-6' => 52, '11-7' => 52, '11-8' => 52, '11-9' => 52, // Document TO (ERD)
                        ];  
        $examenID = 12; // examenID in foreign app.

        // find all beoordlingen
        // $beoordeling = Beoordeling::find()->all();
        $sql = "select * FROM beoordeling
                INNER JOIN form ON beoordeling.formid=form.id
                INNER JOIN examen ON form.examenid=examen.id
                WHERE examen.actief=1";
        $beoordeling = Beoordeling::findBySql($sql)->all();

        $output = [];
        foreach($beoordeling as $item) {
            $output[] = json_decode( $item['resultaat'], true);
        }
        // all beoordelingen are now put in array of assiociative arrays

        echo "<pre>";
        foreach($output as $item) { // take one beoordeling at a time      
            echo '<br>'.'# Student: '.$item['studentnr'].'<br>';
            $result = [];

            for($i=0; $i<count($item['points']); $i++) {    // $item['points'] is an array with the points to the questions
                $index= $item['formid'].'-'.$i;             // create index x-y where x is form and y is question number
                if(array_key_exists($index, $formMapping)) {
                    if (! array_key_exists($formMapping[$index], $result)) {
                        $result[ $formMapping[$index] ] = 0;
                    }
                    $result[ $formMapping[$index] ] += $item['points'][$i]; // translate index x-y into nummber with $formmappings
                } else {                                                    // the number is the id in the foreign database
                    echo "No mapping for form ".$item['formid']." question ".$i; // this questin has nog mapping in teh table $formMappings
                }
            }

            foreach ($result as $key => $value) { // the result needs to betranslated into queries
                $score = intval( ($value+5)/10 );
                echo "Original score: $value <br>";
                echo "delete from scorestudent where examenid=12 and studentnummer=".$item['studentnr']." and criteriumId=$key ;";
                echo "<br>";
                echo "insert into scorestudent (studentnummer, criteriumId, score, examenid) values(".$item['studentnr'].",". $key.",". $score.", 12 );";
                echo "<br>";
            }
   
        }

        echo "<pre>";
        exit;

    }

    public function actionExport3() {
        
        $examenid=12;
        $sql="  SELECT s.naam naam, s.nummer studentnr, f.omschrijving formnaam, v.mappingid mappingid, min(v.volgnr) vraagnr, count(*) aantal, v.mappingid mappingid, sum(r.score) score
                FROM results r
                INNER JOIN student s on s.id=r.studentid
                INNER JOIN vraag v on v.formid = r.formid
                INNER JOIN form f on f.id=v.formid
                INNER JOIN examen e on e.id = f.examenid
                WHERE v.volgnr = r.vraagnr
                AND e.actief = 1
                group by naam, studentnr, formnaam, mappingid
                order by naam, mappingid
                ";
        $result = Yii::$app->db->createCommand($sql)->queryAll();
        $teller=0;
        $output = "";
        $output .= "<pre>";
        foreach($result as $row) {
            $totVraag = $row['vraagnr'] + $row['aantal'] -1;
            $score = intval( ($row['score']+5)/10 );
            $output .= "<br>### ".++$teller." student: ".$row['naam']." form ".$row['formnaam']." vraag ".$row['vraagnr']." - ".$totVraag." SPL score: ".$score."(".$row['score'].") ###<br>";
            $output .= "delete from scorestudent where examenid=".$examenid." and studentnummer=".$row['studentnr']." and criteriumId=".$row['mappingid'].";";
            $output .= "<br>";
            $output .= "insert into scorestudent (studentnummer, criteriumId, score, examenid) values(".$row['studentnr'].",". $row['mappingid'].",". $score.", ".$examenid." );";
            $output .= "<br>";
        }
        $output .=  "</pre>";

        return $this->render('query', [
            'output' => $output,
        ]);
        echo $output;
    }


    public function actionExport4() {
            
        $examenid=12;
        $sql="  select * 
                from rolspeler r where 
                actief = 1 AND id  not in (
                select rolspelerid from gesprek g
                where r.id=g.rolspelerid
                and status <2 )
                order by r.naam
            ";
        $result = Yii::$app->db->createCommand($sql)->queryAll();
        $teller=0;
        $output = "";
        $output .= "<pre>";
        foreach($result as $row) {
            $output .= $row['naam']."<br>";
        }
        $output .=  "</pre>";

        return $this->render('query', [
            'output' => $output,
        ]);
        echo $output;
    }

}