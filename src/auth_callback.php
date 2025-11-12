<?php
require_once 'vendor/autoload.php';

session_start();

$client = new Google_Client();
$client->setAuthConfig('credentials.json');
$client->setRedirectUri('http://localhost/supernova/auth_callback.php');

if (!isset($_GET['code'])) {
    header('Location: homepage.php');
    exit;
}

try {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

    if (isset($token['error'])) {
        throw new Exception('Google OAuth Error: ' . $token['error']);
    }

    $_SESSION['access_token'] = $token;

    header('Location: export_to_calendar.php');
    exit;

} catch (Exception $e) {
    // Вивід помилки
    echo "Auth error: " . $e->getMessage();
    exit;
}
