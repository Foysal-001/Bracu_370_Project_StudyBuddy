<div class="navbar">
    <div class="logo">Study Group System</div>

    <div>
        <a href="index.php">Home</a>

        <?php if (isset($_SESSION['user_id'])) { ?>
            <a href="home.php">Dashboard</a>
            <a href="create_group.php">Create Group</a>
            <a href="groups.php">Groups</a>
            <a href="schedule.php">Schedule</a>
            <a href="requests.php">Requests</a>
            <a href="leaderboard.php">Leaderboard</a>
            <a href="merge_groups.php">Merge</a>
            <a href="logout.php">Logout</a>
        <?php } else { ?>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        <?php } ?>
    </div>
</div>
