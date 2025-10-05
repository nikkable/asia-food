<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var \repositories\Favorite\models\FavoriteList $favorites */
?>

<!-- Кнопка избранного -->
<button type="button" class="btn btn-outline-danger favorite-button me-2" data-bs-toggle="modal" data-bs-target="#favoriteModal">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
    <span class="favorite-counter ms-1">
        <?= $favorites->getCount() ?>
    </span>
</button>

<!-- Модалка избранного -->
<div class="modal fade" id="favoriteModal" tabindex="-1" aria-labelledby="favoriteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="favoriteModalLabel">Избранное</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>

            <div class="modal-body">
                <!-- Содержимое будет загружено через AJAX -->
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Загрузка...</span>
                    </div>
                    <p class="mt-3">Загрузка избранных товаров...</p>
                </div>
            </div>
        </div>
    </div>
</div>
