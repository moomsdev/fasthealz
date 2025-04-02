(function ($) {
    /**
     * Mobile Menu Functionality
     * Handles mobile menu open/close and sub-menu interactions
     */
    function initMobileMenu() {
        // Open menu
        $('#touch-menu').click(function () {
            $('body').addClass('active-menu');
        });

        // Close menu
        $('.line-dark, #close-menu').click(function () {
            $('body').removeClass('active-menu');
        });

        // Add dropdown arrows and handle sub-menu toggle
        $("#menu-mobile .menu li.menu-item-has-children> a").after('<svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512"><path d="M201.4 342.6c12.5 12.5 32.8 12.5 45.3 0l160-160c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L224 274.7 86.6 137.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l160 160z"/></svg>');
        
        $('#menu-mobile .menu li.menu-item-has-children svg').click(function () {
            $(this).parent('li').children('ul').stop(0).slideToggle(300);
            $(this).parent('li').toggleClass('re-arrow');
        });
    }

    /**
     * UI Enhancement Functions
     * Handles various UI improvements and styling
     */
    function enhanceUI() {
        // Initialize select2
        $('.select2').select2({});

        // Add classes to tables
        $('table').addClass('table table-bordered').wrap('<div class="table-responsive"></div>');

        // Add classes to WooCommerce elements
        $('.woocommerce-product-details__short-description').addClass('content-post clearfix');
        $('.page-description').addClass('term-description');
        $('.term-description').addClass('content-post clearfix');
        $('.woocommerce-MyAccount-content').addClass('content-post clearfix');

        // Account page modifications
        $('.account-body #customer_login')
            .removeClass('u-columns col2-set')
            .children('.u-column1').removeClass('col-1')
            .end()
            .children('.u-column2').removeClass('col-2');

        $('.account-body .box-login .lost_password')
            .insertAfter('.account-body .box-login .woocommerce-form-login__rememberme');

        // Account toggle handlers
        $('.account-body .box-login .note .note1 a').click(function () {
            $('.account-body .box-login').addClass('active');
        });
        $('.account-body .box-login .note .note2 a').click(function () {
            $('.account-body .box-login').removeClass('active');
        });
    }

    /**
     * Scroll-related Functions
     * Handles back-to-top, fixed header, and smooth scroll
     */
    function initScrollFunctions() {
        // Back to top functionality
        $("#back-top").hide();
        $(window).scroll(function () {
            if ($(this).scrollTop() > 500) {
                $('#back-top').fadeIn();
            } else {
                $('#back-top').fadeOut();
            }
        });

        $('#back-top').click(function () {
            $('body,html').animate({scrollTop: 0}, 800);
            return false;
        });

        // Fixed header on scroll
        var navfixed = $(".head");
        $(window).scroll(function () {
            navfixed.toggleClass("navbar-fixed-top", $(this).scrollTop() > 10);
        });

        // Menu fixed functionality
        const mainMenu = document.querySelector('#menu-main-menu');
        const header = document.querySelector('#header');
        const threshold = mainMenu.offsetTop + mainMenu.offsetHeight;

        window.addEventListener('scroll', () => {
            header.classList.toggle('fixed', window.scrollY > threshold);
        });

        // Smooth scroll for anchor links
        document.addEventListener('click', function (event) {
            if (event.target.tagName === 'A' && event.target.getAttribute('href')?.startsWith('#')) {
                event.preventDefault();
                const targetId = event.target.getAttribute('href').slice(1);
                const targetElement = document.getElementById(targetId);
                if (targetElement) {
                    targetElement.scrollIntoView({behavior: 'smooth'});
                }
            }
        });
    }

    /**
     * Slider Initializations
     * Initializes various Swiper sliders
     */
    function initSliders() {
        // Banner slider
        new Swiper(".swiper-banner", {
            loop: true,
            autoplay: {delay: 6000},
            speed: 500,
            pagination: {
                el: ".banner-pagination",
                clickable: true,
            },
            navigation: {
                nextEl: ".banner-next",
                prevEl: ".banner-prev",
            },
            breakpoints: {
                0: {slidesPerView: 1},
                429: {slidesPerView: 1},
                575: {slidesPerView: 1},
                768: {slidesPerView: 1},
                992: {slidesPerView: 1}
            },
        });

        // Testimonials slider
        new Swiper(".swiper-testimonials", {
            loop: true,
            autoplay: {delay: 6000},
            speed: 500,
            pagination: {
                el: ".swiper-pagination",
                clickable: true,
            },
            breakpoints: {
                0: {slidesPerView: 2, spaceBetween: 25},
                575: {slidesPerView: 2, spaceBetween: 50},
                768: {slidesPerView: 3, spaceBetween: 80}
            },
        });

        // Posts slider
        new Swiper(".swiper-posts", {
            loop: true,
            autoplay: {delay: 6000},
            speed: 500,
            navigation: {
                nextEl: ".post-slider-next",
                prevEl: ".post-slider-prev",
            },
            breakpoints: {
                0: {slidesPerView: 1},
                575: {slidesPerView: 2, spaceBetween: 80},
                992: {slidesPerView: 3, spaceBetween: 80}
            },
        });

        // Media slider
        new Swiper(".swiper-media", {
            loop: true,
            autoplay: {delay: 6000},
            speed: 500,
            navigation: {
                nextEl: ".image-slider-next",
                prevEl: ".image-slider-prev",
            },
            breakpoints: {
                0: {slidesPerView: 1},
                575: {slidesPerView: 2, spaceBetween: 80},
                992: {slidesPerView: 3, spaceBetween: 80}
            },
        });
    }

    /**
     * Contact Form 7 Ajax Handler
     * Manages loading state of contact form submit button
     */
    function initContactForm() {
        $('.wpcf7-submit').click(function () {
            const thisElement = $(this);
            const oldVal = thisElement.val();
            const textLoading = 'Đang xử lý ...';
            
            $('.cf7_submit .ajax-loader').remove();
            thisElement.val(textLoading);
            
            document.addEventListener('wpcf7submit', function () {
                thisElement.val(oldVal);
            }, false);
        });
    }

    /**
     * Quick Order Functionality
     * Handles calculations for order totals and shipping
     */
    function initQuickOrder() {
        document.addEventListener('DOMContentLoaded', function () {
            const qtyInputs = document.querySelectorAll('.qty-input');
            const tableBody = document.querySelector('tbody[data-shipping]');
            const shippingFeeCell = document.querySelector('.shipping-fee');
            const totalCell = document.querySelector('.total-realtime');

            const shippingFee = parseInt(tableBody.dataset.shipping) || 0;
            const freeThreshold = parseInt(tableBody.dataset.threshold) || 2000000;

            function updateAllTotals() {
                let total = 0;

                qtyInputs.forEach(input => {
                    const price = parseFloat(input.dataset.price);
                    const qty = parseInt(input.value) || 0;
                    const subtotal = price * qty;

                    const row = input.closest('tr');
                    const subtotalCell = row.querySelector('.subtotal-realtime');
                    subtotalCell.textContent = subtotal.toLocaleString('vi-VN') + 'đ';

                    total += subtotal;
                });

                let shippingCost = shippingFee;
                if (total >= freeThreshold) {
                    shippingCost = 0;
                    shippingFeeCell.textContent = "Miễn phí";
                } else {
                    shippingFeeCell.textContent = shippingFee.toLocaleString('vi-VN') + 'đ';
                }

                const grandTotal = total + shippingCost;
                totalCell.textContent = grandTotal.toLocaleString('vi-VN') + 'đ';
            }

            qtyInputs.forEach(input => {
                input.addEventListener('input', updateAllTotals);
            });

            updateAllTotals();
        });
    }

    // Initialize all functionality
    initMobileMenu();
    enhanceUI();
    initScrollFunctions();
    initSliders();
    initContactForm();
    initQuickOrder();

})(jQuery);