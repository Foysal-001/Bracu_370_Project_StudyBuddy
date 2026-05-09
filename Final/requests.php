<?php
require "db.php";
require_login();

$admin_id = $_SESSION['user_id'];

if (isset($_GET['approve'])) {
    $user = $_GET['user'];
    $group = $_GET['group'];

    $check = mysqli_query($database,
    "SELECT * FROM groups
     WHERE group_id='$group' AND admin_id='$admin_id'");

    if (mysqli_num_rows($check) > 0) {
        mysqli_query($database,
        "UPDATE join_request
         SET admin_approve=1
         WHERE user_id='$user' AND group_id='$group'");

        $leader_check = mysqli_query($database,
        "SELECT * FROM leaderboard WHERE user_id='$admin_id'");

        if (mysqli_num_rows($leader_check) == 0) {
            mysqli_query($database,
            "INSERT INTO leaderboard(user_id, username, score)
             VALUES('$admin_id', '{$_SESSION['username']}', 5)");
        } else {
            mysqli_query($database,
            "UPDATE leaderboard SET score = score + 5
             WHERE user_id='$admin_id'");
        }
    }
}

if (isset($_GET['reject'])) {
    $user = $_GET['user'];
    $group = $_GET['group'];

    $check = mysqli_query($database,
    "SELECT * FROM groups
     WHERE group_id='$group' AND admin_id='$admin_id'");

    if (mysqli_num_rows($check) > 0) {
        mysqli_query($database,
        "DELETE FROM join_request
         WHERE user_id='$user' AND group_id='$group'");

        mysqli_query($database,
        "UPDATE user
         SET reject_penalty = reject_penalty + 5
         WHERE user_id='$user'");
    }
}

$query = "SELECT join_request.*, user.username, groups.group_name
          FROM join_request
          JOIN user ON join_request.user_id = user.user_id
          JOIN groups ON join_request.group_id = groups.group_id
          WHERE groups.admin_id='$admin_id'
          AND join_request.admin_approve=0";

$result = mysqli_query($database, $query);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Requests</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php require "header.php"; ?>

<div class="container card">
    <h2>Join Requests For Your Groups</h2>

    <div class="table-wrap">
        <table>
            <tr>
                <th>User</th>
                <th>Group</th>
                <th>Action</th>
            </tr>

            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?php echo $row['username']; ?></td>
                    <td><?php echo $row['group_name']; ?></td>
                    <td>
                        <a class="btn green"
                        href="requests.php?approve=1&user=<?php echo $row['user_id']; ?>&group=<?php echo $row['group_id']; ?>">
                            Approve
                        </a>

                        <a class="btn red"
                        href="requests.php?reject=1&user=<?php echo $row['user_id']; ?>&group=<?php echo $row['group_id']; ?>">
                            Reject
                        </a>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>
</div>

</body>
</html>
