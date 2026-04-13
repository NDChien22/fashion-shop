// Banner Carousel Handler
(function() {
    let currentSlide = 0;
    let slides = [];
    let autoPlayTimeout;
    const SLIDE_DURATION = 5000; // 5 giây/slide (total 20s cho 4 slides)
    const ANIMATION_DURATION = 500; // 500ms cho transition smooth

    // Khởi tạo banner carousel
    function initBanner() {
        const bannerList = document.getElementById('banner-list');
        const prevBtn = document.getElementById('prev-btn');
        const nextBtn = document.getElementById('next-btn');

        if (!bannerList) return; // Nếu không có banner, thoát

        // Lấy số lượng banner items
        slides = document.querySelectorAll('.banner-item');
        
        if (slides.length === 0) return;

        // Gán sự kiện cho các nút
        if (prevBtn) {
            prevBtn.addEventListener('click', handlePrevClick);
        }
        
        if (nextBtn) {
            nextBtn.addEventListener('click', handleNextClick);
        }

        // Cập nhật trạng thái dot ban đầu
        updateDots();

        // Chỉ bật auto-play khi có nhiều hơn 1 banner
        if (slides.length > 1) {
            startAutoPlay();
        }
    }

    // Hàm chuyển đến slide cụ thể
    function goToSlide(index) {
        const bannerList = document.getElementById('banner-list');
        if (!bannerList || slides.length === 0) return;

        // Đảm bảo index nằm trong phạm vi
        currentSlide = (index + slides.length) % slides.length;

        // Tính toán vị trí dịch chuyển
        const translateX = -currentSlide * (100 / slides.length);
        
        // Áp dụng transition smooth
        bannerList.style.transition = `transform ${ANIMATION_DURATION}ms ease-in-out`;
        bannerList.style.transform = `translateX(${translateX}%)`;

        // Cập nhật indicator dots
        updateDots();

        // Reset CSS animation
        resetCSSAnimation();
    }

    // Hàm xử lý click nút prev
    function handlePrevClick() {
        clearTimeout(autoPlayTimeout);
        goToSlide(currentSlide - 1);
        // Restart auto-play sau khi người dùng interact
        if (slides.length > 1) {
            startAutoPlay();
        }
    }

    // Hàm xử lý click nút next
    function handleNextClick() {
        clearTimeout(autoPlayTimeout);
        goToSlide(currentSlide + 1);
        // Restart auto-play sau khi người dùng interact
        if (slides.length > 1) {
            startAutoPlay();
        }
    }

    // Hàm cập nhật indicator dots
    function updateDots() {
        const dots = document.querySelectorAll('[data-banner-dot]');
        dots.forEach((dot, index) => {
            if (index === currentSlide) {
                dot.style.width = '40px';
                dot.style.backgroundColor = '#c5a059';
            } else {
                dot.style.width = '8px';
                dot.style.backgroundColor = '#d1d5db';
            }
        });
    }

    // Hàm auto-play
    function startAutoPlay() {
        if (slides.length <= 1) return;

        clearTimeout(autoPlayTimeout);
        
        autoPlayTimeout = setTimeout(() => {
            goToSlide(currentSlide + 1);
            startAutoPlay(); // Recursively continue auto-play
        }, SLIDE_DURATION);
    }

    // Hàm reset CSS animation (remove CSS animation, use JS control)
    function resetCSSAnimation() {
        const bannerList = document.getElementById('banner-list');
        if (bannerList) {
            // Tạm dừng CSS animation
            bannerList.style.animation = 'none';
        }
    }

    // Hàm pause khi hover
    function pauseOnHover() {
        const bannerGroup = document.querySelector('.banner-group');
        if (!bannerGroup) return;

        bannerGroup.addEventListener('mouseenter', () => {
            clearTimeout(autoPlayTimeout);
            const bannerList = document.getElementById('banner-list');
            if (bannerList) {
                bannerList.style.animation = 'none';
                bannerList.style.transition = 'none';
            }
        });

        bannerGroup.addEventListener('mouseleave', () => {
            if (slides.length > 1) {
                startAutoPlay();
            }
        });
    }

    // Khỏi động khi DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            initBanner();
            pauseOnHover();
        });
    } else {
        initBanner();
        pauseOnHover();
    }

    // Export functions to window for external use
    window.bannerCarousel = {
        goToSlide,
        handlePrevClick,
        handleNextClick
    };
})();
