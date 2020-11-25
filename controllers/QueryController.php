<?php

namespace app\controllers;

use Yii;
use app\models\Beoordeling;
use app\models\BeoordelingSearch;
use app\models\Vraag;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use yii\filters\AccessControl;

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

    private function getExamenId() {
        $sql = "select otherid from examen where actief=1";
        $examenid = Yii::$app->db->createCommand($sql)->queryOne();

        if ($examenid) {
            return $examenid['otherid'];
        } else {
            return 0;
        }
    }

    public function actionExportResults() {

        $db_naam="beoordeling";

        $examenid=$this->getExamenId();

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
        $output = "Import to examenid ".$examenid."<br>";;
        $output .= "<pre>";
        foreach($result as $row) {
            $totVraag = $row['vraagnr'] + $row['aantal'] -1;
            $score = max( intval( ($row['score']+5)/10 ), 0); // score is rounden and is minimal 0, this for crucial questions that have a coutn of -99
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

        $examenid=$this->getExamenId();

        $sql="  SELECT werkproces, s.naam naam, s.nummer studentnr, GROUP_CONCAT(CONCAT('[',f.omschrijving, ']: ', opmerking)) opmerkingen
                FROM beoordeling b
                INNER JOIN form f ON f.id=b.formid
                INNER JOIN student s ON s.id=b.studentid
                INNER JOIN examen e ON e.id=f.examenid
                AND e.actief = 1
                AND f.actief = 1
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
                        VALUES(:examenid, :studentnr, :werkproces, :opmerking)";
                $params = array(':examenid'=>$examenid,':studentnr'=>$row['studentnr'],':werkproces'=>$row['werkproces'],
                                    ':opmerking'=>$opmerking);
                try {
                    $result = Yii::$app->db->createCommand($sql)->bindValues($params)->execute();
                } catch (\yii\db\Exception $e) { // TODO, only works in debug mode?
                    $output1 .= $e."<br>";
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

        $sql="select werkprocesid, studentnummer, opmerkingen
                from ".$db_naam.".printwerkproces where examenid=:examenid order by 1,2";
        $params = array(':examenid'=>$examenid);
        $result = Yii::$app->db->createCommand($sql)->bindValues($params)->queryAll();
        $output2="";
        foreach($result as $row) {
            $output2 .= $row['werkprocesid']."-".$row['studentnummer']."<br>".$row['opmerkingen']."<hr>";
        }

        // first list show result in target DB and 2nd shows proces log
        $output="";
        $output.="<h1>Process Log</h1>";
        $output.="Note that comments are updates if the comments are empty or if the comment starts with a '['<br>";
        $output.="<pre>$output1</pre>";
        $output.="<h1>Updated Content</h1>Content of target DB (with examenid ".$examenid." for all foms) after update:<pre>$output2</pre>";

        return $this->render('query', [
            'output' => $output,
        ]);
    }

    public function actionRecalcScores() {
        // De resultaten worden opgslagen met formid en volgnummer, het volgnummer verwijst naar hetzelfde volgnummer van de vragen die aan het form hangen.
        // De numemring is darbij niet belangrijk, het gaat om de volgorde.
        // Als de volgorde of weging (punten) van de vragen wijzigt dan kan de results tabel worden ge-update. Dit script anayseert en resutlaart in een
        // aantal update statements die met de hand moeten worden uitgevoerd omeen nieuwe vragenlijst opnieuw in de reeds bestaande resutlaten te verwerken.
        // Dit is crappy en dit zou moeten worden opgelost door de antwoorden (ja, soms, nee) uit de vragen tabel te normaliseren en dan in de resultaten tabel de
        // vraagid mee te nemen.
        // Dit is (nog) niet gedaan omdat het formulier neit 'weet' wat de vraag id's zijn, het weet alleen de volgorde van de radi buttons.
        // Oplossing is om de vraagid's direct na het posten op basis van de (dan geldige) volgorde in te vullen. De id's moetne dan NULL mogen zijn.
        // Stap 1: vraag tabel krijgt FK naar antwoorden. Elke vraag krijg twee of drie antwoorden (id, vraagid, antwoord (1,2,3; ja, soms, nee) )
        // Stap 2: vraag kolommen ja, nee en soms verwijderen en alle queries en model in code aanpassen
        // Stap 3: in de post form, een query toevoegen die de id's in de restualten tabel opneemt
        // Stap 4: de resulaten kunnen in de results tabel blijven maar kunnen nu eenvoudig worden doorberekend
        // Veel werk, veel risco en het levert weinig op zeker als weging en dergelijke nieet vaak wordne aangepast tijdens het nakijken.
        // when scores are changed, you can recalc the socres for already finshed forms
        $vragen=Vraag::find()->joinWith('form')->where(['actief'=>1])->orderBy('formid ASC, volgnr ASC')->all();

        $output="<pre>";
        $prevFormid='-1';
        $teller=1;

        foreach($vragen as $row) {

            if ( $row['formid'] != $prevFormid ) {
                $teller=1;
            }
            
            if ( $row['volgnr'] != $teller ) {
                $output .= "<br>WARNING: teller en volgnr out of sync ".$teller." : ".$row['volgnr'];
                $output .= ", formid: ".$row['formid'].", vraag:".$row['vraag']."<br>";
            }

            if (isset( $row['ja']) ) {
                $sql = "select * from results where formid=:formid and vraagnr=:vraagnr and antwoordnr=1 and score!=:score";
                $params = array(':formid'=>$row['formid'], ':vraagnr'=>$teller, ':score'=>$row['ja']);
                $result = Yii::$app->db->createCommand($sql)->bindValues($params)->queryAll();
                if (count($result)) {
                    $output .= "<hr>(JA) Form: ".$row['formid'].", vraagnr: ".$row['volgnr'].", teller: ".$teller.", vraag: ".$row['vraag']."<br>ja: ".$row['ja'];
                    $output .= "<br>";
                    $line=$result[0];
                    $output .= "Wrong number of results: ".count($result)."<br>";
                    $output .= "Result: ".$line['id'].", vraagnr: ".$line['vraagnr'].", antwoordnr:".$line['antwoordnr'].", score: ".$line['score'];
                    $output .= "<br>";
                    $output .= "update results set score=".$row['ja']." where formid=".$row['formid']." and vraagnr=".$row['volgnr']." and antwoordnr=1";
                    $output .= "<br>";
                }
            }
            if (isset( $row['soms']) ) {
                if (isset( $row['ja']) ) {
                    $sql = "select * from results where formid=:formid and vraagnr=:vraagnr and antwoordnr=2 and score!=:score";
                    $params = array(':formid'=>$row['formid'], ':vraagnr'=>$teller, ':score'=>$row['soms']);
                    $result = Yii::$app->db->createCommand($sql)->bindValues($params)->queryAll();
                    if (count($result)) {
                        $output .= "<hr>(SOMS) orm: ".$row['formid'].", vraagnr: ".$teller.", vraag: ".$row['vraag']."<br>soms: ".$row['soms'];
                        $output .= "<br>";
                        $line=$result[0];
                        $output .= "Wrong number of results: ".count($result)."<br>";
                        $output .= "Result: ".$line['id'].", vraagnr: ".$line['vraagnr'].", antwoordnr:".$line['antwoordnr'].", score: ".$line['score'];
                        $output.="<br>";
                        $output .= "update results set score=".$row['soms']." where formid=".$row['formid']." and vraagnr=".$row['volgnr']." and antwoordnr=2";
                        $output .= "<br>";
                    }
                }
            }
            if (isset( $row['nee']) ) {
                if (isset( $row['ja']) ) {
                    $sql = "select * from results where formid=:formid and vraagnr=:vraagnr and antwoordnr=3 and score!=:score";
                    $params = array(':formid'=>$row['formid'], ':vraagnr'=>$teller, ':score'=>$row['nee']);
                    $result = Yii::$app->db->createCommand($sql)->bindValues($params)->queryAll();
                    if (count($result)) {
                        $output .= "<hr>(NEE) Form: ".$row['formid'].", vraagnr: ".$teller.", vraag: ".$row['vraag']."<br>nee: ".$row['nee'];
                        $output .= "<br>";
                        $line=$result[0];
                        $output .= "Wrong number of results: ".count($result)."<br>";
                        $output .= "Result: ".$line['id'].", vraagnr: ".$line['vraagnr'].", antwoordnr:".$line['antwoordnr'].", score: ".$line['score'];
                        $output.="<br>";
                        $output .= "update results set score=".$row['nee']." where formid=".$row['formid']." and vraagnr=".$row['volgnr']." and antwoordnr=3";
                        $output .= "<br>";
                    }
                }
            }

            $teller++;
            $prevFormid = $row['formid'];
        }
        return $this->render('query', [
            'output' => $output,
        ]);

    }

    private function executeQuery($sql, $title="no title") {
        $result = Yii::$app->db->createCommand($sql)->queryAll();

        $data['title']=$title;;

        if ($result) { // column names are derived from query results
            $data['col']=array_keys($result[0]);
        }
        $data['row']=$result;

        return $data;

    }

    public function actionVrijeRolspelers() {
        // Vrije Rolspers

        $sql="  select naam
                from rolspeler r where
                actief = 1 AND id  not in (
                select rolspelerid from gesprek g
                where r.id=g.rolspelerid
                and status <2 )
                order by r.naam
            ";

        return $this->render('output', [
            'data' => $this->executeQuery($sql, "Rolspelers zonder gesprek"),
        ]);
    }

    public function actionRolspelerBelasting() {

        $sql="  select r.naam naam, count(*) aantal
                from gesprek g 
                inner join rolspeler r on r.id=g.rolspelerid
                inner join form f on f.id=g.formid
                inner join examen e on e.id=f.examenid
                where e.actief = 1 
                group by 1
                order by 1
            ";

        return $this->render('output', [
            'data' => $this->executeQuery($sql,"Aantal beoordelingen per rolspeler"),
        ]);
    }

    public function actionPunten() {
        $sql="
            SELECT s.naam naam, f.omschrijving onderdeel, greatest(sum(r.score),0) score
            FROM results r
            INNER JOIN student s ON s.id=r.studentid
            INNER JOIN form f ON f.id=r.formid
            INNER JOIN examen e ON e.id=f.examenid
            WHERE e.actief=1
            GROUP BY 1,2
            ORDER BY 1,2
        ";

        return $this->render('output', [
            'data' => $this->executeQuery($sql, "Socre per student per onderdeel"),
        ]);
    }

    public function actionGezakt() {
        $sql="
            select naam, count(onderdeel) onderdelen from (
            SELECT s.naam naam, f.omschrijving onderdeel
            FROM results r
            INNER JOIN student s ON s.id=r.studentid
            INNER JOIN form f ON f.id=r.formid
            INNER JOIN examen e ON e.id=f.examenid
            WHERE e.actief=1
            GROUP BY 1,2
            HAVING greatest(sum(r.score),0)=1000
            ORDER BY 1,2
            ) as subquery
            group by 1
            ";

        return $this->render('output', [
            'data' => $this->executeQuery($sql, "Gezakt op cruciale criteria per student aantal onderdelen"),
        ]);
    }

    public function actionGesprekkenPerKandidaat()
    {
        $sql="
            SELECT s.naam, s.id, s.nummer, count(*) gesprekken
            FROM student s
            INNER JOIN gesprek g
            ON g.studentid=s.id
            INNER JOIN form f
            ON f.id=g.formid
            INNER JOIN examen e
            ON e.id=f.examenid
            WHERE g.status=2
            AND e.actief=1
            GROUP BY s.naam, s.id, s.nummer
            ORDER BY gesprekken, s.naam
        ";

        return $this->render('output', [
            'data' => $this->executeQuery($sql, "Gesprekken per kandidaat"),
        ]);
    }


}
