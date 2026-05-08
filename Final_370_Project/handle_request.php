<?php
require "db.php";
require_login();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $rid = intval($_POST["request_id"]);
    $action = $_POST["action"];
    $uid = current_user_id();

    $stmt = $conn->prepare("
        SELECT jr.*, sg.admin_id
        FROM join_requests jr
        JOIN study_groups sg ON jr.group_id = sg.group_id
        WHERE jr.request_id=?
    ");
    $stmt->bind_param("i", $rid);
    $stmt->execute();
    $req = $stmt->get_result()->fetch_assoc();

    if (!$req || $req["admin_id"] != $uid) {
        flash_set("You are not allowed to handle this request.");
        go("requests.php");
    }

    if ($action == "approve") {
        $add = $conn->prepare("
            INSERT IGNORE INTO group_members (group_id, user_id, role)
            VALUES (?, ?, 'member')
        ");
        $add->bind_param("ii", $req["group_id"], $req["user_id"]);
        $add->execute();

        $up = $conn->prepare("UPDATE join_requests SET status='approved', handled_at=CURRENT_TIMESTAMP WHERE request_id=?");
        $up->bind_param("i", $rid);
        $up->execute();

        flash_set("Request approved. The joined member now gets 5 leaderboard points for this membership.");
    } else {
        $up = $conn->prepare("UPDATE join_requests SET status='rejected', handled_at=CURRENT_TIMESTAMP WHERE request_id=?");
        $up->bind_param("i", $rid);
        $up->execute();

        $penalty = $conn->prepare("UPDATE users SET reject_penalty = reject_penalty + 5 WHERE user_id=?");
        $penalty->bind_param("i", $uid);
        $penalty->execute();

        flash_set("Request rejected. 5 leaderboard points were deducted from you.");
    }
}
go("requests.php");
?>
