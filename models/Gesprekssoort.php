<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "gesprekssoort".
 *
 * @property int $id
 * @property int $examenid
 * @property int $volgnummer
 * @property int $naam
 *
 * @property Beoordeling[] $beoordelings
 * @property Gesprek[] $gespreks
 * @property Examen $examen
 */
class Gesprekssoort extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'gesprekssoort';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['examenid', 'volgnummer', 'naam'], 'required'],
            [['examenid', 'volgnummer'], 'integer'],
            [['naam'], 'string', 'max' => 40],
            [['examenid'], 'exist', 'skipOnError' => true, 'targetClass' => Examen::className(), 'targetAttribute' => ['examenid' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'examenid' => 'Examenid',
            'volgnummer' => 'Volgnummer',
            'naam' => 'Naam',
        ];
    }

    /**
     * Gets query for [[Beoordelings]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBeoordelings()
    {
        return $this->hasMany(Beoordeling::className(), ['gessprekssoortid' => 'id']);
    }

    /**
     * Gets query for [[Gespreks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGespreks()
    {
        return $this->hasMany(Gesprek::className(), ['gesprekssoortid' => 'id']);
    }

    /**
     * Gets query for [[Examen]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExamen()
    {
        return $this->hasOne(Examen::className(), ['id' => 'examenid']);
    }
}
