// Функция для отображения уведомлений
function showNotification(type, message) {
    // Проверяем, существует ли контейнер для уведомлений
    var notificationContainer = $('#notification-container');
    if (notificationContainer.length === 0) {
        // Если контейнера нет, создаем его
        $('body').append('<div id="notification-container" style="position: fixed; top: 20px; right: 20px; left: 10px; z-index: 9999;"></div>');
        notificationContainer = $('#notification-container');
    }
    
    // Создаем уведомление
    var notificationId = 'notification-' + Date.now();
    var notificationClass = type === 'success' ? 'alert-success' : 'alert-danger';
    var notification = $('<div id="' + notificationId + '" class="alert ' + notificationClass + ' alert-dismissible fade show" role="alert" style="min-width: 300px; margin-bottom: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.2);">' +
        message +
        '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
        '</div>');
    
    // Добавляем уведомление в контейнер
    notificationContainer.append(notification);
    
    // Автоматически скрываем уведомление через 3 секунды
    setTimeout(function() {
        $('#' + notificationId).alert('close');
    }, 3000);
}
