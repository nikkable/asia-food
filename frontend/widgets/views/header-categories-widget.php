<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var repositories\Category\models\Category[] $categories */
/** @var string|null $currentCategorySlug */
?>

<?php foreach ($categories as $category): ?>
    <li<?= $currentCategorySlug === $category->slug ? ' class="active"' : '' ?>>
        <a href="<?= Url::to(['/category/view', 'slug' => $category->slug]) ?>">
            <?= Html::encode($category->name) ?>
        </a>
    </li>
<?php endforeach; ?>
