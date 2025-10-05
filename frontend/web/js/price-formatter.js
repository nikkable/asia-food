/**
 * Функции для форматирования цен
 */

/**
 * Форматирует цену в рублях
 * @param {number} price - Цена для форматирования
 * @returns {string} Отформатированная цена с символом рубля
 */
function formatPrice(price) {
    return new Intl.NumberFormat('ru-RU', {
        style: 'currency',
        currency: 'RUB',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(price);
}

/**
 * Форматирует цену без символа валюты
 * @param {number} price - Цена для форматирования
 * @returns {string} Отформатированная цена без символа валюты
 */
function formatPriceWithoutCurrency(price) {
    return new Intl.NumberFormat('ru-RU', {
        style: 'decimal',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(price);
}
