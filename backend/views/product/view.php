<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var repositories\Product\models\Product $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Товары', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="product-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Изменить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы действительно хотите удалить товар?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute' => 'category_id',
                'value' => function ($model) {
                    return $model->category ? $model->category->name : 'Не указана';
                },
            ],
            'name',
            'description:ntext',
            [
                'attribute' => 'image',
                'format' => 'raw',
                'value' => function ($model) {
                    if ($model->image) {
                        return Html::img($model->getImageUrl(), [
                            'alt' => Html::encode($model->name),
                            'style' => 'max-width: 300px; max-height: 300px;'
                        ]);
                    }
                    return 'Нет изображения';
                },
            ],
            'price:currency',
            'price_discount:currency',
            'quantity',
            'article',
            [
                'attribute' => 'status',
                'value' => function ($model) {
                    return $model->status ? 'Активный' : 'Неактивный';
                },
            ],
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>

</div>
