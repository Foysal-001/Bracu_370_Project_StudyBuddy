<?php
require "db.php";
require_login();

$uid = current_user_id();

$stmt = $conn->prepare("
    SELECT jr.request_id, jr.status, jr.requested_at,
           sg.group_name, sg.group_id,
           u.name, u.email
    FROM join_requests jr
    JOIN study_groups sg ON jr.group_id = sg.group_id
    JOIN users u ON jr.user_id = u.user_id
    WHERE sg.admin_id=? AND jr.status='pending'
    ORDER BY jr.requested_at DESC
");
$stmt->bind_param("i", $uid);
$stmt->execute();
$requests = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Join Requests</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php require "header.php"; ?>
<div class="container">
    <?php flash_show(); ?>

    <div class="card">
        <h2>Pending Join Requests</h2>
        <p class="small">Approving adds the user as a member. Rejecting deducts 5 leaderboard points from the admin.</p>
        <a class="btn gray" href="home.php">Back</a>
    </div>

    <div class="card table-wrap">
        <table>
            <tr>
                <th>Group</th>
                <th>User</th>
                <th>Email</th>
                <th>Requested At</th>
                <th>Action</th>
            </tr>
            <?php while ($r = $requests->fetch_assoc()): ?>
            <tr>
                <td><?php echo safe($r["group_name"]); ?></td>
                <td><?php echo safe($r["name"]); ?></td>
                <td><?php echo safe($r["email"]); ?></td>
                <td><?php echo safe($r["requested_at"]); ?></td>
                <td>
                    <form method="POST" action="handle_request.php" style="display:inline;">
                        <input type="hidden" name="request_id" value="<?php echo $r["request_id"]; ?>">
                        <input type="hidden" name="action" value="approve">
                        <button class="btn green" type="submit">Approve</button>
                    </form>
                    <form method="POST" action="handle_request.php" style="display:inline;">
                        <input type="hidden" name="request_id" value="<?php echo $r["request_id"]; ?>">
                        <input type="hidden" name="action" value="reject">
                        <button class="btn red" type="submit">Reject</button>
                    </form>
                    <a class="btn gray" href="group_details.php?id=<?php echo $r["group_id"]; ?>">View</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</div>
</body>
</html>
