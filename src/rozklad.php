<?php
include 'db.php';

$filter_group = '';
if (!empty($_GET['group_id'])) {
    $filter_group = '?group_id=' . intval($_GET['group_id']);
}

function h($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['add_year'])) {
        $name = trim($_POST['year_name']);
        if (empty($name)) {
            echo "Назва року не може бути порожньою.";
        } else {
            $stmt = $conn->prepare("INSERT INTO academic_years (year_name) VALUES (?)");
            $stmt->bind_param("s", $name);
            echo $stmt->execute() ? "Навчальний рік успішно додано." : "Помилка: " . $stmt->error;
            $stmt->close();
        }
    }

    if (isset($_POST['add_group'])) {
        $name = trim($_POST['group_name']);
        if (empty($name)) {
            echo "Назва групи не може бути порожньою.";
        } else {
            $stmt = $conn->prepare("INSERT INTO `groups` (name) VALUES (?)");
            $stmt->bind_param("s", $name);
            echo $stmt->execute() ? "Група успішно додана." : "Помилка: " . $stmt->error;
            $stmt->close();
        }
    }

    if (isset($_POST['add_subject'])) {
        $name = trim($_POST['subject_name']);
        $teacher_id = $_POST['subject_teacher_id'];
        $classroom_id = $_POST['subject_classroom_id'];

        if (empty($name) || empty($teacher_id) || empty($classroom_id)) {
            echo "Усі поля предмету обовʼязкові!";
        } else {
            $stmt = $conn->prepare("INSERT INTO subject (name, teacher_id, classroom_id) VALUES (?, ?, ?)");
            $stmt->bind_param("sii", $name, $teacher_id, $classroom_id);
            echo $stmt->execute() ? "Предмет успішно додано." : "Помилка: " . $stmt->error;
            $stmt->close();
        }
    }

    if (isset($_POST['add_teacher'])) {
        $name = trim($_POST['teacher_name']);
        if (empty($name)) {
            echo "ПІБ викладача не може бути порожнім.";
        } else {
            $stmt = $conn->prepare("INSERT INTO teachers (full_name) VALUES (?)");
            $stmt->bind_param("s", $name);
            echo $stmt->execute() ? "Викладач успішно доданий." : "Помилка: " . $stmt->error;
            $stmt->close();
        }
    }

    if (isset($_POST['add_classroom'])) {
        $name = trim($_POST['classroom_name']);
        $head_teacher = $_POST['classroom_head_teacher'];
        $location = trim($_POST['classroom_location']);

        if (empty($name) || empty($head_teacher) || empty($location)) {
            echo "Усі поля кабінету обовʼязкові.";
        } elseif (!is_numeric($head_teacher)) {
            echo "ID викладача має бути числовим.";
        } else {
            $check = $conn->prepare("SELECT id FROM teachers WHERE id = ?");
            $check->bind_param("i", $head_teacher);
            $check->execute();
            $result = $check->get_result();

            if ($result->num_rows == 0) {
                echo "Помилка: викладач із таким ID не існує.";
            } else {
                $stmt = $conn->prepare("INSERT INTO classroom (name, head_teacher, location) VALUES (?, ?, ?)");
                $stmt->bind_param("sis", $name, $head_teacher, $location);
                echo $stmt->execute() ? "Кабінет успішно додано." : "Помилка: " . $stmt->error;
                $stmt->close();
            }
            $check->close();
        }
    }

    if (isset($_POST['add_lesson_number'])) {
        $number = trim($_POST['lesson_number']);
        if (empty($number)) {
            echo "Номер пари не може бути порожнім.";
        } else {
            $stmt = $conn->prepare("INSERT INTO lesson_number (lesson_number) VALUES (?)");
            $stmt->bind_param("s", $number);
            echo $stmt->execute() ? "Номер пари успішно додано." : "Помилка: " . $stmt->error;
            $stmt->close();
        }
    }

    if (isset($_POST['add_schedule'])) {
        $required_fields = ['academic_year_id', 'group_id', 'subject_id', 'teacher_id', 'classroom_id', 'lesson_number_id', 'day_of_week', 'lesson_type'];
        foreach ($required_fields as $field) {
            if (empty($_POST[$field])) {
                echo "Усі поля розкладу мають бути заповнені.";
                exit;
            }
        }

        $stmt = $conn->prepare("INSERT INTO schedule (academic_year_id, group_id, subject_id, teacher_id, classroom_id, lesson_number_id, day_of_week, lesson_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param(
            "iiiiisss",
            $_POST['academic_year_id'],
            $_POST['group_id'],
            $_POST['subject_id'],
            $_POST['teacher_id'],
            $_POST['classroom_id'],
            $_POST['lesson_number_id'],
            $_POST['day_of_week'],
            $_POST['lesson_type']
        );
        echo $stmt->execute() ? "Розклад успішно збережено." : "Помилка: " . $stmt->error;
        $stmt->close();
    }
}

