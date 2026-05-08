<?php
require "db.php";
require_login();

$uid = current_user_id();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $main = intval($_POST["main_group_id"]);
    $merge = intval($_POST["merge_group_id"]);

    if ($main == $merge) {
        flash_set("You cannot merge the same group.");
        go("merge_groups.php");
    }

    $main_stmt = $conn->prepare("SELECT * FROM study_groups WHERE group_id=? AND admin_id=?");
    $main_stmt->bind_param("ii", $main, $uid);
    $main_stmt->execute();
    $main_group = $main_stmt->get_result()->fetch_assoc();

    $merge_stmt = $conn->prepare("SELECT * FROM study_groups WHERE group_id=? AND admin_id=?");
    $merge_stmt->bind_param("ii", $merge, $uid);
    $merge_stmt->execute();
    $merge_group = $merge_stmt->get_result()->fetch_assoc();

    if (!$main_group || !$merge_group) {
        flash_set("You can only merge groups where you are admin.");
        go("merge_groups.php");
    }

    if (strtolower(trim($main_group["interest"])) != strtolower(trim($merge_group["interest"]))) {
        flash_set("Only groups with the same interest can be merged. Uppercase and lowercase do not matter.");
        go("merge_groups.php");
    }

    $copy_members = $conn->prepare("
        INSERT IGNORE INTO group_members (group_id, user_id, role)
        SELECT ?, user_id,
        CASE WHEN role='admin' THEN 'member' ELSE role END
        FROM group_members
        WHERE group_id=?
    ");
    $copy_members->bind_param("ii", $main, $merge);
    $copy_members->execute();

    $copy_schedule = $conn->prepare("
        INSERT INTO group_schedules (group_id, meeting_title, meeting_date, meeting_time, location, note)
        SELECT ?, meeting_title, meeting_date, meeting_time, location, note
        FROM group_schedules
        WHERE group_id=?
    ");
    $copy_schedule->bind_param("ii", $main, $merge);
    $copy_schedule->execute();

    $log = $conn->prepare("INSERT INTO merge_logs (main_group_id, merged_group_id, merged_by) VALUES (?, ?, ?)");
    $log->bind_param("iii", $main, $merge, $uid);
    $log->execute();

    $delete = $conn->prepare("DELETE FROM study_groups WHERE group_id=?");
    $delete->bind_param("i", $merge);
    $delete->execute();

    flash_set("Groups merged successfully. You received 20 leaderboard points for this merge.");
    go("group_details.php?id=" . $main);
}

$groups = $conn->prepare("
    SELECT group_id, group_name, course, interest
    FROM study_groups
    WHERE admin_id=?
    ORDER BY course, interest, group_name
");
$groups->bind_param("i", $uid);
$groups->execute();
$list = $groups->get_result();

$items = [];
while ($row = $list->fetch_assoc()) {
    $items[] = $row;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Merge Groups</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php require "header.php"; ?>
<div class="container">
    <?php flash_show(); ?>

    <div class="card">
        <h2>Merge Groups</h2>
        <p class="small">Only groups with the same interest can be merged. Uppercase and lowercase do not matter. Members and schedules move into the main group.</p>

        <?php if (count($items) < 2): ?>
            <div class="notice">You need at least two groups to merge.</div>
            <a class="btn gray" href="home.php">Back</a>
        <?php else: ?>
            <form method="POST">
                <label>Main Group</label>
                <select name="main_group_id" required>
                    <?php foreach ($items as $g): ?>
                        <option value="<?php echo $g["group_id"]; ?>">
                            <?php echo safe($g["group_name"] . " | " . $g["course"] . " | " . $g["interest"]); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label>Group to Merge</label>
                <select name="merge_group_id" required>
                    <?php foreach ($items as $g): ?>
                        <option value="<?php echo $g["group_id"]; ?>">
                            <?php echo safe($g["group_name"] . " | " . $g["course"] . " | " . $g["interest"]); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <button class="btn" type="submit">Merge Groups</button>
                <a class="btn gray" href="home.php">Back</a>
            </form>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
