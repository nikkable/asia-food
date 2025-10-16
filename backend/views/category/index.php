<?php

use repositories\Category\models\Category;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var backend\models\search\CategorySearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Категории';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="category-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Создать категорию', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'parent_id',
            'name',
            'slug',
            [
                'attribute' => 'image',
                'format' => 'raw',
                'value' => function ($model) {
                    if ($model->image) {
                        return Html::img($model->getImageUrl(), [
                            'alt' => Html::encode($model->name),
                            'style' => 'max-width: 50px; max-height: 50px;'
                        ]);
                    }
                    return 'Нет';
                },
                'contentOptions' => ['style' => 'width: 80px; text-align: center;'],
            ],
            [
                'attribute' => 'status',
                'format' => 'raw',
                'value' => function ($model) {
                    $class = $model->status ? 'label-success' : 'label-default';
                    $text = $model->status ? 'Активная' : 'Неактивная';
                    return "<span class='label {$class}'>{$text}</span>";
                },
                'filter' => [
                    1 => 'Активная',
                    0 => 'Неактивная'
                ],
            ],
            [
                'class' => ActionColumn::class,
                'urlCreator' => function ($action, Category $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>


</div>
