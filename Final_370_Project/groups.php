<?php
require "db.php";
require_login();

$course = trim($_GET["course"] ?? "");
$interest = trim($_GET["interest"] ?? "");
$keyword = trim($_GET["keyword"] ?? "");

$sql = "
    SELECT sg.*, u.name AS admin_name,
    (SELECT COUNT(*) FROM group_members gm WHERE gm.group_id = sg.group_id) AS total_members
    FROM study_groups sg
    JOIN users u ON sg.admin_id = u.user_id
    WHERE 1
";

$params = [];
$types = "";

if ($course != "") {
    $sql .= " AND LOWER(sg.course) LIKE LOWER(?)";
    $params[] = "%$course%";
    $types .= "s";
}
if ($interest != "") {
    $sql .= " AND LOWER(sg.interest) LIKE LOWER(?)";
    $params[] = "%$interest%";
    $types .= "s";
}
if ($keyword != "") {
    $sql .= " AND (LOWER(sg.group_name) LIKE LOWER(?) OR LOWER(sg.description) LIKE LOWER(?))";
    $params[] = "%$keyword%";
    $params[] = "%$keyword%";
    $types .= "ss";
}

$sql .= " ORDER BY sg.created_at DESC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$groups = $stmt->get_result();
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
    <?php flash_show(); ?>

    <div class="card">
        <h2>Search Groups</h2>
        <form method="GET">
            <div class="grid">
                <div>
                    <label>Course</label>
                    <input type="text" name="course" value="<?php echo safe($course); ?>" placeholder="CSE370">
                </div>
                <div>
                    <label>Interest</label>
                    <input type="text" name="interest" value="<?php echo safe($interest); ?>" placeholder="Database">
                </div>
                <div>
                    <label>Keyword</label>
                    <input type="text" name="keyword" value="<?php echo safe($keyword); ?>" placeholder="Project">
                </div>
            </div>
            <button class="btn" type="submit">Search</button>
            <a class="btn gray" href="groups.php">Clear</a>
            <a class="btn dark" href="home.php">Back</a>
        </form>
    </div>

    <div class="grid">
        <?php while ($g = $groups->fetch_assoc()): ?>
            <div class="card">
                <h3><?php echo safe($g["group_name"]); ?></h3>
                <p><b>Course:</b> <?php echo safe($g["course"]); ?></p>
                <p><b>Interest:</b> <?php echo safe($g["interest"]); ?></p>
                <p><b>Admin:</b> <?php echo safe($g["admin_name"]); ?></p>
                <p><b>Members:</b> <?php echo safe($g["total_members"]); ?></p>
                <a class="btn" href="group_details.php?id=<?php echo $g["group_id"]; ?>">View Details</a>
            </div>
        <?php endwhile; ?>
    </div>
</div>
</body>
</html>
