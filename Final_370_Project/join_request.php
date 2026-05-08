<?php
require "db.php";
require_login();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $gid = intval($_POST["group_id"]);
    $uid = current_user_id();

    $check = $conn->prepare("SELECT * FROM group_members WHERE group_id=? AND user_id=?");
    $check->bind_param("ii", $gid, $uid);
    $check->execute();

    if ($check->get_result()->num_rows > 0) {
        flash_set("You are already a member.");
        go("group_details.php?id=" . $gid);
    }

    $stmt = $conn->prepare("
        INSERT INTO join_requests (group_id, user_id, status, handled_at)
        VALUES (?, ?, 'pending', NULL)
        ON DUPLICATE KEY UPDATE status='pending', requested_at=CURRENT_TIMESTAMP, handled_at=NULL
    ");
    $stmt->bind_param("ii", $gid, $uid);
    $stmt->execute();

    flash_set("Join request sent.");
    go("group_details.php?id=" . $gid);
}
go("groups.php");
?>
