$(document).ready(function() {
    // Обработчик события открытия модального окна избранного
    $('#favoriteModal').on('show.bs.modal', function (e) {
        $.ajax({
            url: '/favorite/modal-content',
            type: 'GET',
            dataType: 'html',
            success: function(response) {
                $('#favoriteModal .modal-body').html(response);
            },
            error: function() {
                showNotification('error', 'Произошла ошибка при загрузке избранного');
            }
        });
    });
    // Функция для обновления содержимого модального окна избранного
    function refreshFavoriteModal() {
        // Проверяем, открыто ли модальное окно
        if ($('#favoriteModal').hasClass('show')) {
            $.ajax({
                url: '/favorite/modal-content',
                type: 'GET',
                dataType: 'html',
                success: function(response) {
                    $('#favoriteModal .modal-body').html(response);
                },
                error: function() {
                    showNotification('error', 'Произошла ошибка при обновлении избранного');
                }
            });
        }
    }
    // Функции для работы с избранным
    function updateFavoriteContent(response) {
        if (response.success) {
            // Обновляем счетчик избранного
            $('.favorite-counter').text(response.favoritesCount);
            
            // Если это было удаление товара из модального окна
            if (response.removedProductId) {
                // Обновляем состояние всех кнопок для этого товара на странице
                var pageButtons = $('.add-to-favorite[data-product-id="' + response.removedProductId + '"]');
                pageButtons.removeClass('active').find('span').text('В избранное');
                
                // Удаляем элемент из модального окна
                var item = $('.favorite-item[data-product-id="' + response.removedProductId + '"]');
                item.fadeOut(300, function() {
                    $(this).remove();
                    
                    // Если больше нет товаров, показываем сообщение
                    if (response.favoritesCount === 0) {
                        var emptyFavoriteHtml = '<div class="text-center py-5">' +
                            '<div class="mb-4">' +
                            '<svg width="64" height="64" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="text-muted">' +
                            '<path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>' +
                            '</svg>' +
                            '</div>' +
                            '<h5 class="text-muted">В избранном пока нет товаров</h5>' +
                            '<p class="text-muted">Добавьте товары в избранное, чтобы вернуться к ним позже</p>' +
                            '<button type="button" class="btn btn-primary" data-bs-dismiss="modal">Продолжить покупки</button>' +
                            '</div>';
                        $('#favoriteModal .modal-body').html(emptyFavoriteHtml);
                    }
                });
            }
            
            // Если это было добавление товара
            if (response.productId && !response.removedProductId) {
                var addButtons = $('.add-to-favorite[data-product-id="' + response.productId + '"]');
                addButtons.addClass('active').find('span').text('В избранном');
                
                // Обновляем содержимое модального окна
                refreshFavoriteModal();
            }
        } else {
            alert('Ошибка: ' + (response.message || 'Неизвестная ошибка'));
        }
    }
    
    // Обработка кликов по кнопкам добавления в избранное
    $(document).on('click', '.add-to-favorite', function(e) {
        e.preventDefault();
        var button = $(this);
        var productId = button.data('product-id');
        var action = button.hasClass('active') ? 'remove' : 'add';
        var url = action === 'add' ? '/favorite/add' : '/favorite/remove';
        
        // Блокируем кнопку на время запроса
        button.prop('disabled', true);
        
        $.ajax({
            url: url + '?id=' + productId,
            type: 'POST',
            data: {
                id: productId,
                '_csrf-frontend': $('meta[name="csrf-token"]').attr('content')
            },
            dataType: 'json',
            success: function(response) {
                updateFavoriteContent(response);
                button.prop('disabled', false);
                
                // Показываем уведомление
                showNotification(response.success ? 'success' : 'error', response.message);
            },
            error: function() {
                button.prop('disabled', false);
                showNotification('error', 'Произошла ошибка при обновлении избранного');
            }
        });
    });
    
    // Обработка кликов по кнопкам удаления из избранного в модалке
    $(document).on('click', '.favorite-remove-btn', function() {
        var button = $(this);
        var productId = button.data('product-id');
        
        $.ajax({
            url: '/favorite/remove?id=' + productId,
            type: 'POST',
            data: {
                id: productId,
                '_csrf-frontend': $('meta[name="csrf-token"]').attr('content')
            },
            dataType: 'json',
            success: updateFavoriteContent,
            error: function() {
                showNotification('error', 'Произошла ошибка при удалении товара из избранного');
            }
        });
    });
    
    // Обработка клика по кнопке очистки избранного
    $(document).on('click', '.favorite-clear-btn', function() {
        if (confirm('Вы уверены, что хотите очистить список избранного?')) {
            $.ajax({
                url: '/favorite/clear',
                type: 'POST',
                data: {
                    '_csrf-frontend': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Обновляем счетчик
                        $('.favorite-counter').text('0');
                        
                        // Обновляем содержимое модального окна
                        refreshFavoriteModal();
                        
                        // Обновляем кнопки на странице
                        $('.add-to-favorite.active').removeClass('active').find('span').text('В избранное');
                        
                        showNotification('success', 'Список избранного очищен');
                    } else {
                        showNotification('error', response.message || 'Неизвестная ошибка');
                    }
                },
                error: function() {
                    showNotification('error', 'Произошла ошибка при очистке избранного');
                }
            });
        }
    });
    
    // Добавление товара в корзину из модалки избранного
    $(document).on('click', '.favorite-item .add-to-cart-btn', function() {
        var button = $(this);
        var productId = button.data('product-id');
        var productName = button.data('product-name');
        
        // Сохраняем оригинальный текст кнопки
        var originalText = button.html();
        
        // Меняем текст и блокируем кнопку
        button.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Добавляем...').prop('disabled', true);
        
        $.ajax({
            url: '/cart/add?id=' + productId,
            type: 'POST',
            data: {
                quantity: 1,
                '_csrf-frontend': $('meta[name="csrf-token"]').attr('content')
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Обновляем счетчик корзины
                    $('.cart-counter').text(response.cartAmount);
                    
                    // Меняем текст кнопки на успех
                    button.html('<i class="fas fa-check"></i> Добавлено').removeClass('btn-primary').addClass('btn-success');
                    
                    // Показываем уведомление
                    showNotification('success', 'Товар "' + productName + '" добавлен в корзину');
                    
                    // Через 2 секунды возвращаем оригинальный текст и стиль
                    setTimeout(function() {
                        button.html(originalText).removeClass('btn-success').addClass('btn-primary').prop('disabled', false);
                    }, 2000);
                } else {
                    // Возвращаем оригинальный текст
                    button.html(originalText).prop('disabled', false);
                    
                    // Показываем уведомление об ошибке
                    showNotification('error', response.message || 'Ошибка при добавлении товара в корзину');
                }
            },
            error: function() {
                // Возвращаем оригинальный текст
                button.html(originalText).prop('disabled', false);
                
                // Показываем уведомление об ошибке
                showNotification('error', 'Произошла ошибка при добавлении товара в корзину');
            }
        });
    });
});
