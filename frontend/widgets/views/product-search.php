<?php

use yii\helpers\Html;

/** @var string $placeholder */
/** @var string $inputClass */
/** @var string $containerClass */
/** @var string $searchUrl */
?>

<div class="product-search js-product-search <?= Html::encode($containerClass) ?>" data-search-url="<?= Html::encode($searchUrl) ?>">
    <div class="product-search-wrapper">
        <input type="text" 
               class="product-search-input js-product-search-input <?= Html::encode($inputClass) ?>"
               placeholder="<?= Html::encode($placeholder) ?>"
               autocomplete="off">
        <button type="button" class="product-search-btn">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M21 21L16.514 16.506L21 21ZM19 10.5C19 15.194 15.194 19 10.5 19C5.806 19 2 15.194 2 10.5C2 5.806 5.806 2 10.5 2C15.194 2 19 5.806 19 10.5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
    </div>
    <div class="search-dropdown" style="display: none;">
        <div class="search-results">
            <!-- Результаты поиска будут добавлены динамически -->
        </div>
        <div class="search-no-results" style="display: none;">
            <p>Ничего не найдено</p>
        </div>
        <div class="search-loading" style="display: none;">
            <p>Поиск...</p>
        </div>
    </div>
</div>
