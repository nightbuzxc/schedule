<?php
$mysqli = new mysqli("db", "root", "", "college_schedule");
if ($mysqli->connect_error) {
    die("Помилка з'єднання з базою даних: " . $mysqli->connect_error);
}
$mysqli->set_charset("utf8");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"]);
    $password = $_POST["password"];
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $mysqli->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $hashedPassword);

    if ($stmt->execute()) {
        echo "Користувач успішно зареєстрований";
    } else {
        echo "Помилка реєстрації: " . $stmt->error;
    }

    $stmt->close();
}
?>


<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Реєстрація</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css">
  <link rel='stylesheet' href='http://maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css'>
  <link rel="stylesheet" href="lgn_style/sign.css">
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
</div>

  <svg id="svg-source" height="0" xmlns="http://www.w3.org/2000/svg" style="position: absolute">
  </svg>

  <div class="wrapper">
    <div class="header">
      <h3 class="sign-in">Реєстрація</h3>
    </div>
    <div class="clear"></div> 

    <?php if (!empty($error)): ?>
      <p style="color:red; text-align:center;"><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="POST">
        <div>
          <input type="text" name="username" placeholder="Логін" required>
        </div> 
        <div>
           <input type="password" name="password" placeholder="Пароль" required>
        </div> 
       <div>
        <input class="button" type="submit" value="Зареєструватися" />  
      </div>
      <p>Вже маєте акаунт? <a href="user_login.php">Увійти</a></p>
    </form>  
  </div>

  <script src="http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
</body>
</html>