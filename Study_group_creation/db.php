<?php
$conn = new mysqli("localhost", "root", "", "stdy_grp");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>