$years = $conn->query("SELECT * FROM academic_years");
$groups = $conn->query("SELECT * FROM `groups`");
$subject = $conn->query("SELECT * FROM `subject`");
$teachers = $conn->query("SELECT * FROM teachers");
$classroom = $conn->query("SELECT * FROM classroom");
$lesson_number = $conn->query("SELECT * FROM lesson_number");
?>


<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Керування розкладом</title>
        <link rel="stylesheet" href="lgn_style/style.css">
    
</head>

<style>
  .scrollable-table {
  max-width: 100%;
  max-height: 250px;
  overflow: auto;
  margin: 5px auto 10px;
  background: #fff;
  border: 1px solid #ccc;
  border-radius: 1px;
  padding: 4px;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.03);
  display: none;
  transition: all 0.2s ease;
}

.scrollable-table table {
  border-collapse: collapse;
  width: 100%;
  min-width: 320px;
  font-size: 11px;
}

.scrollable-table th, .scrollable-table td {
  padding: 4px 6px;
  border: 1px solid #bbb;
  text-align: center;
}

.scrollable-table th {
  background: #5183B8;
  color: #fff;
  position: sticky;
  top: 0px;
  z-index: 1;
  font-size: 11px;
}

.right-panel h2 {
  text-align: center;
  margin: 10px auto 5px;
  cursor: pointer;
  background: #5183B8;
  color: #fff;
  padding: 6px 10px;
  border-radius: 2px;
  width: max-content;
  min-width: 500px;
  font-size: 15px;
  transition: background 0.2s, color 0.2s;
}

