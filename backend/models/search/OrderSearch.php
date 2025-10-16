<?php

namespace backend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use repositories\Order\models\Order;

/**
 * OrderSearch represents the model behind the search form of `repositories\Order\models\Order`.
 */
class OrderSearch extends Order
{
    public function rules(): array
    {
        return [
            [['id', 'user_id', 'status', 'payment_status', 'created_at', 'updated_at'], 'integer'],
            [['customer_name', 'customer_email', 'customer_phone', 'note', 'payment_method', 'payment_transaction_id'], 'safe'],
            [['total_cost'], 'number'],
        ];
    }

    public function scenarios(): array
    {
        return Model::scenarios();
    }

    public function search(array $params, string $formName = null): ActiveDataProvider
    {
        $query = Order::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params, $formName);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'user_id' => $this->user_id,
            'total_cost' => $this->total_cost,
            'status' => $this->status,
            'payment_status' => $this->payment_status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'customer_name', $this->customer_name])
            ->andFilterWhere(['like', 'customer_email', $this->customer_email])
            ->andFilterWhere(['like', 'customer_phone', $this->customer_phone])
            ->andFilterWhere(['like', 'note', $this->note])
            ->andFilterWhere(['like', 'payment_method', $this->payment_method])
            ->andFilterWhere(['like', 'payment_transaction_id', $this->payment_transaction_id]);

        return $dataProvider;
    }
}
