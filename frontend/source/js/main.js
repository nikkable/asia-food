const ready = function () {
    AOS.init({
        once: true
    });
    /*
    AOS.init({
        // Глобальные настройки:
        disable: false, // принимает следующие значения: «телефон», «планшет», «мобильный», логическое значение, выражение или функция.
        startEvent: 'DOMContentLoaded', // имя события, отправленного в документе, которое AOS должен инициализировать
        initClassName: 'aos-init', // класс применяется после инициализации
        animatedClassName: 'aos-animate', // класс, примененный к анимации
        useClassNames: false, // если true, содержимое `data-aos` будет добавлено в качестве классов при прокрутке
        disableMutationObserver: false, // отключает автоматическое обнаружение мутаций (дополнительно)
        debounceDelay: 50, // задержка устранения дребезга, используемая при изменении размера окна (дополнительно)
        throttleDelay: 99, // задержка дросселирования, используемая при прокрутке страницы (дополнительно)


        // Настройки, которые можно переопределить для каждого элемента с помощью атрибутов data-aos-*:
        offset: 120, // смещение (в пикселях) от исходной точки триггера
        delay: 0, // значения от 0 до 3000, с шагом 50мс
        duration: 400, // значения от 0 до 3000, с шагом 50мс
        easing: 'ease', // замедление по умолчанию для анимации AOS
        once: true, // должна ли анимация происходить только один раз — при прокрутке вниз
        mirror: false, // должны ли элементы анимироваться при прокрутке мимо них
        anchorPlacement: 'top-bottom', // определяет, какая позиция элемента относительно окна должна запускать анимацию

    })

     */

    // Добавление класса при клике и удаление при закрытии или клика вне. Default - мобильное меню
    const Menu = function (params) {
        const menuSelector = getParam('selector') ?? '.js-mobile'
        const menuSelectorClose = getParam('selectorClose') ?? '.js-mobile-close'
        const menuSelectorButton = getParam('selectorButton') ?? '.js-header-app'

        const classAddName = 'mobile-open'

        const body = document.querySelector('body')
        const menuButton = document.querySelector(menuSelectorButton)
        const menu = document.querySelector(menuSelector)
        const menuClose = document.querySelector(menuSelectorClose)

        if(!menuButton || !menu || !menuClose) return

        menuButton.addEventListener('click', (event) => {
            event.preventDefault()
            mobileOpen()
        })

        menuClose.addEventListener('click', (event) => {
            event.preventDefault()
            mobileClose()
        })

        document.addEventListener('click', (event) => {
            const target = event.target

            if(!target.closest(menuSelector) && !target.closest(menuSelectorButton)) {
                mobileClose()
            }
        })

        function getParam(param) {
            return params && params[param] ? params[param] : null
        }

        function mobileOpen() {
            menu.classList.add(classAddName)
            body.classList.add(classAddName)
        }

        function mobileClose() {
            menu.classList.remove(classAddName)
            body.classList.remove(classAddName)
        }
    }

    // Модальные окна
    const Modal = function () {
        const modalSelectorClose = '.js-modal-close'
        const classAddName = 'modal--show'
        const dataAttributeClick = 'data-modal'
        const dataAttributeId = 'data-modal-id'

        const modals = document.querySelectorAll('[' + dataAttributeClick + ']')
        const body = document.querySelector('body')

        modals.forEach(modal => {
            modal.addEventListener('click', (event) => {
                event.preventDefault()

                const target = event.target
                const current = target.closest('[' + dataAttributeClick + ']')
                const currentModalId = current.getAttribute(dataAttributeId)

                const modalOpened = document.getElementById(currentModalId)

                if(modalOpened) {
                    const modalCloseElem = modalOpened.querySelector(modalSelectorClose)

                    if(!modalCloseElem) return

                    modalCloseElem.addEventListener('click', (eventClose) => {
                        eventClose.preventDefault()

                        modalClose(modalOpened)
                    })

                    modalOpened.addEventListener('click', (eventModal) => {
                        const modalOpenedTarget = eventModal.target

                        if(!modalOpenedTarget.closest('.modal-dialog')) {
                            modalClose(modalOpened)
                        }
                    })

                    modalOpen(modalOpened)
                }
            })
        })

        const modalCloses = document.querySelectorAll(modalSelectorClose)
        modalCloses.forEach(close => {
            close.addEventListener('click', function (event) {
                event.preventDefault()
                const target = event.currentTarget
                const modal = target.closest('.modal')

                modalClose(modal)
            })
        })

        function modalOpen(elem) {
            elem.classList.add(classAddName)
            // body.classList.add(classAddName)
        }

        function modalClose(elem) {
            elem.classList.remove(classAddName)
            // body.classList.remove(classAddName)
        }
    }

    const Faq = function () {
        const items = document.querySelectorAll('.js-faq-item')

        items.forEach(item => {
            item.addEventListener('click', event => {
                const current = event.currentTarget

                if(current.classList.contains('show')) {
                    current.classList.remove('show')
                } else {
                    current.classList.add('show')
                }
            })
        })
    }

    Menu()
    Modal()
    Faq()

    function initializeGoogleAnalytics() {
        console.log('Google Analytics инициализируется...');

        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-0F47YPMX6E');
    }

    // Функция для отключения Google Analytics
    function disableGoogleAnalytics() {
        console.log('Google Analytics отключен.');

        if (typeof gtag === 'function') {
            gtag('consent', 'update', {
                'analytics_storage': 'denied'
            });
            console.log('Согласие на Google Analytics обновлено: analytics_storage = denied.');
        } else {
            console.warn('Функция gtag не найдена. Google Analytics, возможно, не инициализирован или уже отключен.');
        }
    }

    function manageAuthFeatures(enable) {
        const loginLink = document.querySelector('#login-form');
        const registerLink = document.querySelector('#registration-form');
        const authContainer = document.querySelector('.auth-box');

        if (enable) {
            console.log('Функции регистрации и авторизации включены.');
            if (loginLink) loginLink.querySelector('.auth-box-form').style.display = '';
            if (loginLink) loginLink.querySelector('.auth-box-btns').style.display = '';
            if (registerLink) registerLink.querySelector('.auth-box-form').style.display = '';
            if (registerLink) registerLink.querySelector('.auth-box-btns').style.display = '';
        } else {
            console.log('Функции регистрации и авторизации отключены.');
            if (loginLink) loginLink.querySelector('.auth-box-form').style.display = 'none';
            if (loginLink) loginLink.querySelector('.auth-box-btns').style.display = 'none';
            if (registerLink) registerLink.querySelector('.auth-box-form').style.display = 'none';
            if (registerLink) registerLink.querySelector('.auth-box-btns').style.display = 'none';

            const messageHtml = `
                    <div class="consent-required-message" style="color: #000;">
                        <p style="color: #000;">${cookieConsentMessageText}</p>
                        <p style="color: #000;"><a class="js-cookie-show" href="#">${cookieConsentBtnText}</a></p>
                    </div>
                `;
            const messageElement = createDOMElements(messageHtml);

            if(authContainer) {
                authContainer.appendChild(messageElement);

                const cookieNotificationShowBtn = authContainer.querySelector('.js-cookie-show');

                if(cookieNotificationShowBtn) {
                    const cookieNotification = document.querySelector('.js-cookie');
                    cookieNotificationShowBtn.addEventListener('click', function (event) {
                        event.preventDefault();
                        if(cookieNotification) {
                            cookieNotification.classList.add('show');
                        }
                    });
                }
            }
        }
    }

    function createDOMElements(htmlString) {
        const template = document.createElement('template');
        template.innerHTML = htmlString.trim();
        return template.content;
    }

    function checkCookies() {
        applyConsent();
        const cookieConsent = localStorage.getItem('cookieConsent');
        let cookieNotification = document.querySelector('.js-cookie');
        const acceptAllBtn = cookieNotification.querySelector('.js-cookie-accept-all');
        const necessaryOnlyBtn = cookieNotification.querySelector('.js-cookie-necessary-only');
        const rejectBtn = cookieNotification.querySelector('.js-cookie-reject');

        // Определяем, было ли согласие уже дано
        if (cookieConsent) {
            cookieNotification.classList.remove('show');
            applyConsent(cookieConsent);
        } else {
            cookieNotification.classList.add('show');
        }

        // Обработчик для кнопки "Принять все"
        acceptAllBtn.addEventListener('click', function () {
            localStorage.setItem('cookieConsent', 'all');
            cookieNotification.classList.remove('show');
            applyConsent('all');
        });

        // Обработчик для кнопки "Только необходимое"
        necessaryOnlyBtn.addEventListener('click', function () {
            localStorage.setItem('cookieConsent', 'necessary');
            cookieNotification.classList.remove('show');
            applyConsent('necessary');
        });

        // Обработчик для кнопки "Отклонить"
        rejectBtn.addEventListener('click', function () {
            localStorage.setItem('cookieConsent', 'rejected');
            cookieNotification.classList.remove('show');
            applyConsent('rejected');
        });

        // При клике на кнопку, в локальное хранилище записывается текущая дата
        // cookieBtn.addEventListener('click', function () {
        //     localStorage.setItem('cookieDate', Date.now())
        //     cookieNotification.classList.remove('show')
        // })
        //
        // cookieBtnCancel.addEventListener('click', function () {
        //     localStorage.setItem('cookieDateCancel', Date.now())
        //     cookieNotification.classList.remove('show')
        // })
    }

    // Функция для применения выбора пользователя
    function applyConsent(consentType) {
        switch (consentType) {
            case 'all':
                initializeGoogleAnalytics();
                manageAuthFeatures(true); // Включаем авторизацию и регистрацию
                console.log('Согласие: Принять все. Google Analytics включен, авторизация включена.');
                break;
            case 'necessary':
                disableGoogleAnalytics();
                manageAuthFeatures(true); // Включаем авторизацию и регистрацию
                console.log('Согласие: Только необходимое. Google Analytics отключен, авторизация включена.');
                break;
            case 'rejected':
                disableGoogleAnalytics();
                manageAuthFeatures(false); // Отключаем авторизацию и регистрацию
                console.log('Согласие: Отклонить. Google Analytics отключен, авторизация отключена.');
                break;
            default:
                console.warn('Неизвестный тип согласия:', consentType);
                initializeGoogleAnalytics();
                manageAuthFeatures(true);
                break;
        }
    }

    // Уведомление об использовании cookies
    checkCookies();

    // Language
    const langBoxes = document.querySelectorAll('.js-lang-box')

    langBoxes.forEach(langBox => {
        langBox.addEventListener('mouseover', function (event) {
            const current = event.currentTarget
            const currentUl = current.querySelector('ul')

            if(!currentUl) return

            currentUl.classList.add('active')
        })

        langBox.addEventListener('mouseout', function (event) {
            const current = event.currentTarget
            const currentUl = current.querySelector('ul')

            if(!currentUl) return

            currentUl.classList.remove('active')
        })
    })

    // Currency
    const currencies = document.querySelectorAll('.js-currency a')

    currencies.forEach(currency => {
        currency.addEventListener('click', function (event) {
            event.preventDefault()

            const currency = event.currentTarget
            const currencyValue = currency.text

            setCookie('currency', currencyValue, { path: '/' })

            location.reload(true)
        })
    })
}

document.addEventListener("DOMContentLoaded", ready)

function setCookie(name, value, options) {
    options = options || {};

    var expires = options.expires;

    if (typeof expires == 'number' && expires) {
        var d = new Date();
        d.setTime(d.getTime() + expires * 1000);
        expires = options.expires = d;
    }
    if (expires && expires.toUTCString) {
        options.expires = expires.toUTCString();
    }

    value = encodeURIComponent(value);

    var updatedCookie = name + '=' + value;

    for (var propName in options) {
        updatedCookie += '; ' + propName;
        var propValue = options[propName];
        if (propValue !== true) {
            updatedCookie += '=' + propValue;
        }
    }

    document.cookie = updatedCookie;
}
