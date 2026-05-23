<?php
require "db.php";
require_login();

$query = "SELECT leaderboard.*, user.reject_penalty
          FROM leaderboard
          JOIN user ON leaderboard.user_id = user.user_id
          ORDER BY leaderboard.score DESC";

$result = mysqli_query($database, $query);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Leaderboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php require "header.php"; ?>

<div class="container card">
    <h2>Leaderboard</h2>

    <p class="small">
        +10 points for creating a group<br>
        +5 points when admin approves a join request<br>
        +20 points when admin merges groups<br>
        -5 points if user gets rejected to join a group<br>
        Reject penalty is stored in the user table
    </p>

    <div class="table-wrap">
        <table>
            <tr>
                <th>Username</th>
                <th>Score</th>
                <th>Reject Penalty</th>
            </tr>

            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?php echo $row['username']; ?></td>
                    <td><?php echo $row['score']; ?></td>
                    <td><?php echo $row['reject_penalty']; ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>
</div>

</body>
</html>
