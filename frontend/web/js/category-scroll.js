/**
 * Автоматическое центрирование активной категории в горизонтальном скролле
 * на мобильных устройствах (max-width: 991px)
 */
(function() {
    'use strict';

    /**
     * Центрирует активный элемент в горизонтальном скролле
     */
    function centerActiveCategory() {
        // Проверяем разрешение экрана
        if (window.innerWidth > 991) {
            return;
        }

        const categoriesContainer = document.querySelector('.category-page-categories ul');
        if (!categoriesContainer) {
            return;
        }

        const activeItem = categoriesContainer.querySelector('li.active');
        if (!activeItem) {
            return;
        }

        // Вычисляем позицию для центрирования
        const containerWidth = categoriesContainer.offsetWidth;
        const itemLeft = activeItem.offsetLeft;
        const itemWidth = activeItem.offsetWidth;
        
        // Центрируем элемент: позиция элемента - половина контейнера + половина элемента
        const scrollPosition = itemLeft - (containerWidth / 2) + (itemWidth / 2);

        // Плавная прокрутка к активному элементу
        categoriesContainer.scrollTo({
            left: Math.max(0, scrollPosition),
            behavior: 'smooth'
        });
    }

    // Центрируем при загрузке страницы
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', centerActiveCategory);
    } else {
        centerActiveCategory();
    }

    // Центрируем при изменении размера окна (если пользователь повернул устройство)
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(centerActiveCategory, 250);
    });
})();
