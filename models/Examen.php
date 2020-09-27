<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "examen".
 *
 * @property int $id
 * @property string $naam
 * @property int $actief
 * @property string|null $datum_start
 * @property string|null $datum_eind
 *
 * @property Gesprekssoort[] $gesprekssoorts
 */
class Examen extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'examen';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['naam'], 'required'],
            [['actief'], 'integer'],
            [['datum_start', 'datum_eind'], 'safe'],
            [['naam'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'naam' => 'Naam',
            'actief' => 'Actief',
            'datum_start' => 'Datum Start',
            'datum_eind' => 'Datum Eind',
        ];
    }

    /**
     * Gets query for [[Gesprekssoorts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGesprekssoorts()
    {
        return $this->hasMany(Gesprekssoort::className(), ['examenid' => 'id']);
    }
}
