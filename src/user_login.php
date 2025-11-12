<?php
session_start();
$mysqli = new mysqli("db", "root", "", "college_schedule");
$mysqli->set_charset("utf8");

$error = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    $stmt = $mysqli->prepare("SELECT id, password FROM admins WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($userId, $hashedPassword);
        $stmt->fetch();

        if (password_verify($password, $hashedPassword)) {
            $_SESSION["admin_id"] = $userId;
            $_SESSION["username"] = $username;
            header("Location: userpage.php");
            exit;
        } else {
            $error = "Невірний лоігн або пароль";
        }
    } else {
        $error = "Користувача не знайдено";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Авторизація</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css">
  <link rel='stylesheet' href='http://maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css'>
  <link rel="stylesheet" href="lgn_style/sign.css">
  </head>

<body>
  <div id="notification-toast"></div>
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
</div>

  <svg id="svg-source" height="0" xmlns="http://www.w3.org/2000/svg" style="position: absolute">
  </svg>

  <div class="wrapper">
    <div class="header">
      <h3 class="sign-in">Авторизація</h3>
    </div>
    <div class="clear"></div> 

    <form method="POST">
        <div>
          <input type="text" name="username" placeholder="Логін" required>
        </div> 
        <div>
           <input type="password" name="password" placeholder="Пароль" required>
        </div> 
        <div>
         <input class="button" type="submit" value="Авторизуватись" />
         <div class="reg"><p>Не маєте акаунт? <a href="user_reg.php">Зареєстурватись</a></p></div>
       </div>
    </form>  
  </div>

  <script src="http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>

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