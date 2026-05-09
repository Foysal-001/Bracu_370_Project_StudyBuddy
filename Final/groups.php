<?php
require "db.php";
require_login();

$user_id = $_SESSION['user_id'];
$message = "";

if (isset($_GET['join'])) {
    $group_id = $_GET['join'];

    $group_check = mysqli_query($database,
    "SELECT * FROM groups WHERE group_id='$group_id'");

    $group = mysqli_fetch_assoc($group_check);

    if ($group && $group['admin_id'] == $user_id) {
        $message = "You are the admin of this group, so you are already a member.";
    } else {
        $request_check = mysqli_query($database,
        "SELECT * FROM join_request
         WHERE user_id='$user_id' AND group_id='$group_id'");

        if (mysqli_num_rows($request_check) == 0) {
            mysqli_query($database,
            "INSERT INTO join_request(user_id, group_id, admin_approve)
             VALUES('$user_id', '$group_id', 0)");

            $message = "Join request sent.";
        } else {
            $message = "You already requested or joined this group.";
        }
    }
}

if (isset($_GET['search']) && $_GET['search'] != "") {
    $search = $_GET['search'];

    $query = "SELECT groups.*, user.username
              FROM groups
              JOIN user ON groups.admin_id = user.user_id
              WHERE groups.topic LIKE '%$search%'
              ORDER BY groups.group_id DESC";
} else {
    $query = "SELECT groups.*, user.username
              FROM groups
              JOIN user ON groups.admin_id = user.user_id
              ORDER BY groups.group_id DESC";
}

$result = mysqli_query($database, $query);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Groups</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php require "header.php"; ?>

<div class="container">
    <div class="card">
        <h2>All Study Groups</h2>

        <?php if ($message != "") { ?>
            <div class="notice"><?php echo $message; ?></div>
        <?php } ?>

        <form method="GET">
            <input type="text" name="search" placeholder="Search by topic">
            <button class="btn">Search</button>
            <a class="btn dark" href="groups.php">Reset</a>
        </form>
    </div>

    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <div class="card">
            <h3><?php echo $row['group_name']; ?></h3>

            <p><b>Topic:</b> <?php echo $row['topic']; ?></p>
            <p><b>Description:</b> <?php echo $row['description']; ?></p>
            <p><b>Location:</b> <?php echo $row['location']; ?></p>
            <p><b>Time:</b> <?php echo $row['time']; ?></p>
            <p><b>Admin:</b> <?php echo $row['username']; ?></p>

            <?php
            $gid = $row['group_id'];

            $already = mysqli_query($database,
            "SELECT * FROM join_request
             WHERE user_id='$user_id' AND group_id='$gid'");

            if ($row['admin_id'] == $user_id) {
                echo "<p class='small'>You are admin/member of this group.</p>";
            } else if (mysqli_num_rows($already) > 0) {
                echo "<p class='small'>Already requested or joined.</p>";
            } else {
            ?>
                <a class="btn" href="groups.php?join=<?php echo $row['group_id']; ?>">
                    Send Join Request
                </a>
            <?php } ?>
        </div>
    <?php } ?>
</div>

</body>
</html>
