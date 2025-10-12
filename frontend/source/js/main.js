const ready = function () {
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


    const catalogSwiper = new Swiper('.js-screen-main', {
        loop: true,
        spaceBetween: 20,
        slidesPerGroup: 1,
        slidesPerView: 1,
        centeredSlides: true,
        navigation: {
            nextEl: '.js-slider-next',
            prevEl: '.js-slider-prev',
        },
        pagination: {
            el: '.js-slider-dots',
            clickable: true,
            bulletClass: 'slider-dot',
            bulletActiveClass: 'slider-dot-active',
        },
        autoplay: {
            delay: 3000,
            disableOnInteraction: false,
        },
    });

    const catalogSliderContainer = document.querySelector('.js-screen-main');

    catalogSliderContainer.addEventListener('mouseenter', () => {
        catalogSwiper.autoplay.stop();
    });

    catalogSliderContainer.addEventListener('mouseleave', () => {
        catalogSwiper.autoplay.start();
    });
}

document.addEventListener("DOMContentLoaded", ready)
