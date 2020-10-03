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
        return 'Form';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nr'], 'required'],
            [['nr', 'examenid'], 'integer'],
            [['omschrijving'], 'string', 'max' => 350],
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
        ];
    }
}
