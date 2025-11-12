<?php
include 'db.php';

function h($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_year'])) {
        $name = $_POST['year_name'];
        if ($conn->query("INSERT INTO academic_years (year_name) VALUES ('$name')")) {
            echo "Навчальний рік успішно додано.";
        } else {
            echo "Помилка: " . $conn->error;
        }
    }

    if (isset($_POST['add_group'])) {
        $name = $_POST['group_name'];
        if ($conn->query("INSERT INTO `groups` (name) VALUES ('$name')")) {
            echo "Група успішно додана.";
        } else {
            echo "Помилка: " . $conn->error;
        }
    }

    if (isset($_POST['add_subject'])) {
        $name = $_POST['subject_name'];
        $teacher_id = $_POST['subject_teacher_id'];
        $classroom_id = $_POST['subject_classroom_id'];

        if (empty($teacher_id) || empty($classroom_id)) {
            echo "Викладач або кабінет не вибрані!";
        } else {
            $stmt = $conn->prepare("INSERT INTO subject (name, teacher_id, classroom_id) VALUES (?, ?, ?)");
            if ($stmt === false) {
                echo "Помилка підготовки запиту: " . $conn->error;
            } else {
                $stmt->bind_param("sii", $name, $teacher_id, $classroom_id);

                if ($stmt->execute()) {
                    echo "Предмет успішно додано.";
                } else {
                    echo "Помилка: " . $stmt->error;
                }

                $stmt->close();
            }
        }
    }

    if (isset($_POST['add_teacher'])) {
        $name = $_POST['teacher_name'];
        if ($conn->query("INSERT INTO teachers (full_name) VALUES ('$name')")) {
            echo "Викладач успішно доданий.";
        } else {
            echo "Помилка: " . $conn->error;
        }
    }

    if (isset($_POST['add_classroom'])) {
        $name = $_POST['classroom_name'];
        $head_teacher = $_POST['classroom_head_teacher'];
        $location = $_POST['classroos_location']; 
        if (!is_numeric($head_teacher)) {
            echo "ID викладача має бути числовим.";
            return;
        }
    
        $check = $conn->prepare("SELECT id FROM teachers WHERE id = ?");
        $check->bind_param("i", $head_teacher);
        $check->execute();
        $result = $check->get_result();
    
        if ($result->num_rows == 0) {
            echo "Помилка: викладач із таким ID не існує.";
        } else {
            $stmt = $conn->prepare("INSERT INTO classroom (name, head_teacher, location) VALUES (?, ?, ?)");
            if ($stmt === false) {
                echo "Помилка підготовки запиту: " . $conn->error;
            } else {
                $stmt->bind_param("sis", $name, $head_teacher, $location);
    
                if ($stmt->execute()) {
                    echo "Кабінет успішно додано.";
                } else {
                    echo "Помилка: " . $stmt->error;
                }
    
                $stmt->close();
            }
        }
    
        $check->close();
    }
    

    if (isset($_POST['add_lesson_number'])) {
        $number = $_POST['lesson_number'];
        if ($conn->query("INSERT INTO lesson_number (lesson_number) VALUES ('$number')")) {
            echo "Номер пари успішно додано.";
        } else {
            echo "Помилка: " . $conn->error;
        }
    }

    if (isset($_POST['add_schedule'])) {
        $stmt = $conn->prepare("INSERT INTO schedule (academic_year_id, group_id, subject_id, teacher_id, classroom_id, lesson_number_id, day_of_week, lesson_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt === false) {
            echo "Помилка підготовки запиту: " . $conn->error;
        } else {
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

            if ($stmt->execute()) {
                echo "Розклад успішно збережено.";
            } else {
                echo "Помилка: " . $stmt->error;
            }

            $stmt->close();
        }
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
    <title>Керування розкладом</title>
    <link rel="icon" type="image/png" href="pic/icon.png" />
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
    <a href="rozklad.php"><button class="button-1">Повернутись</button></a>
</div>
  </div>
</div>
    

  <div class="add-container">

<div class="add">
     <a href="rozklad.php"><button class="button-0">Повернутися</button></a>
    <h2>Додавання розкладу</h2>

    <form method="POST">
      <label for="year_name">Навчальний рік:</label>
      <input type="text" name="year_name" required>
      <button type="submit" name="add_year" class="button-0">Додати рік</button>
    </form>

    <form method="POST">
      <label for="group_name">Група:</label>
      <input type="text" name="group_name" required>
      <button type="submit" name="add_group" class="button-0">Додати групу</button>
    </form>

    <h2>Додати новий навчальний рік</h2>
        <form method="post">
            <label>Назва року:</label>
            <input type="text" name="year_name" required>
            <button class="button-0" type="submit" name="add_year">Додати</button>
        </form>

        <h2>Додати нову групу</h2>
        <form method="post">
            <label>Назва групи:</label>
            <input type="text" name="group_name" required>
            <button class="button-0"  type="submit" name="add_group">Додати</button>
        </form>
        
        <h2>Додати нового викладача</h2>
        <form method="post">
            <label>ПІБ:</label>
            <input type="text" name="teacher_name" required>
            <button class="button-0"  type="submit" name="add_teacher">Додати</button>
        </form>

</div>

<div class="add0">
        <h2>Додати новий кабінет</h2>
        <form method="post">
            <label>Назва:</label>
            <input type="text" name="classroom_name" required>
            <label>Завідувач:</label>
                <select name="classroom_head_teacher">
                <?php $teachers->data_seek(0); while ($row = $teachers->fetch_assoc()): ?>
                <option value="<?= $row['id'] ?>"><?= $row['full_name'] ?></option>
                <?php endwhile; ?>
            </select>

            <label>Місце розташування:</label>
            <input type="text" name="classroom_location">
            <button class="button-0"  type="submit" name="add_classroom">Додати</button>
        </form>

        <h2>Додати новий предмет</h2>
        <form method="post">
            <label>Назва предмету:</label>
            <input type="text" name="subject_name" required>
    
            <label>Викладач:</label>
            <select name="subject_teacher_id">
                <?php $teachers->data_seek(0); while ($row = $teachers->fetch_assoc()): ?>
                    <option value="<?= $row['id'] ?>"><?= $row['full_name'] ?></option>
                <?php endwhile; ?>
            </select>

            <label>Кабінет:</label>
            <select name="subject_classroom_id">
                <?php $classroom->data_seek(0); while ($row = $classroom->fetch_assoc()): ?>
                    <option value="<?= $row['id'] ?>"><?= $row['name'] ?></option>
                <?php endwhile; ?>
            </select>

            <button class="button-0"  type="submit" name="add_subject">Додати</button>
        </form>

        <h2>Додати номер пари</h2>
        <form method="post">
            <label>Номер:</label>
            <input type="number" name="lesson_number" required>
            <button class="button-0"  type="submit" name="add_lesson_number">Додати</button>
        </form>

        <form action="import_excel.php" method="post" enctype="multipart/form-data">
  <label for="excelFile">Імпортувати Excel файл (.xlsx) з предметами та викладачами:</label><br>
  <input type="file" name="excelFile" accept=".xlsx" required>
  <button class="button-0" type="submit">Імпортувати</button>
</form>
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

<script type="text/javascript" src="ajax.js"></script>

</body>
</html>
