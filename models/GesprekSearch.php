<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Gesprek;

/**
 * GesprekSearch represents the model behind the search form of `app\models\Gesprek`.
 */
class GesprekSearch extends Gesprek
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'formid', 'rolspelerid', 'studentid', 'status'], 'integer'],
            [['opmerking'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Gesprek::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['id' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'formid' => $this->formid,
            'rolspelerid' => $this->rolspelerid,
            'studentid' => $this->studentid,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'opmerking', $this->opmerking]);

        return $dataProvider;
    }
}
