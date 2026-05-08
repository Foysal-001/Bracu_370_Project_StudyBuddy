<?php
require "db.php";
require_login();

$uid = current_user_id();

$created = $conn->prepare("SELECT COUNT(*) AS total FROM study_groups WHERE admin_id=?");
$created->bind_param("i", $uid);
$created->execute();
$created_count = $created->get_result()->fetch_assoc()["total"];

$joined = $conn->prepare("SELECT COUNT(*) AS total FROM group_members WHERE user_id=? AND role='member'");
$joined->bind_param("i", $uid);
$joined->execute();
$joined_count = $joined->get_result()->fetch_assoc()["total"];

$pending = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM join_requests jr
    JOIN study_groups sg ON jr.group_id = sg.group_id
    WHERE sg.admin_id=? AND jr.status='pending'
");
$pending->bind_param("i", $uid);
$pending->execute();
$pending_count = $pending->get_result()->fetch_assoc()["total"];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Home</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php require "header.php"; ?>
<div class="container">
    <?php flash_show(); ?>

    <div class="hero">
        <h1>Dashboard</h1>
        <p>Manage your study groups from one place.</p>
    </div>

    <div class="grid">
        <div class="card"><h3>Groups Owned</h3><h2><?php echo $created_count; ?></h2></div>
        <div class="card"><h3>Groups Joined</h3><h2><?php echo $joined_count; ?></h2></div>
        <div class="card"><h3>Pending Requests</h3><h2><?php echo $pending_count; ?></h2></div>
    </div>

    <div class="grid">
        <div class="card">
            <h3>Create Group</h3>
            <p>Add a course-interest based study group.</p>
            <a class="btn" href="create_group.php">Create</a>
        </div>
        <div class="card">
            <h3>Browse Groups</h3>
            <p>Search, filter, join, leave and view details.</p>
            <a class="btn" href="groups.php">Browse</a>
        </div>
        <div class="card">
            <h3>Join Requests</h3>
            <p>Approve or reject requests for your groups.</p>
            <a class="btn" href="requests.php">Manage</a>
        </div>
        <div class="card">
            <h3>Merge Groups</h3>
            <p>Combine groups that have the same interest.</p>
            <a class="btn" href="merge_groups.php">Merge</a>
        </div>
    </div>
</div>
</body>
</html>
