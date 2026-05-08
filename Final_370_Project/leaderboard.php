<?php
require "db.php";
require_login();

$result = $conn->query("
    SELECT 
        u.user_id,
        u.name,
        u.email,
        COALESCE(owned.total_owned, 0) AS owned_groups,
        COALESCE(membered.total_joined, 0) AS joined_groups,
        COALESCE(merged.total_merged, 0) AS merged_groups,
        COALESCE(u.reject_penalty, 0) AS reject_penalty,
        (
            COALESCE(owned.total_owned, 0) * 10 +
            COALESCE(membered.total_joined, 0) * 5 +
            COALESCE(merged.total_merged, 0) * 20 -
            COALESCE(u.reject_penalty, 0)
        ) AS total_score
    FROM users u
    LEFT JOIN (
        SELECT admin_id, COUNT(*) AS total_owned
        FROM study_groups
        GROUP BY admin_id
    ) owned ON owned.admin_id = u.user_id
    LEFT JOIN (
        SELECT user_id, COUNT(*) AS total_joined
        FROM group_members
        WHERE role='member'
        GROUP BY user_id
    ) membered ON membered.user_id = u.user_id
    LEFT JOIN (
        SELECT merged_by, COUNT(*) AS total_merged
        FROM merge_logs
        GROUP BY merged_by
    ) merged ON merged.merged_by = u.user_id
    ORDER BY total_score DESC, u.name ASC
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Leaderboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php require "header.php"; ?>
<div class="container">
    <div class="card">
        <h2>Leaderboard</h2>
        <div class="notice">
            <b>Leaderboard calculation:</b><br>
            10 points for each group owned by a user.<br>
            20 points for each successful group merge done by the owner/admin.<br>
            5 points for each group joined as a member.<br>
            5 points deducted from an admin whenever the admin rejects a join request.
        </div>
        <a class="btn gray" href="home.php">Back</a>
    </div>

    <div class="card table-wrap">
        <table>
            <tr>
                <th>Rank</th>
                <th>User</th>
                <th>Owned</th>
                <th>Joined</th>
                <th>Merged</th>
                <th>Penalty</th>
                <th>Total Score</th>
            </tr>
            <?php $rank = 1; while ($u = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $rank++; ?></td>
                <td>
                    <b><?php echo safe($u["name"]); ?></b><br>
                    <span class="small"><?php echo safe($u["email"]); ?></span>
                </td>
                <td><?php echo safe($u["owned_groups"]); ?> × 10</td>
                <td><?php echo safe($u["joined_groups"]); ?> × 5</td>
                <td><?php echo safe($u["merged_groups"]); ?> × 20</td>
                <td>-<?php echo safe($u["reject_penalty"]); ?></td>
                <td class="score"><?php echo safe($u["total_score"]); ?></td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</div>
</body>
</html>
