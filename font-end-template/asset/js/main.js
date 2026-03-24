        const menuBtn = document.getElementById('menu-btn');
        const closeBtn = document.getElementById('close-btn');
        const navMenu = document.getElementById('nav-menu');

        menuBtn.addEventListener('click', () => {
            navMenu.classList.add('active');
        });

        closeBtn.addEventListener('click', () => {
            navMenu.classList.remove('active');
        });



        function startCountdown() {
            let h = 2, m = 45, s = 0;
            setInterval(() => {
                if (s > 0) s--;
                else { if (m > 0) { m--; s = 59; } else { if (h > 0) { h--; m = 59; s = 59; } } }
                document.getElementById('hours').innerText = String(h).padStart(2, '0');
                document.getElementById('minutes').innerText = String(m).padStart(2, '0');
                document.getElementById('seconds').innerText = String(s).padStart(2, '0');
            }, 1000);
        }
        startCountdown();



   