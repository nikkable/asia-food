<?php

namespace backend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use repositories\Category\models\Category;

/**
 * CategorySearch represents the model behind the search form of `repositories\Category\models\Category`.
 */
class CategorySearch extends Category
{
    public function rules(): array
    {
        return [
            [['id', 'parent_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['name', 'slug', 'description', 'image'], 'safe'],
        ];
    }

    public function scenarios(): array
    {
        return Model::scenarios();
    }

    public function search(array $params, string $formName = null): ActiveDataProvider
    {
        $query = Category::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params, $formName);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'parent_id' => $this->parent_id,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'slug', $this->slug])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'image', $this->image]);

        return $dataProvider;
    }
}
