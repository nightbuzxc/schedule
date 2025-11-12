<?php
require_once 'vendor/autoload.php';
session_start();

if (isset($_GET['group_id'])) {
    $_SESSION['group_id'] = $_GET['group_id'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['range'] = $_POST['range'];
    if ($_POST['range'] === 'custom') {
        $_SESSION['start_date'] = $_POST['start_date'];
        $_SESSION['end_date'] = $_POST['end_date'];
    }
}

$client = new Google_Client();
$client->setAuthConfig('credentials.json');
$client->setRedirectUri('http://localhost/supernova/auth_callback.php');
$client->addScope(Google_Service_Calendar::CALENDAR_EVENTS);

$auth_url = $client->createAuthUrl();
header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
exit;
?>
