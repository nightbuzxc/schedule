<?php
include 'db.php';


function h($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

if (isset($_POST['group'])) {
    $groupName = $_POST['group'];

    $stmt = $pdo->prepare("SELECT * FROM schedule WHERE group_name = ?");
    $stmt->execute([$groupName]);
    $schedule = $stmt->fetchAll();

    header('Content-Type: application/json');
    echo json_encode($schedule);
    exit;
}

$groups = $conn->query("SELECT * FROM `groups`");
$lesson_numbers = $conn->query("SELECT id, lesson_number FROM lesson_number ORDER BY lesson_number");
$days = ["Понеділок", "Вівторок", "Середа", "Четвер", "П'ятниця", "Субота"];

echo '<table border="1" cellpadding="5" cellspacing="0">';
echo '<thead><tr><th>№ пари</th><th>Група</th>';

foreach ($days as $day) {
    echo "<th>$day</th>";
}
echo '</tr></thead><tbody>';

while ($lesson = $lesson_numbers->fetch_assoc()) {
    $lesson_id = $lesson['id'];
    $lesson_num = $lesson['lesson_number'];
    foreach ($groups as $group) {
        echo '<tr>';
        echo "<td>$lesson_num</td>";
        echo "<td>" . h($group['name']) . "</td>";
        for ($day = 1; $day <= 6; $day++) {
            $stmt = $conn->prepare("
                SELECT s.name AS subject
                FROM schedule sch
                LEFT JOIN subject s ON sch.subject_id = s.id
                WHERE sch.group_id = ? AND sch.day_of_week = ? AND sch.lesson_number_id = ? LIMIT 1
            ");
            $stmt->bind_param("iii", $group['id'], $day, $lesson_id);
            $stmt->execute();
            $res = $stmt->get_result();
            $row = $res->fetch_assoc();
            echo "<td>" . h($row['subject'] ?? '-') . "</td>";
        }
        echo '</tr>';
    }
}

echo '</tbody></table>';
?>
