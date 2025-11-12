<?php
include 'db.php';

$filter_group = $_GET['group_id'] ?? '';
$filter_subject = $_GET['subject_id'] ?? '';
$filter_teacher = $_GET['teacher_id'] ?? '';
$filter_day = $_GET['day_of_week'] ?? '';

$sql = "SELECT 
            schedule.id, 
            academic_years.year_name AS academic_year, 
            `groups`.name AS group_name, 
            `subject`.name AS subject_name, 
            teachers.full_name AS teacher_name, 
            `classroom`.name AS classroom_name, 
            `classroom`.location AS classroom_location,
            lesson_number.lesson_number AS lesson_number, 
            schedule.day_of_week, 
            schedule.lesson_type,
            schedule.lesson_type
        FROM schedule
        LEFT JOIN academic_years ON schedule.academic_year_id = academic_years.id
        LEFT JOIN `groups` ON schedule.group_id = `groups`.id
        LEFT JOIN `subject` ON schedule.subject_id = `subject`.id
        LEFT JOIN teachers ON schedule.teacher_id = teachers.id
        LEFT JOIN `classroom` ON schedule.classroom_id = `classroom`.id
        LEFT JOIN lesson_number ON schedule.lesson_number_id = lesson_number.id
        WHERE 1";

if (!empty($filter_group)) $sql .= " AND schedule.group_id = '" . $conn->real_escape_string($filter_group) . "'";
if (!empty($filter_subject)) $sql .= " AND schedule.subject_id = '" . $conn->real_escape_string($filter_subject) . "'";
if (!empty($filter_teacher)) $sql .= " AND schedule.teacher_id = '" . $conn->real_escape_string($filter_teacher) . "'";
if (!empty($filter_day)) $sql .= " AND schedule.day_of_week = '" . $conn->real_escape_string($filter_day) . "'";

$result = $conn->query($sql);
if (!$result) {
    die("Помилка SQL-запиту: " . $conn->error);
}

$groups_all = $conn->query("SELECT * FROM `groups`");
$subject = $conn->query("SELECT * FROM  `subject`");
$teachers = $conn->query("SELECT * FROM teachers");

$grouped = [];
$groups_result = $conn->query("SELECT * FROM `groups`");
while ($row = $groups_result->fetch_assoc()) {
    $parts = explode('-', $row['name']);
    $main = $parts[0];
    $grouped[$main][] = $row;
}
?>


    <!DOCTYPE html>
    <html lang="uk">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Навчальний розклад ВСП "РТФК" НУВГП</title>
        <link rel="icon" type="image/png" href="pic/icon.png" />
    <link rel="stylesheet" href="lgn_style/style.css">
    </head>

    <style>
        
        .edit_delete button.small-btn {
    font-size: 0.75rem;      /* Менший шрифт */
    padding: 3px 8px;        /* Менші відступи */
    margin: 0 3px;
    cursor: pointer;
    border: 1px solid #666;
    border-radius: 3px;
    background-color: #f0f0f0;
    color: #333;
    transition: background-color 0.3s ease;
}

.edit_delete button.small-btn:hover {
    background-color: #ddd;
}

.edit_delete button.delete-btn {
    border-color: #cc0000;
    color: #cc0000;
}

.edit_delete button.delete-btn:hover {
    background-color: #fdd;
}

    </style>
    <body>

    <div class="block">
<div class="logo-text">
    <div class="logo-info">
        <a href="https://tehcollege.rv.ua">
            <img src="https://tehcollege.rv.ua/wp-content/uploads/2024/05/logo_blue.svg" class="logo" alt="Logo">
        </a>
        <p class="vsp-rtfk">Відокремлений структурний підрозділ <br>“Рівненський технічний фаховий коледж НУВГП”</p>

    </div>
    <a href="user_logout.php"><button class="button-1">Вихід</button></a>
</div>
</div>

<div class="homepage">
<h1 class="text-center">Оберіть групу:</h2>
<div class="groups-grid">
    <?php foreach ($grouped as $category => $items): ?>
        <details class="group-category">
            <summary class="category-title"><?= htmlspecialchars($category) ?></summary>
            <div class="group-block">
                <?php foreach ($items as $subgroup): ?>
                    <a href="userpage.php?group_id=<?= $subgroup['id'] ?>" class="group-button-link">
                        <button class="button-3 <?= ($filter_group == $subgroup['id']) ? 'active' : '' ?>">
                            <?= htmlspecialchars($subgroup['name']) ?>
                        </button>
                    </a>
                <?php endforeach; ?>
            </div>
        </details>
    <?php endforeach; ?>
</div>


</div>

