
const PAGES = {
    'trang-chu': { title: 'Trang chủ', file: 'home.html' },
    'gioi-thieu': { title: 'Giới thiệu', file: 'introduce.html' }
};

let countdownInterval = null;


function startCountdown() {
    if (countdownInterval) clearInterval(countdownInterval);

    const h = document.getElementById('hours');
    const m = document.getElementById('minutes');
    const s = document.getElementById('seconds');

    if (!h || !m || !s) return;

    let hh = 2, mm = 45, ss = 0;

    countdownInterval = setInterval(() => {
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

        if (hh === 0 && mm === 0 && ss === 0) {
            clearInterval(countdownInterval);
        }
    }, 1000);
}


async function loadPage() {
    const content = document.getElementById("content-area");
    if (!content) return;

    let hash = window.location.hash || '#';
    let pageId = hash.replace('#', '') || 'trang-chu';

    const config = PAGES[pageId];

    if (!config) {
        content.innerHTML = `<div class="p-20 text-center">Không tìm thấy trang</div>`;
        return;
    }

    try {
        // hiệu ứng mượt
        content.classList.add("opacity-0");

        const res = await fetch(config.file);
        if (!res.ok) throw new Error("404");

        const html = await res.text();

        setTimeout(() => {
            content.innerHTML = html;
            content.classList.remove("opacity-0");

            updateUI(pageId, config.title);
            runPageScript(pageId);

        }, 150);

    } catch (err) {
        console.error(err);
        content.innerHTML = `<div class="p-20 text-center">Trang đang cập nhật...</div>`;
    }
}


function runPageScript(pageId) {

    // Trang chủ
    if (pageId === 'dashboard') {
        if (typeof renderProducts === 'function') renderProducts();
        if (typeof renderBestSellers === 'function') renderBestSellers();
    }

    // Trang giới thiệu
    if (pageId === 'gioi-thieu') {
        startCountdown();
    }
}


function updateUI(pageId, title) {
    const bcCurrent = document.getElementById("breadcrumb-current");
    const bcArea = document.getElementById("breadcrumb-area");

    // breadcrumb
    if (bcArea) {
        const isHome = pageId === 'dashboard';
        bcArea.classList.toggle('hidden', isHome);

        if (bcCurrent && !isHome) {
            bcCurrent.innerHTML = `
                <span class="mx-2 text-gray-300">/</span>
                <span class="text-[#bc9c75] font-bold">${title}</span>
            `;
        }
    }

    // active menu
    document.querySelectorAll('.menu-link').forEach(link => {
        const href = link.getAttribute('href');

        const active =
            (pageId === 'dashboard' && href === '#') ||
            (href === `#${pageId}`);

        if (active) {
            link.classList.add('text-[#bc9c75]', 'border-b-2', 'border-[#bc9c75]');
            link.classList.remove('text-gray-600');
        } else {
            link.classList.remove('text-[#bc9c75]', 'border-b-2', 'border-[#bc9c75]');
            link.classList.add('text-gray-600');
        }
    });
}


window.addEventListener('hashchange', loadPage);
document.addEventListener('DOMContentLoaded', loadPage);
