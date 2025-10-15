<?php

/** @var FavoriteList $favorites */

use repositories\Favorite\models\FavoriteList;

?>

<!-- Кнопка избранного -->
<button type="button" class="favorite-button" data-bs-toggle="modal" data-bs-target="#favoriteModal">
    <svg role="img" width="41" height="35" viewBox="0 0 41 35" fill="none" xmlns="http://www.w3.org/2000/svg" class="t1002__wishlisticon-img">
        <path d="M39.9516 11.9535C39.9516 22.5416 20.4993 33.8088 20.4993 33.8088C20.4993 33.8088 1.04688 22.5416 1.04688 11.9535C1.04688 -2.42254 20.4993 -2.2284 20.4993 10.4239C20.4993 -2.2284 39.9516 -2.03914 39.9516 11.9535Z" stroke-width="1.5" stroke-linejoin="round"></path>
    </svg>
    <span class="favorite-button-counter js-favorite-counter">
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
