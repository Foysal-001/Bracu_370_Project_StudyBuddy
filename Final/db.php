<?php
session_start();

$database = mysqli_connect("localhost", "root", "", "370_final");

if (!$database) {
    die("Database connection failed");
}

function require_login() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
}
?>
