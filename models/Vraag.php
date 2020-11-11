<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "vraag".
 *
 * @property int $id
 * @property int $formid
 * @property int $volgnr
 * @property string $vraag
 * @property int|null $ja
 * @property int|null $soms
 * @property int|null $nee
 *
 * @property Form $form
 */
class Vraag extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'vraag';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['formid', 'volgnr', 'vraag'], 'required'],
            [['formid', 'volgnr', 'ja', 'soms', 'nee','mappingid'], 'integer'],
            [['vraag'], 'string', 'max' => 200],
            [['formid'], 'exist', 'skipOnError' => true, 'targetClass' => Form::className(), 'targetAttribute' => ['formid' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'formid' => 'Formid',
            'volgnr' => 'Volgnr',
            'vraag' => 'Vraag',
            'ja' => 'Ja',
            'soms' => 'Soms',
            'nee' => 'Nee',
            'mappingid' => 'mappingid',
        ];
    }

    /**
     * Gets query for [[Form]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getForm()
    {
        return $this->hasOne(Form::className(), ['id' => 'formid']);
    }
}
