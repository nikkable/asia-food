<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var repositories\Category\models\Category[] $categories */
/** @var string|null $currentCategorySlug */
?>

<?php foreach ($categories as $category): ?>
    <li<?= $currentCategorySlug === $category->slug ? ' class="active"' : '' ?>>
        <?= Html::a(Html::encode($category->name), ['category/view', 'slug' => $category->slug]) ?>
    </li>
<?php endforeach; ?>
