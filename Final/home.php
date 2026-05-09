<?php
require "db.php";
require_login();

$user_id = $_SESSION['user_id'];

$own_groups = mysqli_query($database, "SELECT COUNT(*) AS total FROM groups WHERE admin_id='$user_id'");
$own = mysqli_fetch_assoc($own_groups);

$approved_groups = mysqli_query($database,
"SELECT COUNT(*) AS total FROM join_request
 WHERE user_id='$user_id' AND admin_approve=1");
$joined = mysqli_fetch_assoc($approved_groups);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php require "header.php"; ?>

<div class="container">
    <div class="hero">
        <h1>Welcome <?php echo $_SESSION['username']; ?></h1>
        <p>This is your dashboard.</p>
    </div>

    <div class="grid">
        <div class="card">
            <h3>Your Created Groups</h3>
            <p class="score"><?php echo $own['total']; ?></p>
        </div>

        <div class="card">
            <h3>Your Approved Groups</h3>
            <p class="score"><?php echo $joined['total']; ?></p>
        </div>

        <div class="card">
            <h3>Quick Actions</h3>
            <a class="btn" href="create_group.php">Create Group</a>
            <a class="btn dark" href="groups.php">View Groups</a>
        </div>
    </div>
</div>

</body>
</html>
