$(document).ready(function() {
    // Функция для обновления содержимого модального окна корзины
    function refreshCartModal() {
        // Проверяем, открыто ли модальное окно
        if ($('#cartModal').hasClass('show')) {
            $.ajax({
                url: '/cart/modal-content',
                type: 'GET',
                dataType: 'html',
                success: function(response) {
                    $('#cartModal .modal-body').html(response);
                },
                error: function() {
                    showNotification('error', 'Произошла ошибка при обновлении корзины');
                }
            });
        }
    }

    // Обработчик события открытия модального окна корзины
    $('#cartModal').on('show.bs.modal', function (e) {
        $.ajax({
            url: '/cart/modal-content',
            type: 'GET',
            dataType: 'html',
            success: function(response) {
                $('#cartModal .modal-body').html(response);
            },
            error: function() {
                showNotification('error', 'Произошла ошибка при загрузке корзины');
            }
        });
    });

    // Функция для обновления содержимого корзины без перезагрузки
    function updateCartContent(response) {
        if (response.success) {
            // Обновляем счетчик товаров
            $('.js-cart-counter').text(response.cartAmount);
            
            // Обновляем общую сумму
            if ($('.cart-total strong').length) {
                $('.cart-total strong').text(new Intl.NumberFormat('ru-RU').format(response.cartTotal) + 'р.');
            }
            
            // Если это было изменение количества, обновляем отображение количества
            if (response.updatedProductId) {
                var item = $('.cart-item[data-product-id="' + response.updatedProductId + '"]');
                item.find('.btn.disabled').text(response.newQuantity);
                var priceElement = item.find('.cart-item-total');
                if (priceElement.length) {
                    priceElement.text('= ' + new Intl.NumberFormat('ru-RU').format(response.itemTotal) + 'р.');
                }
            }
            
            // Обновляем кнопку оформления заказа
            if ($('.btn-success').length) {
                $('.btn-success').text('Оформить заказ на ' + new Intl.NumberFormat('ru-RU').format(response.cartTotal) + 'р.');
            }
            
            // Если товар был удален, обновляем модальное окно
            if (response.removedProductId) {
                refreshCartModal();
            }
        } else {
            alert('Ошибка: ' + (response.message || 'Неизвестная ошибка'));
        }
    }

    // Обработка кликов по кнопкам изменения количества
    $(document).on('click', '.cart-quantity-btn', function() {
        var button = $(this);
        var action = button.data('action');
        var productId = button.data('product-id');

        $.ajax({
            url: '/cart/update-quantity',
            type: 'POST',
            data: {
                productId: productId,
                action: action,
                '_csrf-frontend': $('meta[name="csrf-token"]').attr('content')
            },
            dataType: 'json',
            success: function(response) {
                updateCartContent(response);
                
                // Если товар был удален, обновляем модальное окно
                if (response.removedProductId) {
                    refreshCartModal();
                }
            },
            error: function() {
                showNotification('error', 'Произошла ошибка при обновлении количества');
            }
        });
    });

    // Обработка кликов по кнопкам удаления
    $(document).on('click', '.cart-remove-btn', function() {
        var button = $(this);
        var productId = button.data('product-id');

        if (confirm('Вы уверены, что хотите удалить этот товар из корзины?')) {
            $.ajax({
                url: '/cart/remove?id=' + productId,
                type: 'POST',
                data: {
                    '_csrf-frontend': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Обновляем счетчик корзины
                        $('.js-cart-counter').text(response.cartAmount);
                        
                        // Обновляем модальное окно
                        refreshCartModal();
                        
                        showNotification('success', 'Товар удален из корзины');
                    } else {
                        showNotification('error', response.message || 'Неизвестная ошибка');
                    }
                },
                error: function() {
                    showNotification('error', 'Произошла ошибка при удалении товара');
                }
            });
        }
    });

    // Обработка формы быстрого заказа
    $(document).on('submit', '#quick-order-form', function(e) {
        e.preventDefault();

        var form = $(this);
        var formData = form.serialize();

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Закрываем модалку
                    $('#cartModal').modal('hide');
                    // Показываем уведомление об успехе
                    showNotification('success', 'Заказ успешно оформлен!');
                    // Очищаем корзину и перезагружаем страницу
                    location.reload();
                } else {
                    showNotification('error', 'Ошибка при оформлении заказа: ' + (response.message || 'Неизвестная ошибка'));
                }
            },
            error: function() {
                showNotification('error', 'Произошла ошибка при оформлении заказа');
            }
        });

        return false;
    });

    // Обновление корзины при добавлении товара (для обновления счетчика)
    $(document).on('cartUpdated', function(event, data) {
        $('.js-cart-counter').text(data.cartAmount);
        
        // Обновляем модальное окно, если оно открыто
        refreshCartModal();
    });
});
