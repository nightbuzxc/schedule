<?php
session_start();
include 'db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        if (password_verify($password, $admin['password'])) {
            $_SESSION['admin'] = $admin['username'];
            header("Location: homepage.php");
            exit;
        } else {
            $error = "Невірний пароль.";
        }
    } else {
        $error = "Користувача не знайдено.";
    }
}
?>


<?php
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
<html >
<head>
  <meta charset="UTF-8">
  <title>Увійти як адміністратор</title>
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css">

  <link rel='stylesheet prefetch' href='http://maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css'>

      <link rel="stylesheet" href="lgn_style/sign.css">

  
</head>
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

<body>
<div id="notification-toast"></div>
<div class="wrapper">

  <div class="header">
    <h3 class="sign-in">Вхід як адміністратор</h3>
  </div>

  <form method="POST">

      <div>
        <input type="text" name="username" placeholder="Логін" required>
      </div> 

      <div>
         <input type="password" name="password" placeholder="Пароль" required>
      </div> 

     <div>
      <input class="button" type="submit" value="Авторизуватись" />
    </div>

  </form>  
</div>
 <script src='http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>

 <script>
    function showNotification(message) {
      var toast = $('#notification-toast');
      toast.text(message);
      
      toast.addClass('show');
      
      setTimeout(function(){ 
        toast.removeClass('show'); 
      }, 3000);
    }

    <?php
    if (!empty($error)) {
      echo 'showNotification(' . json_encode($error) . ');';
    }
    ?>
</script>

</body>
</html>
