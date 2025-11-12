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

    if (!empty($filter_group)) $sql .= " AND schedule.group_id = '" . $conn->real_escape_string($filter_group) . "'";
    if (!empty($filter_subject)) $sql .= " AND schedule.subject_id = '" . $conn->real_escape_string($filter_subject) . "'";
    if (!empty($filter_teacher)) $sql .= " AND schedule.teacher_id = '" . $conn->real_escape_string($filter_teacher) . "'";
    if (!empty($filter_day)) $sql .= " AND schedule.day_of_week = '" . $conn->real_escape_string($filter_day) . "'";

    $result = $conn->query($sql);
    if (!$result) {
        die("Помилка SQL-запиту: " . $conn->error);
    }

    $groups_all = $conn->query("SELECT * FROM `groups`");
    $subject = $conn->query("SELECT * FROM `subject`");
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
        <link rel="icon" type="image/png" href="icon.png" />
        <link rel="stylesheet" href="test/style.css" />
    </head>
    <body>
    <div class="logo-text">
        <div class="logo-info">
            <a href="https://tehcollege.rv.ua">
                <img src="https://tehcollege.rv.ua/wp-content/uploads/2024/05/logo_blue.svg" class="logo" alt="Logo" />
            </a>
            <p class="vsp-rtfk">Відокремлений структурний підрозділ <br>“Рівненський технічний фаховий коледж НУВГП”</p>
        </div>
        <a href="rozklad.php">
          <button class="button-1" role="button">Додати</button>
        </a>
    </div>
<div class="homepage">
    <h3>Оберіть групу:</h3>
    <div class="group-categories">
        <?php foreach ($grouped as $category => $items): ?>
            <details class="group-category">
                <summary><?= htmlspecialchars($category) ?></summary>
                <div class="group-sublist-button-grid">
                    <?php foreach ($items as $subgroup): ?>
                        <a href="homepage.php?group_id=<?= $subgroup['id'] ?>">
                            <button class="button-0" <?= ($filter_group == $subgroup['id']) ? 'style="background-color: #0b63ce; color: white;"' : '' ?>>
                                <?= htmlspecialchars($subgroup['name']) ?>
                            </button>
                        </a>
                    <?php endforeach; ?>
                </div>
            </details>
        <?php endforeach; ?>
    <?php if ($filter_group): ?>


        <button class="button-1" onclick="toggleExportBlock()">Завантажити розклад</button>

        <div id="exportBlock" style="margin-top: 30px; display: none; gap: 40px; align-items: center;">
            <div>
                <p>Скануй QR-код для перегляду розкладу:</p>
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=160x160&data=<?= urlencode("https://diploma.com/homepage.php?group_id=" . $filter_group) ?>" alt="QR-код" />
            </div>

            <div>
                <p>Завантажити розклад:</p>
                <a href="share/export.php?group_id=<?= $filter_group ?>&format=pdf" target="_blank">
                    <button class="button-2">PDF</button>
                </a>
                <a href="share/export.php?group_id=<?= $filter_group ?>&format=excel" target="_blank">
                    <button class="button-1">Excel</button>
                </a>
            </div>
        </div>

        <script>
            function toggleExportBlock() {
                const block = document.getElementById('exportBlock');
                block.style.display = (block.style.display === 'none' || block.style.display === '') ? 'flex' : 'none';
            }
        </script>
    <?php endif; ?>

        </div>

</div>
</body>
</html>
