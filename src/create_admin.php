<?php
include 'db.php';
$sql = "SELECT * FROM admins WHERE username = 'admin'";
$result = $conn->query($sql);

if ($result->num_rows === 0) {
    $username = 'admin';
    $password = 'admin';

    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $password_hash);
    if ($stmt->execute()) {
        echo "Користувача admin успішно створено!";
    } else {
        echo "Помилка при створенні користувача.";
    }
} else {
    echo "Користувач admin вже існує.";
}

$conn->close();
?>
