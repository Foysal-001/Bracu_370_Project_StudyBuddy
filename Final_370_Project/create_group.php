<?php
require "db.php";
require_login();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["group_name"]);
    $course = trim($_POST["course"]);
    $interest = trim($_POST["interest"]);
    $description = trim($_POST["description"]);
    $uid = current_user_id();

    if ($name == "" || $course == "" || $interest == "") {
        flash_set("Group name, course and interest are required.");
    } else {
        $stmt = $conn->prepare("INSERT INTO study_groups (group_name, course, interest, description, admin_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $name, $course, $interest, $description, $uid);
        $stmt->execute();

        $gid = $stmt->insert_id;

        $member = $conn->prepare("INSERT INTO group_members (group_id, user_id, role) VALUES (?, ?, 'admin')");
        $member->bind_param("ii", $gid, $uid);
        $member->execute();

        flash_set("Study group created successfully. Your leaderboard score will increase by 10 for owning this group.");
        go("group_details.php?id=" . $gid);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Create Group</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php require "header.php"; ?>
<div class="container">
    <?php flash_show(); ?>
    <div class="card">
        <h2>Create Study Group</h2>
        <form method="POST">
            <label>Group Name</label>
            <input type="text" name="group_name" required>

            <label>Course</label>
            <input type="text" name="course" placeholder="Example: CSE370" required>

            <label>Interest / Topic</label>
            <input type="text" name="interest" placeholder="Example: Database Project" required>

            <label>Description</label>
            <textarea name="description"></textarea>

            <button class="btn" type="submit">Create</button>
            <a class="btn gray" href="home.php">Back</a>
        </form>
    </div>
</div>
</body>
</html>
