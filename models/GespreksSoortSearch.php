<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\GespreksSoort;

/**
 * GespreksSoortSearch represents the model behind the search form of `app\models\GespreksSoort`.
 */
class GespreksSoortSearch extends GespreksSoort
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'examenid', 'volgnummer', 'naam'], 'integer'],
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
        $query = GespreksSoort::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
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
            'examenid' => $this->examenid,
            'volgnummer' => $this->volgnummer,
            'naam' => $this->naam,
        ]);

        return $dataProvider;
    }
}
