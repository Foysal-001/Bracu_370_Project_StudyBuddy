<?php
include 'db.php';

$group_id = $_GET['id'];

$sql = "SELECT sg.*, u.name 
        FROM study_groups sg
        JOIN users u ON sg.created_by = u.user_id
        WHERE sg.group_id = '$group_id'";

$result = $conn->query($sql);
$row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Group Created</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h2>Group Created Successfully!</h2>

    <p><strong>Group Name:</strong> <?php echo $row['group_name']; ?></p>
    <p><strong>Subject:</strong> <?php echo $row['subject']; ?></p>
    <p><strong>Description:</strong> <?php echo $row['description']; ?></p>
    <p><strong>Created By:</strong> <?php echo $row['name']; ?></p>

    <br>
    <a href="index.php">Create Another Group</a>
</div>

</body>
</html>