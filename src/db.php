<?php
$host = 'db';
$user = 'root';
$pass = '';
$db_name = 'college_schedule';

$conn = new mysqli($host, $user, $pass, $db_name);
if ($conn->connect_error) {
    die("Помилка з'єднання з БД: " . $conn->connect_error);
}