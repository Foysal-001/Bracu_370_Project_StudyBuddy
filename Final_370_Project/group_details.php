<?php
require "db.php";
require_login();

$gid = intval($_GET["id"] ?? 0);
$uid = current_user_id();

$stmt = $conn->prepare("
    SELECT sg.*, u.name AS admin_name, u.email AS admin_email
    FROM study_groups sg
    JOIN users u ON sg.admin_id = u.user_id
    WHERE sg.group_id=?
");
$stmt->bind_param("i", $gid);
$stmt->execute();
$group = $stmt->get_result()->fetch_assoc();

if (!$group) {
    flash_set("Group not found.");
    go("groups.php");
}

$is_admin = ($group["admin_id"] == $uid);

$member_check = $conn->prepare("SELECT role FROM group_members WHERE group_id=? AND user_id=?");
$member_check->bind_param("ii", $gid, $uid);
$member_check->execute();
$membership = $member_check->get_result()->fetch_assoc();
$is_member = $membership ? true : false;

$req_check = $conn->prepare("SELECT status FROM join_requests WHERE group_id=? AND user_id=?");
$req_check->bind_param("ii", $gid, $uid);
$req_check->execute();
$request = $req_check->get_result()->fetch_assoc();

$members = $conn->prepare("
    SELECT u.user_id, u.name, u.email, gm.role, gm.joined_at
    FROM group_members gm
    JOIN users u ON gm.user_id = u.user_id
    WHERE gm.group_id=?
    ORDER BY gm.role ASC, gm.joined_at ASC
");
$members->bind_param("i", $gid);
$members->execute();
$member_list = $members->get_result();

$schedules = $conn->prepare("SELECT * FROM group_schedules WHERE group_id=? ORDER BY meeting_date, meeting_time");
$schedules->bind_param("i", $gid);
$schedules->execute();
$schedule_list = $schedules->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Group Details</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php require "header.php"; ?>
<div class="container">
    <?php flash_show(); ?>

    <div class="card">
        <h2><?php echo safe($group["group_name"]); ?></h2>
        <p><b>Course:</b> <?php echo safe($group["course"]); ?></p>
        <p><b>Interest:</b> <?php echo safe($group["interest"]); ?></p>
        <p><b>Description:</b> <?php echo safe($group["description"]); ?></p>
        <p><b>Admin:</b> <?php echo safe($group["admin_name"]); ?> (<?php echo safe($group["admin_email"]); ?>)</p>

        <div class="actions">
            <?php if (!$is_member && (!$request || $request["status"] == "rejected")): ?>
                <form method="POST" action="join_request.php" style="display:inline;">
                    <input type="hidden" name="group_id" value="<?php echo $gid; ?>">
                    <button class="btn green" type="submit">Request to Join</button>
                </form>
            <?php elseif ($request && $request["status"] == "pending" && !$is_member): ?>
                <span class="btn gray">Request Pending</span>
            <?php endif; ?>

            <?php if ($is_member && !$is_admin): ?>
                <form method="POST" action="leave_group.php" style="display:inline;">
                    <input type="hidden" name="group_id" value="<?php echo $gid; ?>">
                    <button class="btn red" type="submit">Leave Group</button>
                </form>
            <?php endif; ?>

            <?php if ($is_admin): ?>
                <a class="btn" href="add_schedule.php?group_id=<?php echo $gid; ?>">Add Schedule</a>
                <a class="btn gray" href="requests.php">Join Requests</a>
            <?php endif; ?>

            <a class="btn dark" href="groups.php">Back</a>
        </div>
    </div>

    <div class="card">
        <h2>Schedule</h2>
        <div class="table-wrap">
            <table>
                <tr>
                    <th>Title</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Location</th>
                    <th>Note</th>
                </tr>
                <?php while ($s = $schedule_list->fetch_assoc()): ?>
                <tr>
                    <td><?php echo safe($s["meeting_title"]); ?></td>
                    <td><?php echo safe($s["meeting_date"]); ?></td>
                    <td><?php echo safe($s["meeting_time"]); ?></td>
                    <td><?php echo safe($s["location"]); ?></td>
                    <td><?php echo safe($s["note"]); ?></td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>

    <div class="card">
        <h2>Members</h2>
        <div class="table-wrap">
            <table>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Joined</th>
                </tr>
                <?php while ($m = $member_list->fetch_assoc()): ?>
                <tr>
                    <td><?php echo safe($m["name"]); ?></td>
                    <td><?php echo safe($m["email"]); ?></td>
                    <td><?php echo safe($m["role"]); ?></td>
                    <td><?php echo safe($m["joined_at"]); ?></td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>
</div>
</body>
</html>
