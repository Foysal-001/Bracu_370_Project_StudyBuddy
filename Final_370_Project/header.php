<?php
$user_name = $_SESSION['name'] ?? "";
?>
<div class="navbar">
    <div class="logo">Study Group System</div>
    <div>
        <?php if (logged_in()): ?>
            <span class="small">Hello, <?php echo safe($user_name); ?></span>
            <a href="home.php">Home</a>
            <a href="groups.php">Groups</a>
            <a href="leaderboard.php">Leaderboard</a>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="index.php">Start</a>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        <?php endif; ?>
    </div>
</div>
