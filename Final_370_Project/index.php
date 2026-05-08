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
        <h1>Study Group Management System</h1>
        <p>Create groups, search by course, send join requests, approve members, schedule meetings, view leaderboard and merge duplicate/similar interest groups.</p>
        <br>
        <a class="btn" href="login.php">Login</a>
        <a class="btn gray" href="register.php">Create Account</a>
    </div>

    <div class="grid">
        <div class="card"><h3>Search</h3><p>Find groups by course, interest or keyword.</p></div>
        <div class="card"><h3>Requests</h3><p>Admins approve or reject pending join requests.</p></div>
        <div class="card"><h3>Schedule</h3><p>Admins can add meeting dates, times and notes.</p></div>
        <div class="card"><h3>Leaderboard</h3><p>Scores are calculated from real group activity.</p></div>
    </div>
</div>
</body>
</html>
