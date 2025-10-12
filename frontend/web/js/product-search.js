/**
 * Виджет поиска товаров с автодополнением
 */
class ProductSearchWidget {
    constructor(container) {
        this.container = container;
        this.input = container.querySelector('.js-product-search-input');
        this.dropdown = container.querySelector('.search-dropdown');
        this.resultsContainer = container.querySelector('.search-results');
        this.noResultsContainer = container.querySelector('.search-no-results');
        this.loadingContainer = container.querySelector('.search-loading');
        this.submitBtn = container.querySelector('.search-submit-btn');
        
        this.searchUrl = container.dataset.searchUrl;
        this.debounceTimeout = null;
        this.currentRequest = null;
        
        this.bindEvents();
    }
    
    bindEvents() {
        // Поиск при вводе с debounce
        this.input.addEventListener('input', (e) => {
            const query = e.target.value.trim();
            
            clearTimeout(this.debounceTimeout);
            
            if (query.length === 0) {
                this.hideDropdown();
                return;
            }
            
            if (query.length < 2) {
                return; // Минимум 2 символа для поиска
            }
            
            this.debounceTimeout = setTimeout(() => {
                this.performSearch(query);
            }, 300);
        });
        
        // Скрытие dropdown при клике вне области
        document.addEventListener('click', (e) => {
            if (!this.container.contains(e.target)) {
                this.hideDropdown();
            }
        });
        
        // Показ dropdown при фокусе на input (если есть значение)
        this.input.addEventListener('focus', () => {
            const query = this.input.value.trim();
            if (query.length >= 2) {
                this.showDropdown();
            }
        });
        
        // Поиск по клику на кнопку
        this.submitBtn.addEventListener('click', () => {
            const query = this.input.value.trim();
            if (query.length >= 2) {
                this.performSearch(query);
            }
        });
        
        // Поиск по Enter
        this.input.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                const query = this.input.value.trim();
                if (query.length >= 2) {
                    this.performSearch(query);
                }
            }
            
            if (e.key === 'Escape') {
                this.hideDropdown();
            }
        });
    }
    
    async performSearch(query) {
        // Отменяем предыдущий запрос
        if (this.currentRequest) {
            this.currentRequest.abort();
        }
        
        // Показываем состояние загрузки
        this.showLoading();
        
        try {
            // Создаем новый AbortController для отмены запроса
            const controller = new AbortController();
            this.currentRequest = controller;
            
            const url = new URL(this.searchUrl, window.location.origin);
            url.searchParams.append('q', query);
            
            const response = await fetch(url.toString(), {
                signal: controller.signal,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            
            const data = await response.json();
            this.displayResults(data.products || []);
            
        } catch (error) {
            if (error.name !== 'AbortError') {
                console.error('Search error:', error);
                this.showNoResults();
            }
        } finally {
            this.currentRequest = null;
        }
    }
    
    displayResults(products) {
        this.resultsContainer.innerHTML = '';
        this.hideLoading();
        this.hideNoResults();
        
        if (products.length === 0) {
            this.showNoResults();
            return;
        }
        
        products.forEach(product => {
            const item = this.createResultItem(product);
            this.resultsContainer.appendChild(item);
        });
        
        this.showDropdown();
    }
    
    createResultItem(product) {
        const item = document.createElement('a');
        item.className = 'search-result-item';
        item.href = product.url;
        
        const stockClass = product.inStock ? 'in-stock' : 'out-of-stock';
        const stockText = product.inStock ? 'В наличии' : 'Нет в наличии';
        
        item.innerHTML = `
            <img src="${this.escapeHtml(product.image)}" 
                 alt="${this.escapeHtml(product.name)}" 
                 class="search-result-image"
                 onerror="this.src='/images/products/default.png'">
            <div class="search-result-info">
                <div class="search-result-name">${this.escapeHtml(product.name)}</div>
                <div class="search-result-price">${this.escapeHtml(product.priceFormatted)}</div>
                <div class="search-result-stock ${stockClass}">${stockText}</div>
            </div>
        `;
        
        // Закрываем dropdown при клике на результат
        item.addEventListener('click', () => {
            this.hideDropdown();
        });
        
        return item;
    }
    
    showDropdown() {
        this.dropdown.style.display = 'block';
        this.dropdown.classList.add('show');
    }
    
    hideDropdown() {
        this.dropdown.style.display = 'none';
        this.dropdown.classList.remove('show');
    }
    
    showLoading() {
        this.loadingContainer.style.display = 'block';
        this.noResultsContainer.style.display = 'none';
        this.showDropdown();
    }
    
    hideLoading() {
        this.loadingContainer.style.display = 'none';
    }
    
    showNoResults() {
        this.noResultsContainer.style.display = 'block';
        this.loadingContainer.style.display = 'none';
        this.showDropdown();
    }
    
    hideNoResults() {
        this.noResultsContainer.style.display = 'none';
    }
    
    escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }
}

// Инициализация виджетов поиска при загрузке DOM
document.addEventListener('DOMContentLoaded', function() {
    const searchWidgets = document.querySelectorAll('.js-product-search');
    searchWidgets.forEach(widget => {
        new ProductSearchWidget(widget);
    });
});
