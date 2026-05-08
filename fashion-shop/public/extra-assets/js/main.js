// Keep only client-side UI helpers in this file.
// Product listing, cart state and pagination are handled by Laravel + Livewire.

function initWishlistAjax() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    if (document.body.dataset.wishlistAjaxBound === '1') {
        return;
    }

    document.body.dataset.wishlistAjaxBound = '1';

    document.addEventListener('click', function (event) {
        const wishlistButton = event.target.closest('.js-wishlist-button');
        if (!wishlistButton) {
            return;
        }

        // Product cards are wrapped by links; block link navigation when heart is clicked.
        event.preventDefault();
        event.stopPropagation();
    });

    document.addEventListener('submit', async function (event) {
        const form = event.target.closest('.js-wishlist-form');
        if (!form) return;

        event.preventDefault();

        const button = form.querySelector('.js-wishlist-button');
        const icon = form.querySelector('.js-wishlist-icon');
        const methodField = form.querySelector('.js-wishlist-method');
        const isWishlisted = form.dataset.wishlisted === '1';
        const addUrl = form.dataset.addUrl || form.action;
        const removeUrl = form.dataset.removeUrl || form.action;
        const targetUrl = isWishlisted ? removeUrl : addUrl;

        const formData = new FormData(form);
        formData.set('_method', isWishlisted ? 'DELETE' : 'POST');

        if (button) {
            button.disabled = true;
            button.classList.add('opacity-70');
        }

        try {
            const response = await fetch(targetUrl, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json, text/html;q=0.9,*/*;q=0.8',
                },
                body: new URLSearchParams(formData),
                credentials: 'same-origin',
            });

            if (response.redirected && response.url.includes('/login')) {
                window.location.href = response.url;
                return;
            }

            if (!response.ok) {
                throw new Error('Wishlist request failed');
            }

            const nextWishlisted = !isWishlisted;
            form.dataset.wishlisted = nextWishlisted ? '1' : '0';
            form.action = nextWishlisted ? removeUrl : addUrl;

            if (methodField) {
                methodField.value = nextWishlisted ? 'DELETE' : '';
            }

            if (button) {
                button.classList.toggle('text-red-500', nextWishlisted);
                button.classList.toggle('text-gray-800', !nextWishlisted);
            }

            if (icon) {
                icon.classList.remove('ri-heart-line', 'ri-heart-fill');
                icon.classList.add(nextWishlisted ? 'ri-heart-fill' : 'ri-heart-line');
            }
        } catch (error) {
            console.error(error);
        } finally {
            if (button) {
                button.disabled = false;
                button.classList.remove('opacity-70');
            }
        }
    });
}

/**
 * Hàm đóng/mở Sidebar trên Mobile
 */
function toggleSidebar(isOpen) {
    const sidebar = document.getElementById('main-sidebar');
    const overlay = document.getElementById('sidebar-overlay');

    if (!sidebar || !overlay) {
        return;
    }

    if (isOpen) {
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    } else {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
        document.body.style.overflow = '';
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const menuBtn = document.getElementById('menu-btn');
    const overlay = document.getElementById('sidebar-overlay');
    const headerNavOverlay = document.getElementById('header-nav-overlay');
    const headerNavDrawer = document.getElementById('header-nav-drawer');

    if (menuBtn) {
        menuBtn.onclick = function (e) {
            e.preventDefault();
            if (headerNavDrawer && headerNavOverlay) {
                headerNavDrawer.classList.remove('hidden');
                headerNavOverlay.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        };
    }

    if (overlay) {
        overlay.onclick = function () {
            toggleSidebar(false);
        };
    }

    if (headerNavOverlay) {
        headerNavOverlay.onclick = function () {
            if (headerNavDrawer) {
                headerNavDrawer.classList.add('hidden');
            }
            headerNavOverlay.classList.add('hidden');
            document.body.style.overflow = '';
        };
    }
});

// Sử dụng Event Delegation để xử lý cho SPA

document.addEventListener('click', function (e) {
    const header = e.target.closest('.accordion-header');
    if (!header) return;

    const content = header.nextElementSibling;
    const icon = header.querySelector('.accordion-icon');
    if (!content) return;

    const isOpen = content.style.maxHeight && content.style.maxHeight !== '0px';

    if (isOpen) {
        content.style.maxHeight = '0px';
        if (icon) {
            icon.classList.replace('ri-subtract-line', 'ri-add-line');
        }
        header.classList.remove('text-[#bc9c75]');
    } else {
        document.querySelectorAll('.accordion-content').forEach(function (el) {
            el.style.maxHeight = '0px';
            const otherHeader = el.previousElementSibling;
            if (otherHeader) {
                otherHeader.classList.remove('text-[#bc9c75]');
                const otherIcon = otherHeader.querySelector('.accordion-icon');
                if (otherIcon) otherIcon.classList.replace('ri-subtract-line', 'ri-add-line');
            }
        });

        content.style.maxHeight = content.scrollHeight + 'px';
        if (icon) {
            icon.classList.replace('ri-add-line', 'ri-subtract-line');
        }
        header.classList.add('text-[#bc9c75]');
    }
});

document.addEventListener('DOMContentLoaded', function () {
    const chatBtn = document.getElementById('chat-toggle-btn');
    const supportBox = document.getElementById('support-box');
    const closeChatBtn = document.getElementById('close-chat-btn');
    const chatIcon = document.getElementById('chat-icon');

    if (!supportBox) {
        return;
    }

    function toggleChat() {
        const isHidden = supportBox.classList.contains('hidden');

        if (isHidden) {
            supportBox.classList.remove('hidden');
            setTimeout(function () {
                supportBox.classList.remove('opacity-0', 'translate-y-4');
                supportBox.classList.add('opacity-100', 'translate-y-0');
            }, 10);

            if (chatIcon) chatIcon.classList.replace('ri-messenger-fill', 'ri-close-line');
        } else {
            supportBox.classList.add('opacity-0', 'translate-y-4');
            supportBox.classList.remove('opacity-100', 'translate-y-0');

            setTimeout(function () {
                supportBox.classList.add('hidden');
            }, 300);

            if (chatIcon) chatIcon.classList.replace('ri-close-line', 'ri-messenger-fill');
        }
    }

    if (chatBtn) {
        chatBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            toggleChat();
        });
    }

    if (closeChatBtn) {
        closeChatBtn.addEventListener('click', toggleChat);
    }
});

document.addEventListener('DOMContentLoaded', function () {
    const backToTopBtn = document.getElementById('back-to-top');

    if (!backToTopBtn) return;

    window.addEventListener('scroll', function () {
        if (window.scrollY > 300) {
            backToTopBtn.classList.remove('opacity-0', 'invisible', 'translate-y-10');
            backToTopBtn.classList.add('opacity-100', 'visible', 'translate-y-0');
        } else {
            backToTopBtn.classList.remove('opacity-100', 'visible', 'translate-y-0');
            backToTopBtn.classList.add('opacity-0', 'invisible', 'translate-y-10');
        }
    });

    backToTopBtn.onclick = function () {
        window.scrollTo({
            top: 0,
            behavior: 'smooth',
        });
    };
});

let countdownInterval = null;

function startCountdown() {
    if (countdownInterval) clearInterval(countdownInterval);
    const h = document.getElementById('hours');
    const m = document.getElementById('minutes');
    const s = document.getElementById('seconds');
    if (!h || !m || !s) return;

    let hh = 2;
    let mm = 45;
    let ss = 0;
    countdownInterval = setInterval(function () {
        if (ss > 0) ss--;
        else {
            if (mm > 0) {
                mm--;
                ss = 59;
            } else if (hh > 0) {
                hh--;
                mm = 59;
                ss = 59;
            }
        }
        h.innerText = String(hh).padStart(2, '0');
        m.innerText = String(mm).padStart(2, '0');
        s.innerText = String(ss).padStart(2, '0');
        if (hh === 0 && mm === 0 && ss === 0) clearInterval(countdownInterval);
    }, 1000);
}

document.addEventListener('DOMContentLoaded', function () {
    startCountdown();
    initWishlistAjax();

    if (typeof window.updateCartBadge === 'function') {
        window.updateCartBadge();
    }

    window.addEventListener('cart-count-updated', function (event) {
        const badge = document.getElementById('cart-count');
        if (!badge) {
            return;
        }

        const count = Number(event?.detail?.count || 0);
        badge.innerText = String(count);
        badge.style.display = count > 0 ? 'flex' : 'none';
    });

    window.addEventListener('whistlist-count-updated', function (event) {
        const badge = document.getElementById('whistlist-count');
        if (!badge) {
            return;
        }

        const count = Number(event?.detail?.count || 0);
        badge.innerText = String(count);

        if (count > 0) {
            badge.classList.remove('hidden');
            badge.classList.add('flex');
        } else {
            badge.classList.remove('flex');
            badge.classList.add('hidden');
        }
    });

    window.addEventListener('wishlist-item-removed', function (event) {
        const productId = Number(event?.detail?.productId || 0);
        if (!productId) {
            return;
        }

        const card = document.querySelector('[data-product-card="1"][data-product-id="' + productId + '"]');
        if (!card) {
            return;
        }

        const grid = document.getElementById('whistlist-grid');
        const emptyState = document.getElementById('whistlist-empty-state');

        card.remove();

        if (!grid || !emptyState) {
            return;
        }

        const hasItems = grid.querySelectorAll('[data-product-card="1"]').length > 0;
        if (hasItems) {
            return;
        }

        grid.classList.add('hidden');
        emptyState.classList.remove('hidden');
    });
});

function toggleAccordion(btn) {
    document.querySelectorAll('.accordion-content').forEach(function (el) {
        if (el !== btn.nextElementSibling) {
            el.classList.add('hidden');
            const icon = el.previousElementSibling?.querySelector('svg');
            if (icon) {
                icon.classList.remove('rotate-180');
            }
        }
    });

    const content = btn.nextElementSibling;
    const icon = btn.querySelector('svg');
    if (!content || !icon) {
        return;
    }

    content.classList.toggle('hidden');
    icon.classList.toggle('rotate-180');
}

function initFAQ() {
    const buttons = document.querySelectorAll('.accordion-item button');

    buttons.forEach(function (btn) {
        btn.replaceWith(btn.cloneNode(true));
    });

    const newButtons = document.querySelectorAll('.accordion-item button');
    newButtons.forEach(function (btn) {
        btn.addEventListener('click', function () {
            const item = this.closest('.accordion-item');
            const content = item?.querySelector('.accordion-content');
            const icon = this.querySelector('svg');
            if (!item || !content) {
                return;
            }

            const isOpen = item.classList.contains('active-faq');

            if (isOpen) {
                item.classList.remove('active-faq');
                content.style.maxHeight = null;
                if (icon) icon.classList.remove('rotate-180');
            } else {
                item.classList.add('active-faq');
                content.style.maxHeight = content.scrollHeight + 'px';
                if (icon) icon.classList.add('rotate-180');
            }
        });
    });

    const navLinks = document.querySelectorAll('a[href^="#"]');

    navLinks.forEach(function (link) {
        link.addEventListener('click', function (e) {
            const targetId = this.getAttribute('href')?.substring(1);
            if (!targetId) {
                return;
            }

            const targetElement = document.getElementById(targetId);

            if (targetElement) {
                e.preventDefault();

                const headerOffset = 150;
                const elementPosition = targetElement.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth',
                });
            }
        });
    });
}
