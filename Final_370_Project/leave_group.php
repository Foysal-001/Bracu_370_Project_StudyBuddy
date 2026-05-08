<?php
require "db.php";
require_login();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $gid = intval($_POST["group_id"]);
    $uid = current_user_id();

    $admin = $conn->prepare("SELECT admin_id FROM study_groups WHERE group_id=?");
    $admin->bind_param("i", $gid);
    $admin->execute();
    $group = $admin->get_result()->fetch_assoc();

    if ($group && $group["admin_id"] == $uid) {
        flash_set("Group admin cannot leave their own group.");
        go("group_details.php?id=" . $gid);
    }

    $stmt = $conn->prepare("DELETE FROM group_members WHERE group_id=? AND user_id=?");
    $stmt->bind_param("ii", $gid, $uid);
    $stmt->execute();

    flash_set("You left the group. Your membership score will update automatically.");
    go("groups.php");
}
go("groups.php");
?>
