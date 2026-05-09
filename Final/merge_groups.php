<?php
require "db.php";
require_login();

$admin_id = $_SESSION['user_id'];
$username = "";

if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
} else if (isset($_SESSION['user'])) {
    $username = $_SESSION['user'];
}

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
            $main_query = mysqli_query($database,
            "SELECT * FROM groups WHERE group_id='$main_group'");

            $merge_query = mysqli_query($database,
            "SELECT * FROM groups WHERE group_id='$merged_group'");

            $main_row = mysqli_fetch_assoc($main_query);
            $merge_row = mysqli_fetch_assoc($merge_query);

            if (strtolower($main_row['topic']) != strtolower($merge_row['topic'])) {
                $message = "Only groups with the same topic can be merged.";
            } else {
                $topic = $main_row['topic'];
                $main_name = $main_row['group_name'];
                $merge_name = $merge_row['group_name'];

                $new_name = $main_name . " + " . $merge_name;

                $main_desc = $main_row['description'];
                $merge_desc = $merge_row['description'];

                $new_desc = $main_desc . "\n\nMerged with: " . $merge_name;

                if ($merge_desc != "") {
                    $new_desc = $new_desc . "\nMerged group description: " . $merge_desc;
                }

                $new_name = mysqli_real_escape_string($database, $new_name);
                $new_desc = mysqli_real_escape_string($database, $new_desc);
                $topic_safe = mysqli_real_escape_string($database, $topic);

                mysqli_query($database,
                "INSERT INTO group_merge(main_group_id, merged_group_id, topic)
                 VALUES('$main_group', '$merged_group', '$topic_safe')");

                mysqli_query($database,
                "DELETE FROM join_request
                 WHERE group_id='$merged_group'
                 AND user_id IN (
                     SELECT user_id FROM (
                         SELECT user_id FROM join_request
                         WHERE group_id='$main_group'
                     ) AS temp
                 )");

                mysqli_query($database,
                "UPDATE join_request
                 SET group_id='$main_group', admin_approve=1
                 WHERE group_id='$merged_group'
                 AND admin_approve=1");

                mysqli_query($database,
                "UPDATE groups
                 SET group_name='$new_name', description='$new_desc'
                 WHERE group_id='$main_group'");

                mysqli_query($database,
                "DELETE FROM groups
                 WHERE group_id='$merged_group'");

                $leader_check = mysqli_query($database,
                "SELECT * FROM leaderboard WHERE user_id='$admin_id'");

                if (mysqli_num_rows($leader_check) == 0) {
                    mysqli_query($database,
                    "INSERT INTO leaderboard(user_id, username, score)
                     VALUES('$admin_id', '$username', 20)");
                } else {
                    mysqli_query($database,
                    "UPDATE leaderboard
                     SET score = score + 20
                     WHERE user_id='$admin_id'");
                }

                $message = "Groups merged successfully. Main group was updated and the other group was deleted.";
            }
        } else {
            $message = "Only the admin/creator of both groups can merge them.";
        }
    }
}

$groups = mysqli_query($database,
"SELECT * FROM groups
 WHERE admin_id='$admin_id'
 ORDER BY group_id DESC");

$merge_history = mysqli_query($database,
"SELECT gm.*, g.group_name AS main_group_name
 FROM group_merge gm
 LEFT JOIN groups g ON gm.main_group_id = g.group_id
 ORDER BY gm.merge_id DESC");
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
    <p class="small">
        Only the admin/creator of both groups can merge them.<br>
        Both groups must have the same topic.<br>
        The main group will be updated, approved members will move there, and the other group will be deleted.
    </p>

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

        <label>Group To Merge And Delete</label>
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

<div class="container card">
    <h2>Merge History</h2>

    <div class="table-wrap">
        <table>
            <tr>
                <th>Updated Main Group</th>
                <th>Deleted Group ID</th>
                <th>Topic</th>
            </tr>

            <?php while ($row = mysqli_fetch_assoc($merge_history)) { ?>
                <tr>
                    <td><?php echo $row['main_group_name']; ?></td>
                    <td><?php echo $row['merged_group_id']; ?></td>
                    <td><?php echo $row['topic']; ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>
</div>

</body>
</html>
