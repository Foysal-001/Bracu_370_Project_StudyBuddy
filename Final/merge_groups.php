<?php
require "db.php";
require_login();

$admin_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $main_group = $_POST['main_group'];
    $merged_group = $_POST['merged_group'];

    if ($main_group == $merged_group) {
        $message = "You cannot merge the same group.";
    } else {
        $check = mysqli_query($database,
        "SELECT * FROM groups
         WHERE (group_id='$main_group' OR group_id='$merged_group')
         AND admin_id='$admin_id'");

        if (mysqli_num_rows($check) == 2) {
            $main_data = mysqli_query($database,
            "SELECT topic FROM groups
             WHERE group_id='$main_group' AND admin_id='$admin_id'");

            $main_row = mysqli_fetch_assoc($main_data);
            $topic = $main_row['topic'];

            mysqli_query($database,
            "INSERT INTO group_merge(main_group_id, merged_group_id, topic)
             VALUES('$main_group', '$merged_group', '$topic')");

            $leader_check = mysqli_query($database,
            "SELECT * FROM leaderboard WHERE user_id='$admin_id'");

            if (mysqli_num_rows($leader_check) == 0) {
                mysqli_query($database,
                "INSERT INTO leaderboard(user_id, username, score)
                 VALUES('$admin_id', '{$_SESSION['username']}', 20)");
            } else {
                mysqli_query($database,
                "UPDATE leaderboard
                 SET score = score + 20
                 WHERE user_id='$admin_id'");
            }

            $message = "Groups merged successfully.";
        } else {
            $message = "Only the admin/creator of both groups can merge them.";
        }
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
    <title>Merge Groups</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php require "header.php"; ?>

<div class="container card">
    <h2>Merge Your Groups</h2>
    <p class="small">Only the admin/creator can merge their own groups.</p>

    <?php if (isset($message)) { ?>
        <div class="notice"><?php echo $message; ?></div>
    <?php } ?>

    <form method="POST">
        <label>Main Group</label>
        <select name="main_group" required>
            <?php
            mysqli_data_seek($groups, 0);
            while ($row = mysqli_fetch_assoc($groups)) {
            ?>
                <option value="<?php echo $row['group_id']; ?>">
                    <?php echo $row['group_name']; ?> - <?php echo $row['topic']; ?>
                </option>
            <?php } ?>
        </select>

        <label>Group To Merge</label>
        <select name="merged_group" required>
            <?php
            mysqli_data_seek($groups, 0);
            while ($row = mysqli_fetch_assoc($groups)) {
            ?>
                <option value="<?php echo $row['group_id']; ?>">
                    <?php echo $row['group_name']; ?> - <?php echo $row['topic']; ?>
                </option>
            <?php } ?>
        </select>

        <button class="btn">Merge Groups</button>
    </form>
</div>

</body>
</html>
