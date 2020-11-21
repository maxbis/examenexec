<?php

namespace app\controllers;

use Yii;
use app\models\Beoordeling;
use app\models\BeoordelingSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;


/**
 * BeoordelingController implements the CRUD actions for Beoordeling model.
 */
class QueryController extends Controller
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

    public function actionExportResults() {

        $db_naam="beoordeling";

        $sql = "select otherid from examen where actief=1";
        $examenid = Yii::$app->db->createCommand($sql)->queryOne();

        if ($examenid) {
            $examenid=$examenid['otherid'];
        } else {
            $examenid=0;
        }

        $sql="  SELECT s.naam naam, s.nummer studentnr, f.omschrijving formnaam, v.mappingid mappingid, min(v.volgnr) vraagnr, count(*) aantal, v.mappingid mappingid, sum(r.score) score
                FROM results r
                INNER JOIN student s on s.id=r.studentid
                INNER JOIN vraag v on v.formid = r.formid
                INNER JOIN form f on f.id=v.formid
                INNER JOIN examen e on e.id=f.examenid
                WHERE v.volgnr = r.vraagnr
                AND e.actief=1 AND f.actief=1
                GROUP BY naam, studentnr, formnaam, mappingid
                ORDER BY naam, mappingid
                ";
        $result = Yii::$app->db->createCommand($sql)->queryAll();
        $teller=0;
        $output = "";
        $output .= "<pre>";
        foreach($result as $row) {
            $totVraag = $row['vraagnr'] + $row['aantal'] -1;
            $score = intval( ($row['score']+5)/10 );
            $output .= "<br>### ".++$teller." student: ".$row['naam']." form ".$row['formnaam']." vraag ".$row['vraagnr']." - ".$totVraag." SPL score: ".$score."(".$row['score'].") ###<br>";

            $sql = "delete from ".$db_naam.".scorestudent where examenid=".$examenid." and studentnummer=".$row['studentnr']." and criteriumId=".$row['mappingid'].";";
            $output .= $sql."<br>";
            $result = Yii::$app->db->createCommand($sql)->execute();

            $sql = "insert into ".$db_naam.".scorestudent (studentnummer, criteriumId, score, examenid) values(".$row['studentnr'].",". $row['mappingid'].",". $score.", ".$examenid." );";
            $output .= $sql."<br>";
            $result = Yii::$app->db->createCommand($sql)->execute();
         }

        $output .=  "</pre>";

        return $this->render('query', [
            'output' => $output,
        ]);
    }

    public function actionExportComments() {
        $db_naam="beoordeling";

        $sql = "select otherid from examen where actief=1";
        $examenid = Yii::$app->db->createCommand($sql)->queryOne();

        if ($examenid) {
            $examenid=$examenid['otherid'];
        } else {
            $examenid=0;
        }

        $sql="  SELECT werkproces, s.naam naam, s.nummer studentnr, GROUP_CONCAT(CONCAT('[',f.omschrijving, ']: ', opmerking)) opmerkingen
                FROM beoordeling b
                INNER JOIN form f ON f.id=b.formid
                INNER JOIN student s ON s.id=b.studentid
                AND opmerking != ''
                AND werkproces != ''
                GROUP BY 1,2,3
                ORDER BY 1,2,3
                ";
        $result = Yii::$app->db->createCommand($sql)->queryAll();

        $output1 = "";
        foreach($result as $row) {

            $opmerking = str_replace(",[", "\n[",$row['opmerkingen'] ); // the query put a , between the concat, replace it with newline
            $output1 .= $row['werkproces']." ".$row['studentnr']." ".$row['naam']."<br>";
            $output1 .= $opmerking." <br>";
            $sql = "SELECT count(*) cnt FROM ".$db_naam.".printwerkproces
                    WHERE studentnummer=:studentnr AND examenid=:examenid AND werkprocesId=:werkproces";
            $params = array(':examenid'=>$examenid,':studentnr'=>$row['studentnr'],':werkproces'=>$row['werkproces']);
            $result = Yii::$app->db->createCommand($sql)->bindValues($params)->queryAll();

            if ( ! $result[0]['cnt'] ) {
                $output1 .= "INSERT ";
                $sql = "INSERT INTO ".$db_naam.".printwerkproces (examenid, studentnummer, werkprocesId, opmerkingen)
                        VALUES(:examenid, :studentnr, :wekrproces, :opmerking)";
                $params = array(':examenid'=>$examenid,':studentnr'=>$row['studentnr'],':werkproces'=>$row['werkproces'],
                                    ':opmerking'=>$opmerking);
                try {
                    $result = Yii::$app->db->createCommand($sql)->bindValues($params)->execute();
                } catch (\yii\db\Exception $e) {
                    $output1 .= "<span style=\"color: #ff0000\">Warning:</span> failed, unknown werkproces ".$row['werkproces'];
                }
                        
            } else {
                $output1 .= "UPDATE ";
                $sql = "UPDATE ".$db_naam.".printwerkproces SET opmerkingen=:opmerking
                        WHERE studentnummer=:studentnr AND werkprocesId=:werkproces AND examenid=:examenid
                        AND opmerkingen like '[%'";
                $params = array(':examenid'=>$examenid,':studentnr'=>$row['studentnr'],':werkproces'=>$row['werkproces'],
                        ':opmerking'=>$opmerking);
                $result = Yii::$app->db->createCommand($sql)->bindValues($params)->execute();
            }
            $output1.="<hr>";
        }

        $sql="select werkprocesid, studentnummer, opmerkingen from ".$db_naam.".printwerkproces where examenid=:examenid order by 1,2";
        $params = array(':examenid'=>$examenid);
        $result = Yii::$app->db->createCommand($sql)->bindValues($params)->queryAll();
        $output2="";
        foreach($result as $row) {
            $output2 .= $row['werkprocesid']."-".$row['studentnummer']."<br>".$row['opmerkingen']."<hr>";
        }

        // first list show result in target DB and 2nd shows proces log
        $output="Note that comments are updates if the comments are empty or if the comment starts with a '['<br>";
        $output.="<br>Content of target DB (with examenid ".$examenid.") after update:<pre>$output2</pre><h1>Process Log</h1><pre>$output1</pr>";

        return $this->render('query', [
            'output' => $output,
        ]);
    }

    public function actionVrijeRolspelers() {
        // Vrije Rolspers

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
