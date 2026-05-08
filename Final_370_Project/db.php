<?php
session_start();

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "Final_Project370";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

function logged_in() {
    return isset($_SESSION['user_id']);
}

function go($page) {
    header("Location: " . $page);
    exit();
}

function require_login() {
    if (!logged_in()) {
        go("login.php");
    }
}

function current_user_id() {
    return $_SESSION['user_id'] ?? 0;
}

function safe($text) {
    return htmlspecialchars($text ?? "", ENT_QUOTES, "UTF-8");
}

function flash_set($msg) {
    $_SESSION['flash'] = $msg;
}

function flash_show() {
    if (isset($_SESSION['flash'])) {
        echo '<div class="flash">' . safe($_SESSION['flash']) . '</div>';
        unset($_SESSION['flash']);
    }
}
?>
