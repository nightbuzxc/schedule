<?php
require_once 'vendor/autoload.php';
include 'db.php';
session_start();

if (!isset($_SESSION['access_token'])) {
    header('Location: auth.php');
    exit;
}

$client = new Google_Client();
$client->setAuthConfig('credentials.json');
$client->setAccessToken($_SESSION['access_token']);

if ($client->isAccessTokenExpired()) {
    if ($client->getRefreshToken()) {
        $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
        $_SESSION['access_token'] = $client->getAccessToken();
    } else {
        header('Location: auth.php');
        exit;
    }
}

$service = new Google_Service_Calendar($client);
$group_id = intval($_SESSION['group_id'] ?? 0);
if (!$group_id) {
    header('Location: userpage.php');
    exit;
}

$stmt = $conn->prepare("SELECT 
    schedule.id, 
    academic_years.year_name AS academic_year, 
    groups.name AS group_name, 
    `subject`.name AS subject_name, 
    teachers.full_name AS teacher_name, 
    classroom.name AS classroom_ename, 
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
WHERE schedule.group_id = ?");
$stmt->bind_param('i', $group_id);
$stmt->execute();
$result = $stmt->get_result();

$calendarId = 'primary';

function getDateTimeForLesson($dayOfWeek, $lessonNumber, $baseDate) {
    $daysMap = ['Понеділок' => 0, 'Вівторок' => 1, 'Середа' => 2, 'Четвер' => 3, 'П’ятниця' => 4];
    $startTimes = [1 => '09:00', 2 => '10:30', 3 => '12:10', 4 => '13:40', 5 => '15:10', 6 => '16:40'];

    if (!isset($daysMap[$dayOfWeek]) || !isset($startTimes[$lessonNumber])) return null;

    $date = clone $baseDate;
    $date->modify("+{$daysMap[$dayOfWeek]} days");
    $startTimeStr = $date->format('Y-m-d') . 'T' . $startTimes[$lessonNumber] . ':00';

    try {
        $startDateTime = new DateTime($startTimeStr);
    } catch (Exception $e) {
        return null;
    }

    $endDateTime = clone $startDateTime;
    $endDateTime->modify('+1 hour 20 minutes');

    return ['start' => $startDateTime, 'end' => $endDateTime];
}

function getDateRange($rangeOption) {
    $today = new DateTime();
    $start = clone $today;
    $end = clone $today;

    switch ($rangeOption) {
        case 'week':
            $end->modify('+6 days');
            break;
        case 'month':
            $end->modify('+1 month');
            break;
        case '3months':
            $end->modify('+3 months');
            break;
        case 'semester':
            $start = new DateTime('2025-09-01');
            $end = new DateTime('2025-12-31');
            break;
        case 'custom':
            $start = new DateTime($_SESSION['start_date']);
            $end = new DateTime($_SESSION['end_date']);
            break;
    }
    return [$start, $end];
}

list($startDate, $endDate) = getDateRange($_SESSION['range']);
$interval = new DateInterval('P1D');
$period = new DatePeriod($startDate, $interval, (clone $endDate)->modify('+1 day'));

$daysMap = [
    'Понеділок' => 0,
    'Вівторок' => 1,
    'Середа' => 2,
    'Четвер' => 3,
    'П’ятниця' => 4,
];

while ($row = $result->fetch_assoc()) {
    foreach ($period as $date) {
        $weekdayNum = (int)$date->format('N');
        if ($weekdayNum == ($daysMap[$row['day_of_week']] ?? 0)) {
            $times = getDateTimeForLesson($row['day_of_week'], $row['lesson_number'], $date);
            if (!$times) continue;

            $event = new Google_Service_Calendar_Event([
                'summary' => $row['subject_name'] . ' (' . $row['lesson_type'] . ')',
                'location' => $row['classroom_location'],
                'description' => "Викладач: " . $row['teacher_name'] . "\nКабінет: " . $row['classroom_ename'],
                'start' => ['dateTime' => $times['start']->format(DateTime::RFC3339), 'timeZone' => 'Europe/Kiev'],
                'end' => ['dateTime' => $times['end']->format(DateTime::RFC3339), 'timeZone' => 'Europe/Kiev'],
                'reminders' => ['useDefault' => true],
            ]);

            try {
                $service->events->insert($calendarId, $event);
            } catch (Exception $e) {
                echo '<div style="color:red">Помилка додавання події: ', htmlspecialchars($e->getMessage()), '</div>';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Додавання в Google Календар</title>
    <link rel="icon" type="image/png" href="icon.png">
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
        </div>
    </div>

    <div class="calendar-status">
        <h1 class="text-center">Розклад успішно додано до вашого Google Календаря</h1>
        <a href="userpage.php" class="backbttn"><button class="button-1">Повернутися</button></a>
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
            <p>33027, Україна, м. Рівне, вул. Вишиванка, 35</p><br>
            <p>Email: tehnich-college@nuwm.edu.ua</p><br>
            <p>Телефон: (0362) 64-34-03</p>
        </div>

        <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d20251.86126104479!2d26.278242!3d50.61815!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x472f135e0f1986dd%3A0x808322a33a9ed119!2z0JLQodCfICLQoNGW0LLQvdC10L3RgdGM0LrQuNC5INGC0LXRhdC90ZbRh9C90LjQuSDRhNCw0YXQvtCy0LjQuSDQutC-0LvQtdC00LYg0J3Qo9CS0JPQnyI!5e0!3m2!1suk!2sua!4v1748723264555!5m2!1suk!2sua" width="600" height="300" style="borhttps://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d20251.86126104479!2d26.278242!3d50.61815!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x472f135e0f1986dd%3A0x808322a33a9ed119!2z0JLQodCfICLQoNGW0LLQvdC10L3RgdGM0LrQuNC5INGC0LXRhdC90ZbRh9C90LjQuSDRhNCw0YXQvtCy0LjQuSDQutC-0LvQtdC00LYg0J3Qo9CS0JPQnyI!5e0!3m2!1suk!2sua!4v1748723264555!5m2!1suk!2suader:0;" allowfullscreen="" loading="lazy"></iframe>
    </footer>

    <div class="copyright">
        Copyright 2025, ВСП "Рівненський технічний фаховий коледж НУВГП"!
    </div>

    <script type="text/javascript" src="script/ajax.js"></script>
</body>
</html>
