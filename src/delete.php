<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}
include 'db.php';

$id = $_GET['id'];
$conn->query("DELETE FROM schedule WHERE id = $id");

header("Location: homepage.php?group_id=");
?>
