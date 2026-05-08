<?php
require "db.php";
require_login();

$gid = intval($_GET["group_id"] ?? $_POST["group_id"] ?? 0);
$uid = current_user_id();

$check = $conn->prepare("SELECT * FROM study_groups WHERE group_id=? AND admin_id=?");
$check->bind_param("ii", $gid, $uid);
$check->execute();
$group = $check->get_result()->fetch_assoc();

if (!$group) {
    flash_set("Only group admin can add schedule.");
    go("groups.php");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST["meeting_title"]);
    $date = $_POST["meeting_date"];
    $time = $_POST["meeting_time"];
    $location = trim($_POST["location"]);
    $note = trim($_POST["note"]);

    $stmt = $conn->prepare("
        INSERT INTO group_schedules (group_id, meeting_title, meeting_date, meeting_time, location, note)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("isssss", $gid, $title, $date, $time, $location, $note);
    $stmt->execute();

    flash_set("Schedule added.");
    go("group_details.php?id=" . $gid);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Schedule</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php require "header.php"; ?>
<div class="container">
    <div class="card">
        <h2>Add Schedule</h2>
        <p class="small"><?php echo safe($group["group_name"]); ?></p>
        <form method="POST">
            <input type="hidden" name="group_id" value="<?php echo $gid; ?>">

            <label>Meeting Title</label>
            <input type="text" name="meeting_title" required>

            <label>Date</label>
            <input type="date" name="meeting_date" required>

            <label>Time</label>
            <input type="time" name="meeting_time" required>

            <label>Location</label>
            <input type="text" name="location">

            <label>Note</label>
            <textarea name="note"></textarea>

            <button class="btn" type="submit">Add Schedule</button>
            <a class="btn gray" href="group_details.php?id=<?php echo $gid; ?>">Back</a>
        </form>
    </div>
</div>
</body>
</html>
