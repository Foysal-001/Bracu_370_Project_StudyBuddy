<?php
require "db.php";
require_login();

$admin_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $group_id = $_POST['group_id'];
    $location = $_POST['location'];
    $time = $_POST['time'];

    $check = mysqli_query($database,
    "SELECT * FROM groups
     WHERE group_id='$group_id' AND admin_id='$admin_id'");

    if (mysqli_num_rows($check) > 0) {
        mysqli_query($database,
        "UPDATE groups
         SET location='$location', time='$time'
         WHERE group_id='$group_id' AND admin_id='$admin_id'");

        $message = "Schedule updated successfully.";
    } else {
        $message = "You can only schedule your own group.";
    }
}

$groups = mysqli_query($database,
"SELECT * FROM groups
 WHERE admin_id='$admin_id'
 ORDER BY group_id DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Group Schedule</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php require "header.php"; ?>

<div class="container">
    <div class="card">
        <h2>Set Group Schedule</h2>

        <?php if (isset($message)) { ?>
            <div class="flash"><?php echo $message; ?></div>
        <?php } ?>

        <form method="POST">
            <label>Select Your Group</label>
            <select name="group_id" required>
                <?php while ($row = mysqli_fetch_assoc($groups)) { ?>
                    <option value="<?php echo $row['group_id']; ?>">
                        <?php echo $row['group_name']; ?>
                    </option>
                <?php } ?>
            </select>

            <label>Location</label>
            <input type="text" name="location" required>

            <label>Time</label>
            <input type="time" name="time" required>

            <button class="btn">Save Schedule</button>
        </form>
    </div>
</div>

</body>
</html>
