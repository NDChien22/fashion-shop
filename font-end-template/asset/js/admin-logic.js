document.addEventListener('DOMContentLoaded', function() {
            const navItems = document.querySelectorAll('.nav-item');
            const tabContents = document.querySelectorAll('.tab-content');

            navItems.forEach(item => {
                item.addEventListener('click', function() {
                    const target = this.getAttribute('data-target');

                    // 1. Cập nhật trạng thái Menu
                    navItems.forEach(i => i.classList.remove('active'));
                    this.classList.add('active');

                    // 2. Chuyển đổi nội dung Tab
                    tabContents.forEach(tab => {
                        tab.classList.remove('active');
                        if (tab.id === target) {
                            tab.classList.add('active');
                        }
                    });
                });
            });
        });