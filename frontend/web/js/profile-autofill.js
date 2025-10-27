/**
 * Скрипт для автоматического заполнения форм данными пользователя
 */
$(document).ready(function() {
    // Маска для телефона
    if (typeof $.fn.mask !== 'undefined') {
        $('input[name="QuickOrderForm[customerPhone]"], input[name="User[phone]"]').mask('+7 (999) 999-99-99', {
            placeholder: '+7 (___) ___-__-__'
        });
    }
    
    // Подсветка автозаполненных полей
    function highlightAutofilledFields() {
        $('.field-text, .form-control-custom').each(function() {
            var $field = $(this);
            if ($field.val() && $field.val().trim() !== '') {
                $field.addClass('autofilled');
                
                // Убираем подсветку через 3 секунды
                setTimeout(function() {
                    $field.removeClass('autofilled');
                }, 3000);
            }
        });
    }
    
    // Применяем подсветку при загрузке модального окна корзины
    $('#cartModal').on('shown.bs.modal', function() {
        setTimeout(highlightAutofilledFields, 500);
    });
    
    // Применяем подсветку на странице редактирования профиля
    if ($('.profile-form').length) {
        setTimeout(highlightAutofilledFields, 500);
    }
    
    // Уведомление о незаполненном профиле
    function showProfileIncompleteNotice() {
        if (!$('.alert-profile-incomplete').length && $('.quick-order-form').length) {
            var hasEmptyFields = false;
            var requiredFields = ['input[name="QuickOrderForm[customerName]"]', 'input[name="QuickOrderForm[customerPhone]"]'];
            
            $.each(requiredFields, function(index, selector) {
                if (!$(selector).val() || $(selector).val().trim() === '') {
                    hasEmptyFields = true;
                    return false;
                }
            });
            
            if (hasEmptyFields && typeof isUserGuest !== 'undefined' && !isUserGuest) {
                var notice = $('<div class="alert alert-warning alert-profile-incomplete mb-3">' +
                    '<i class="fas fa-exclamation-triangle"></i> ' +
                    'Заполните профиль для быстрого оформления заказов. ' +
                    '<a href="/profile/edit" class="alert-link">Перейти в профиль</a>' +
                    '</div>');
                
                $('.quick-order-form').prepend(notice);
            }
        }
    }
    
    // Проверяем незаполненный профиль при открытии корзины
    $('#cartModal').on('shown.bs.modal', function() {
        setTimeout(showProfileIncompleteNotice, 1000);
    });
    
    // Валидация телефона в реальном времени
    $('input[name="QuickOrderForm[customerPhone]"], input[name="User[phone]"]').on('input blur', function() {
        var $field = $(this);
        var phone = $field.val().replace(/\D/g, '');
        var $feedback = $field.siblings('.invalid-feedback');
        
        if (phone.length > 0 && phone.length < 11) {
            $field.addClass('is-invalid');
            if (!$feedback.length) {
                $field.after('<div class="invalid-feedback">Введите полный номер телефона</div>');
            }
        } else {
            $field.removeClass('is-invalid');
            $feedback.remove();
        }
    });
    
    // Автоматическое заполнение email при вводе
    $('input[name="QuickOrderForm[customerEmail]"], input[name="User[email]"]').on('blur', function() {
        var $field = $(this);
        var email = $field.val().trim();
        
        if (email && !email.includes('@')) {
            // Предлагаем популярные домены
            var suggestions = ['@gmail.com', '@yandex.ru', '@mail.ru', '@outlook.com'];
            var suggestion = email + suggestions[0];
            
            if (confirm('Возможно, вы имели в виду: ' + suggestion + '?')) {
                $field.val(suggestion);
            }
        }
    });
});

// CSS стили для автозаполненных полей
if (!$('#autofill-styles').length) {
    $('<style id="autofill-styles">' +
        '.field-text.autofilled, .form-control-custom.autofilled {' +
            'border-color: #28a745 !important;' +
            'box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25) !important;' +
            'transition: all 0.3s ease;' +
        '}' +
        '.alert-profile-incomplete {' +
            'border-left: 4px solid #ffc107;' +
        '}' +
    '</style>').appendTo('head');
}