<div class="homepage">

        <?php if ($filter_group): ?>
            <h2>Фільтр розкладу</h2>
            <form class="filter" method="GET">
                <input type="hidden" name="group_id" value="<?= htmlspecialchars($filter_group) ?>" />
                <div class="filter-item">
                    <label for="subject">Предмет:</label>
                    <select name="subject_id" id="subject">
                        <option value="">Всі</option>
                        <?php
                        $subject->data_seek(0);
                        while ($row = $subject->fetch_assoc()): ?>
                            <option value="<?= $row['id'] ?>" <?= ($row['id'] == $filter_subject) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($row['name']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="filter-item">
                    <label for="teacher">Викладач:</label>
                    <select name="teacher_id" id="teacher">
                        <option value="">Всі</option>
                        <?php
                        $teachers->data_seek(0);
                        while ($row = $teachers->fetch_assoc()): ?>
                            <option value="<?= $row['id'] ?>" <?= ($row['id'] == $filter_teacher) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($row['full_name']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="filter-item">
                    <label for="day">День тижня:</label>
                    <select name="day_of_week" id="day">
                        <option value="">Всі</option>
                        <?php
                        $days = ['Понеділок', 'Вівторок', 'Середа', 'Четвер', 'П’ятниця'];
                        foreach ($days as $day) {
                            $selected = ($filter_day == $day) ? 'selected' : '';
                            echo "<option value=\"$day\" $selected>$day</option>";
                        }
                        ?>
                    </select>
                </div>

                <button class="button-1" type="submit">Фільтрувати</button>
                <a href="homepage.php"><button class="button-1" type="button">Очистити фільтр</button></a>
            </form>

            <h2>Розклад занять</h2>
            <table border="1" cellpadding="10" cellspacing="0">
                <thead>
                    <tr>
                        <th>Номер пари</th>
                        <?php foreach ($days as $day): ?>
                            <th><?= htmlspecialchars($day) ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php for ($pair = 1; $pair <= 6; $pair++): ?>
    <tr>
        <td><strong>Пара <?= $pair ?></strong></td>
        <?php
foreach ($days as $day) {
    $result->data_seek(0);
$cell_content = "";

while ($row = $result->fetch_assoc()) {
    if ($row['day_of_week'] === $day && $row['lesson_number'] == $pair) {
        $cell_content .= "
            <div class='lesson-block'>
                <strong>" . htmlspecialchars($row['subject_name']) . "</strong><br>
                <small>" . htmlspecialchars($row['lesson_type']) . "</small><br>
                " . htmlspecialchars($row['teacher_name']) . "<br>
                <div class='classroom-info'>
                    <strong>" . htmlspecialchars($row['classroom_name']) . "</strong>
                    <button type='button' class='show-location' data-id='" . $row['id'] . "'>Локація</button>
                </div>
                <div class='location-info' id='location-" . $row['id'] . "' style='display:none;'>" . htmlspecialchars($row['classroom_location']) . "</div>
                <br>
                <div class='edit_delete'>
                    <form action='edit.php' method='get' style='display:inline'>
                        <input type='hidden' name='id' value='" . $row['id'] . "'>
                    </form>
                </div>
                <hr>
            </div>
        ";
    }
}

echo "<td>" . (!empty($cell_content) ? $cell_content : "-") . "</td>";

}
?>

    </tr>
<?php endfor; ?>

            </tbody>
        </table>
 <?php else: ?>
    <div style="display: flex; justify-content: center; align-items: center; height: 20px;">
        <p>Оберіть групу, щоб побачити розклад</p>
    </div>
<?php endif; ?>

 <div style="display: flex; justify-content: center; align-items: center;">


<?php if ($filter_group): ?>
    <button class="button-0" onclick="toggleModal()">Завантажити розклад</button>
    <div id="modalOverlay" class="modal-overlay" onclick="closeModal(event)">
        <div class="modal-content">
    <span class="close-button" onclick="toggleModal()">&times;</span>

    <p class="qrcode">Скануй QR-код для перегляду розкладу:</p>
    <img src="https://api.qrserver.com/v1/create-qr-code/?size=160x160&data=<?= urlencode("http://localhost/supernova/userpage.php?group_id=" . $filter_group) ?>" alt="QR-код" />

    <p class="qrcode">Завантажити розклад:</p>
    <div>
        <a href="share/export.php?group_id=<?= $filter_group ?>&format=pdf" target="_blank">
            <button class="button-1">PDF</button>
        </a>
        <a href="share/export.php?group_id=<?= $filter_group ?>&format=excel" target="_blank">
            <button class="button-1">Excel</button>
        </a>
    </div>
</div>

    </div>

    <script>
        function toggleModal() {
            const modal = document.getElementById('modalOverlay');
            modal.style.display = (modal.style.display === 'flex') ? 'none' : 'flex';
        }

        function closeModal(event) {
            if (event.target.id === 'modalOverlay') {
                toggleModal();
            }
        }
    </script>
<?php endif; ?>
     <a href="auth.php?group_id=<?= $filter_group ?>"><button class="button-0">Додати в Google Календар</button></a>
    <a href="login.php"><button class="button-0">Вхід в адмін панель</button></a>



</div>

    </div>
<footer class="footer">
        <div class="footer-section">
            <h1>Статистика</h1>
            <p>Сьогодні: <span id="today">0</span></p></br>
            <p>Вчора: <span id="yesterday">0</span></p></br>
            <p>Місяць: <span id="month">0</span></p></br>
            <p>Загалом: <span id="total">0</span></p>
        </div>
        
        <div class="footer-section">
            <h1>Контакти</h1>
            <p>33027, Україна, м. Рівне, вул. Вишиванка, 35</p></br>
            <p>Email: tehnich-college@nuwm.edu.ua</p></br>
            <p>Телефон: (0362) 64-34-03</p>

        </div>
        <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d20251.86126104479!2d26.278242!3d50.61815!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x472f135e0f1986dd%3A0x808322a33a9ed119!2z0JLQodCfICLQoNGW0LLQvdC10L3RgdGM0LrQuNC5INGC0LXRhdC90ZbRh9C90LjQuSDRhNCw0YXQvtCy0LjQuSDQutC-0LvQtdC00LYg0J3Qo9CS0JPQnyI!5e0!3m2!1suk!2sua!4v1748723264555!5m2!1suk!2sua" width="600" height="300" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
    </footer>

    <div class="copyright">
            Copyright 2025, ВСП "Рівненський технічний фаховий коледж НУВГП"!
        </div>  

<script type="text/javascript" src="script/ajax.js"></script>

    </body>
    </html>
