<?php
include 'db.php';

$user_id = 1;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $group_id = $_POST['group_id'];

    $checkSql = "SELECT * FROM join_requests 
                 WHERE group_id = ? AND user_id = ?";

    $checkStmt = mysqli_prepare($conn, $checkSql);
    mysqli_stmt_bind_param($checkStmt, "ii", $group_id, $user_id);
    mysqli_stmt_execute($checkStmt);

    $checkResult = mysqli_stmt_get_result($checkStmt);

    if (mysqli_num_rows($checkResult) > 0) {
        header("Location: search_groups.php");
        exit();
    }

    $sql = "INSERT INTO join_requests (group_id, user_id, status)
            VALUES (?, ?, 'pending')";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $group_id, $user_id);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: search_groups.php");
        exit();
    } else {
        echo "Error sending request.";
    }
}
?>