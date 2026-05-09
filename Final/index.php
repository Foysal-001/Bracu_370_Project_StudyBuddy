<?php require "db.php"; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Study Group System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php require "header.php"; ?>

<div class="container">
    <div class="hero">
        <h1>CSE370 Study Group Management System</h1>
        <p>A simple PHP and MySQL project for creating, joining, scheduling and merging study groups.</p>
    </div>

    <div class="grid">
        <div class="card">
            <h3>Create Groups</h3>
            <p>Students can create study groups by topic.</p>
        </div>

        <div class="card">
            <h3>Join Requests</h3>
            <p>Students can request to join groups and admins can approve or reject.</p>
        </div>

        <div class="card">
            <h3>Schedule and Merge</h3>
            <p>Admins can set schedules and merge their own groups.</p>
        </div>
    </div>
</div>

</body>
</html>
