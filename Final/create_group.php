<?php
require "db.php";
require_login();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $group_name = $_POST['group_name'];
    $topic = $_POST['topic'];
    $description = $_POST['description'];
    $admin_id = $_SESSION['user_id'];

    $query = "INSERT INTO groups(group_name, topic, description, admin_id)
              VALUES('$group_name', '$topic', '$description', '$admin_id')";

    if (mysqli_query($database, $query)) {
        $group_id = mysqli_insert_id($database);

        mysqli_query($database,
        "INSERT INTO join_request(user_id, group_id, admin_approve)
         VALUES('$admin_id', '$group_id', 1)");

        $check = mysqli_query($database,
        "SELECT * FROM leaderboard WHERE user_id='$admin_id'");

        if (mysqli_num_rows($check) == 0) {
            mysqli_query($database,
            "INSERT INTO leaderboard(user_id, username, score)
             VALUES('$admin_id', '{$_SESSION['username']}', 10)");
        } else {
            mysqli_query($database,
            "UPDATE leaderboard SET score = score + 10
             WHERE user_id='$admin_id'");
        }

        header("Location: schedule.php");
        exit();
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

<div class="container card">
    <h2>Create Study Group</h2>
    <p class="small">Schedule is handled separately from group creation.</p>

    <form method="POST">
        <label>Group Name</label>
        <input type="text" name="group_name" required>

        <label>Topic</label>
        <input type="text" name="topic" required>

        <label>Description</label>
        <textarea name="description"></textarea>

        <button class="btn">Create Group</button>
    </form>
</div>

</body>
</html>
