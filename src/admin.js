    document.addEventListener('DOMContentLoaded', function () {
        const buttons = document.querySelectorAll('.show-location');
        buttons.forEach(btn => {
            btn.addEventListener('click', function () {
                const container = this.nextElementSibling;
                if (container.style.display === 'none' || container.style.display === '') {
                    container.textContent = 'Локація: ' + this.dataset.location;
                    container.style.display = 'block';
                } else {
                    container.style.display = 'none';
                }
            });
        });
    });

fetch('userpage.php')
    .then(response => response.json())
    .then(data => {
        document.getElementById('today').textContent = data.today;
        document.getElementById('yesterday').textContent = data.yesterday;
        document.getElementById('month').textContent = data.month;
        document.getElementById('total').textContent = data.total;
    })
    .catch(error => console.error('Помилка завантаження статистики:', error));