.right-panel h2:hover {
  background: #fff;
  color: #5183B8;
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
    <a href="homepage.php"><button class="button-1">Повернутися</button></a>
    <a href="add.php"><button class="button-1">Додати елементи</button></a>
</div>
</div>

<div class="container">
    <div class="right-panel">

<h2 data-toggle="subjectTable">Розклад предметів</h2>
<div class="scrollable-table" id="subjectTable">

<table border="1" cellpadding="5" cellspacing="0">
    <thead>
        <tr>
            <th>№ пари</th>
            <th>Група</th>
            <?php
            $days = ['Понеділок', 'Вівторок', 'Середа', 'Четвер', 'П’ятниця'];
            foreach ($days as $day) {
                echo "<th>$day</th>";
            }
            ?>
        </tr>
    </thead>
    <tbody>
        <?php
        $lessons = $conn->query("SELECT id, lesson_number FROM lesson_number ORDER BY lesson_number");
        if (!$lessons) die("Помилка lesson_number: " . $conn->error);

        $groups = $conn->query("SELECT id, name FROM `groups` ORDER BY name");
        if (!$groups) die("Помилка groups: " . $conn->error);

        $group_count = $groups->num_rows;

        while ($lesson = $lessons->fetch_assoc()) {
            $lesson_id = $lesson['id'];
            $lesson_num = $lesson['lesson_number'];

            $groups->data_seek(0);

            $first = true;

            while ($group = $groups->fetch_assoc()) {
                echo "<tr>";
                if ($first) {
                    echo "<td rowspan='$group_count'>" . h($lesson_num) . "</td>";
                    $first = false;
                }

                echo "<td>" . h($group['name']) . "</td>";

                for ($day = 1; $day <= count($days); $day++) {
                    $stmt = $conn->prepare("
                        SELECT s.name AS subject
                        FROM schedule sch
                        LEFT JOIN subject s ON sch.subject_id = s.id
                        WHERE sch.group_id = ? AND sch.day_of_week = ? AND sch.lesson_number_id = ?
                        LIMIT 1
                    ");
                    $stmt->bind_param("iii", $group['id'], $day, $lesson_id);
                    $stmt->execute();
                    $res = $stmt->get_result();
                    $row = $res->fetch_assoc();

                    echo "<td>" . h($row['subject'] ?? '-') . "</td>";
                }

                echo "</tr>";
            }
        }
        ?>
    </tbody>
</table>
</div>

<h2 data-toggle="teacherTable">Розклад викладачів</h2>
<div class="scrollable-table" id="teacherTable">
<table border="1" cellpadding="5" cellspacing="0">
    <thead>
        <tr>
            <th>№ пари</th>
            <th>Група</th>
            <?php
            $days = ['Понеділок', 'Вівторок', 'Середа', 'Четвер', 'П’ятниця'];
            foreach ($days as $day) {
                echo "<th>$day</th>";
            }
            ?>
        </tr>
    </thead>
    <tbody>
        <?php
        $lessons = $conn->query("SELECT id, lesson_number FROM lesson_number ORDER BY lesson_number");
        if (!$lessons) die("Помилка lesson_number: " . $conn->error);

        $groups = $conn->query("SELECT id, name FROM `groups` ORDER BY name");
        if (!$groups) die("Помилка groups: " . $conn->error);

        $group_count = $groups->num_rows;

        while ($lesson = $lessons->fetch_assoc()) {
            $lesson_id = $lesson['id'];
            $lesson_num = $lesson['lesson_number'];

            $groups->data_seek(0);

            $first = true;

            while ($group = $groups->fetch_assoc()) {
                echo "<tr>";
                if ($first) {
                    echo "<td rowspan='$group_count'>" . h($lesson_num) . "</td>";
                    $first = false;
                }

                echo "<td>" . h($group['name']) . "</td>";

                for ($day = 1; $day <= count($days); $day++) {
    $stmt = $conn->prepare("
        SELECT t.full_name AS teachers
        FROM schedule sch
        LEFT JOIN subject s ON sch.subject_id = s.id
        LEFT JOIN teachers t ON s.teacher_id = t.id
        WHERE sch.group_id = ? AND sch.day_of_week = ? AND sch.lesson_number_id = ?
        LIMIT 1
    ");

    if (!$stmt) {
        die("Помилка підготовки запиту: " . $conn->error);
    }

    $stmt->bind_param("iii", $group['id'], $day, $lesson_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();

    echo "<td>" . h($row['teachers'] ?? '-') . "</td>";
}

                echo "</tr>";
            }
        }
        ?>
    </tbody>
</table>
</div>

<h2 data-toggle="classroomTable">Розклад кабінетів</h2>
<div class="scrollable-table" id="classroomTable">
<table border="1" cellpadding="5" cellspacing="0">
    <thead>
        <tr>
            <th>№ пари</th>
            <th>Група</th>
            <?php
            $days = ['Понеділок', 'Вівторок', 'Середа', 'Четвер', 'П’ятниця'];
            foreach ($days as $day) {
                echo "<th>$day</th>";
            }
            ?>
        </tr>
    </thead>
    <tbody>
        <?php
        $lessons = $conn->query("SELECT id, lesson_number FROM lesson_number ORDER BY lesson_number");
        if (!$lessons) die("Помилка lesson_number: " . $conn->error);

        $groups = $conn->query("SELECT id, name FROM `groups` ORDER BY name");
        if (!$groups) die("Помилка groups: " . $conn->error);

        $group_count = $groups->num_rows;

        while ($lesson = $lessons->fetch_assoc()) {
            $lesson_id = $lesson['id'];
            $lesson_num = $lesson['lesson_number'];

            $groups->data_seek(0);

            $first = true;

            while ($group = $groups->fetch_assoc()) {
                echo "<tr>";
                if ($first) {
                    echo "<td rowspan='$group_count'>" . h($lesson_num) . "</td>";
                    $first = false;
                }

                echo "<td>" . h($group['name']) . "</td>";

                for ($day = 1; $day <= count($days); $day++) {
                    $stmt = $conn->prepare("
                        SELECT c.name AS classroom
                        FROM schedule sch
                        LEFT JOIN subject s ON sch.subject_id = s.id
                        LEFT JOIN classroom c ON s.classroom_id = c.id
                        WHERE sch.group_id = ? AND sch.day_of_week = ? AND sch.lesson_number_id = ?
                        LIMIT 1
                    ");
                    if (!$stmt) {
                        echo "<td>Помилка запиту</td>";
                        continue;
                    }

                    $stmt->bind_param("iii", $group['id'], $day, $lesson_id);
                    $stmt->execute();
                    $res = $stmt->get_result();
                    $row = $res->fetch_assoc();

                    echo "<td>" . h($row['classroom'] ?? '-') . "</td>";
                }

                echo "</tr>";
            }
        }
        ?>
    </tbody>
</table>
</div>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('h2[data-toggle]').forEach(function (header) {
      header.addEventListener('click', function () {
        const targetId = this.getAttribute('data-toggle');
        const table = document.getElementById(targetId);
        if (table.style.display === 'none' || table.style.display === '') {
          table.style.display = 'block';
        } else {
          table.style.display = 'none';
        }
      });
    });
  });
</script>

        </div>


<div class="left-panel">
        <div class="form-box">
            <h2>Додати розклад</h2>
            <form method="post">
                <label>Навчальний рік:</label>
                <select name="academic_year_id">
                    <?php $years->data_seek(0); while ($row = $years->fetch_assoc()): ?>
                        <option value="<?= $row['id'] ?>"><?= h($row['year_name']) ?></option>
                    <?php endwhile; ?>
                </select>

                <label>Група:</label>
                <select name="group_id">
                    <?php $groups->data_seek(0); while ($row = $groups->fetch_assoc()): ?>
                        <option value="<?= $row['id'] ?>"><?= h($row['name']) ?></option>
                    <?php endwhile; ?>
                </select>

                <label>Предмет:</label>
                <select name="subject_id" id="subject_id">
    <option value="">Оберіть предмет</option>
    <?php while($row = $subject->fetch_assoc()): ?>
        <option value="<?= $row['id'] ?>"><?= h($row['name']) ?></option>
    <?php endwhile; ?>
</select>

                <label>Викладач:</label>
                <select name="teacher_id" id="teacher_id">
    <?php while($row = $teachers->fetch_assoc()): ?>
        <option value="<?= $row['id'] ?>"><?= h($row['full_name']) ?></option>
    <?php endwhile; ?>
</select>

                <label>Кабінет:</label>
                <select name="classroom_id" id="classroom_id">
    <?php while($row = $classroom->fetch_assoc()): ?>
        <option value="<?= $row['id'] ?>"><?= h($row['name']) ?> (<?= h($row['location']) ?>)</option>
    <?php endwhile; ?>
</select>

                <label>Номер пари:</label>
                <select name="lesson_number_id">
                    <?php $lesson_number->data_seek(0); while ($row = $lesson_number->fetch_assoc()): ?>
                        <option value="<?= $row['id'] ?>"><?= h($row['lesson_number']) ?></option>
                    <?php endwhile; ?>
                </select>

                <label>День тижня:</label>
                <select name="day_of_week">
                    <?php foreach (['Понеділок', 'Вівторок', 'Середа', 'Четвер', 'П’ятниця', 'Субота'] as $day): ?>
                        <option value="<?= $day ?>"><?= $day ?></option>
                    <?php endforeach; ?>
                </select>

                <label>Тип пари:</label>
                <select name="lesson_type">
                    <?php foreach (['Повна', 'Підгрупа', 'Чисельник', 'Знаменник'] as $type): ?>
                        <option value="<?= $type ?>"><?= $type ?></option>
                    <?php endforeach; ?>
                </select>

                <button class="button-0"type="submit" name="add_schedule">Зберегти розклад</button>
            </form>
        </div>
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
