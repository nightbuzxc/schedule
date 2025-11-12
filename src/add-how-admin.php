<?php
include 'db.php';

$filter_group = $_GET['group_id'] ?? '';
$filter_subject = $_GET['subject_id'] ?? '';
$filter_teacher = $_GET['teacher_id'] ?? '';
$filter_day = $_GET['day_of_week'] ?? '';

$sql = "SELECT 
            schedule.id, 
            academic_years.year_name AS academic_year, 
            groups.name AS group_name, 
            `subject`.name AS subject_name, 
            teachers.full_name AS teacher_name, 
            classroom.name AS classroom_name, 
            classroom.location AS classroom_location,
            lesson_number.lesson_number AS lesson_number, 
            schedule.day_of_week, 
            schedule.lesson_type 
        FROM schedule
        LEFT JOIN academic_years ON schedule.academic_year_id = academic_years.id
        LEFT JOIN `groups` ON schedule.group_id = groups.id
        LEFT JOIN `subject` ON schedule.subject_id = `subject`.id
        LEFT JOIN teachers ON schedule.teacher_id = teachers.id
        LEFT JOIN classroom ON schedule.classroom_id = classroom.id
        LEFT JOIN lesson_number ON schedule.lesson_number_id = lesson_number.id
        WHERE 1";

if (!empty($filter_group)) $sql .= " AND schedule.group_id = '$filter_group'";
if (!empty($filter_subject)) $sql .= " AND schedule.subject_id = '$filter_subject'";
if (!empty($filter_teacher)) $sql .= " AND schedule.teacher_id = '$filter_teacher'";
if (!empty($filter_day)) $sql .= " AND schedule.day_of_week = '$filter_day'";

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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Навчальний розклад ВСП "РТФК" НУВГП</title>
    <link rel="icon" type="image/png" href="pic/icon.png">
    <link rel="stylesheet" href="lgn_style/style.css">
</head>
<body>

<div class="block">
<div class="logo-text">
    <div class="logo-info">
        <a href="https://tehcollege.rv.ua">
            <img src="https://tehcollege.rv.ua/wp-content/uploads/2024/05/logo_blue.svg" class="logo" alt="Logo">
        </a>
        <p class="vsp-rtfk">Відокремлений структурний підрозділ <br>“Рівненський технічний фаховий коледж НУВГП”</p>
    </div>
    <a href="homepage.php"><button class="button-1">Повернутись</button></a>
</div>
</div>

<div class="homepage">
<form action="auth.php" method="post">
    <label>Оберіть період:</label>
    <select name="range" id="range">
        <option value="week">Тиждень</option>
        <option value="month">Місяць</option>
        <option value="3months">3 місяці</option>
        <option value="semester">Семестр</option>
        <option value="custom">Свій період</option>
    </select>
    <div id="custom-dates" style="display:none;">
        <label>Початок:</label>
        <input type="date" name="start_date">
        <label>Кінець:</label>
        <input type="date" name="end_date">
    </div>
    <button class="button-0" type="submit">Додати в Google Календар</button>
</form>

<script>
    document.getElementById('range').addEventListener('change', function() {
        document.getElementById('custom-dates').style.display = this.value === 'custom' ? 'block' : 'none';
    });
</script>
</div>

<footer class="footer">
        <div class="footer-section">
    <h1>Статистика</h1>
    <p>Сьогодні: <span id="today">0</span></p><br>
    <p>Вчора: <span id="yesterday">0</span></p><br>
    <p>Місяць: <span id="month">0</span></p><br>
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