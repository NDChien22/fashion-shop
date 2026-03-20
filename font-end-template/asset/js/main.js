const menuBtn = document.getElementById('menu-btn');
        const closeBtn = document.getElementById('close-btn');
        const navMenu = document.getElementById('nav-menu');

        menuBtn.addEventListener('click', () => {
            navMenu.classList.add('active');
        });

        closeBtn.addEventListener('click', () => {
            navMenu.classList.remove('active');
        });