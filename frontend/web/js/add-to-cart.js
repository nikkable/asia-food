/**
 * Обработчик добавления товаров в корзину
 */
$(document).ready(function() {
    // Обработка кликов по кнопкам добавления в корзину
    $(document).on('click', '.add-to-cart-btn', function(e) {
        e.preventDefault();
        
        var button = $(this);
        var productId = button.data('product-id');
        var productName = button.data('product-name');
        var originalText = button.text();
        
        // Блокируем кнопку на время запроса
        button.prop('disabled', true).text('Добавляем...');
        
        $.ajax({
            url: '/cart/add' + '?id=' + productId,
            type: 'POST',
            data: {
                quantity: 1,
                '_csrf-frontend': $('meta[name="csrf-token"]').attr('content')
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Показываем успешное сообщение
                    button.text('Добавлено!').removeClass('btn-three').addClass('btn-success');
                    
                    // Обновляем счетчик корзины если есть
                    if ($('.js-cart-counter').length) {
                        $('.js-cart-counter').text(response.cartAmount);
                    }
                    
                    // Показываем уведомление с правильным порядком параметров
                    showNotification('success', 'Товар "' + productName + '" добавлен в корзину');
                    
                    // Возвращаем кнопку в исходное состояние через 2 секунды
                    setTimeout(function() {
                        button.text(originalText).removeClass('btn-success').addClass('btn-three').prop('disabled', false);
                    }, 2000);
                } else {
                    button.text(originalText).prop('disabled', false);
                    showNotification('error', response.message || 'Ошибка при добавлении товара');
                }
            },
            error: function() {
                button.text(originalText).prop('disabled', false);
                showNotification('error', 'Произошла ошибка. Попробуйте еще раз.');
            }
        });
    });
});
