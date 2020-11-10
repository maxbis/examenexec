<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "Form".
 *
 * @property int $id
 * @property string|null $omschrijving
 * @property int $nr
 * @property int $examenid
 */
class Form extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'form';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nr'], 'required'],
            [['nr', 'examenid', 'actief'], 'integer'],
            [['instructie'], 'string'],
            [['omschrijving'], 'string', 'max' => 600],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'omschrijving' => 'Omschrijving',
            'nr' => 'Nr',
            'examenid' => 'Examenid',
            'instructie' => 'Instructie',
        ];
    }
    public function getExamen()
    {
        return $this->hasOne(Examen::className(), ['id' => 'examenid']);
    }
}
