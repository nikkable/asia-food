<?php
/** @var yii\web\View $this */
/** @var array $categories */

use yii\helpers\Html;

$this->title = 'Каталог';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container">
    <div class="site-catalog-index">
        <h1><?= Html::encode($this->title) ?></h1>

        <div class="list-group">
            <?php foreach ($categories as $category): ?>
                <?= Html::a(Html::encode($category->name), ['/catalog/category', 'slug' => $category->slug], ['class' => 'list-group-item list-group-item-action']) ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>

