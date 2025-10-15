<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var repositories\Category\models\Category[] $categories */
/** @var string $title */
/** @var string $subtitle */
?>

<div class="category">
    <div class="container">
        <div class="category-head">
            <div class="title"><?= Html::encode($title) ?></div>
            <div class="undertitle"><?= Html::encode($subtitle) ?></div>
        </div>
        <div class="category-main">
            <?php foreach ($categories as $category): ?>
                <div class="category-item">
                    <a href="<?= Url::to(['/category/view', 'slug' => $category->slug]) ?>" class="category-item-image">
                        <img src="<?= $category->getImageUrl() ?>" alt="<?= Html::encode($category->name) ?>">
                    </a>
                    <a href="<?= Url::to(['/category/view', 'slug' => $category->slug]) ?>" class="category-item-name">
                        <?= Html::encode($category->name) ?>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
