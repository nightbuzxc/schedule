<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}
include 'db.php';

$id = $_GET['id'];
$result = $conn->query("SELECT * FROM schedule WHERE id = $id");
$record = $result->fetch_assoc();

// Отримання довідкових даних
$years = $conn->query("SELECT * FROM academic_years");
$groups = $conn->query("SELECT * FROM `groups` ");
$subjects = $conn->query("SELECT * FROM `subject` ");
$teachers = $conn->query("SELECT * FROM teachers");
$classrooms = $conn->query("SELECT * FROM classroom");
$lessons = $conn->query("SELECT * FROM lesson_number");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $academic_year_id = $_POST['academic_year_id'];
    $group_id = $_POST['group_id'];
    $subject_id = $_POST['subject_id'];
    $teacher_id = $_POST['teacher_id'];
    $classroom_id = $_POST['classroom_id'];
    $lesson_number_id = $_POST['lesson_number_id'];
    $day_of_week = $_POST['day_of_week'];
    $lesson_type = $_POST['lesson_type'];

    $conn->query("UPDATE schedule SET 
        academic_year_id='$academic_year_id', 
        group_id='$group_id', 
        subject_id='$subject_id', 
        teacher_id='$teacher_id', 
        classroom_id='$classroom_id', 
        lesson_number_id='$lesson_number_id', 
        day_of_week='$day_of_week', 
        lesson_type='$lesson_type' 
        WHERE id=$id");

    header("Location: homepage.php?group_id=$group_id");
    exit;
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Навчальний розклад ВСП "РТФК" НУВГП</title>
    <link rel="icon" type="image/png" href="icon.png" />
    <link rel="stylesheet" href="lgn_style/style.css" />
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
    <a href="rozklad.php">
    </a>
</div>
</div>
</div>
    

<div class="homepage">

<form method="POST" class="form-container">
    <div class="form-group">
        <label>Рік навчання:</label>
        <select name="academic_year_id" required>
            <?php while ($row = $years->fetch_assoc()): ?>
                <option value="<?= $row['id'] ?>" <?= ($row['id'] == $record['academic_year_id']) ? 'selected' : '' ?>>
                    <?= $row['year_name'] ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>

    <div class="form-group">
        <label>Група:</label>
        <select name="group_id" required>
            <?php while ($row = $groups->fetch_assoc()): ?>
                <option value="<?= $row['id'] ?>" <?= ($row['id'] == $record['group_id']) ? 'selected' : '' ?>>
                    <?= $row['name'] ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>

    <div class="form-group">
    <label>Предмет:</label>
    <select name="subject_id" id="schedule_subject" required>
        <?php 
        $subjectsWithData = $conn->query("SELECT subject.id, subject.name, subject.teacher_id, subject.classroom_id FROM subject");
        while ($row = $subjectsWithData->fetch_assoc()): ?>
            <option 
                value="<?= $row['id'] ?>" 
                data-teacher="<?= $row['teacher_id'] ?>" 
                data-classroom="<?= $row['classroom_id'] ?>"
                <?= ($row['id'] == $record['subject_id']) ? 'selected' : '' ?>>
                <?= $row['name'] ?>
            </option>
        <?php endwhile; ?>
    </select>
</div>

<div class="form-group">
    <label>Викладач:</label>
    <select name="teacher_id" id="schedule_teacher" required>
        <?php 
        $teachers = $conn->query("SELECT * FROM teachers");
        while ($row = $teachers->fetch_assoc()): ?>
            <option value="<?= $row['id'] ?>" <?= ($row['id'] == $record['teacher_id']) ? 'selected' : '' ?>>
                <?= $row['full_name'] ?>
            </option>
        <?php endwhile; ?>
    </select>
</div>

<div class="form-group">
    <label>Кабінет:</label>
    <select name="classroom_id" id="schedule_classroom" required>
        <?php 
        $classrooms = $conn->query("SELECT * FROM classroom");
        while ($row = $classrooms->fetch_assoc()): ?>
            <option value="<?= $row['id'] ?>" <?= ($row['id'] == $record['classroom_id']) ? 'selected' : '' ?>>
                <?= $row['name'] ?>
            </option>
        <?php endwhile; ?>
    </select>
</div>


    <div class="form-group">
        <label>Номер пари:</label>
        <select name="lesson_number_id" required>
            <?php while ($row = $lessons->fetch_assoc()): ?>
                <option value="<?= $row['id'] ?>" <?= ($row['id'] == $record['lesson_number_id']) ? 'selected' : '' ?>>
                    <?= $row['lesson_number'] ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>

    <div class="form-group">
        <label>День тижня:</label>
        <select name="day_of_week" required>
            <?php
            $days = ['Понеділок', 'Вівторок', 'Середа', 'Четвер', 'Пʼятниця', 'Субота'];
            foreach ($days as $day):
            ?>
                <option value="<?= $day ?>" <?= ($record['day_of_week'] == $day) ? 'selected' : '' ?>>
                    <?= $day ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label>Тип пари:</label>
        <select name="lesson_type" required>
            <option value="повна" <?= ($record['lesson_type'] == 'повна') ? 'selected' : '' ?>>Повна</option>
            <option value="підгрупи" <?= ($record['lesson_type'] == 'підгрупи') ? 'selected' : '' ?>>Підгрупи</option>
            <option value="чисельник" <?= ($record['lesson_type'] == 'чисельник') ? 'selected' : '' ?>>Чисельник</option>
            <option value="знаменник" <?= ($record['lesson_type'] == 'знаменник') ? 'selected' : '' ?>>Знаменник</option>
        </select>
    </div>

    <h3><strong>Останнє оновлення:</strong> <?= htmlspecialchars($record['updated_at']) ?></h3>
                <button type="submit" name="action" value="save" class="button-1">Зберегти зміни</button>
                <button type="submit" name="action" value="apply_semester" class="button-1 button-2" onclick="return confirm('Застосувати зміни до всіх таких пар у семестрі?')">Застосувати до семестру</button>
</form>
<script type="text/javascript" src="ajax.js"></script>
 </div>

<script type="text/javascript" src="ajax.js"></script>

</body>
</html>