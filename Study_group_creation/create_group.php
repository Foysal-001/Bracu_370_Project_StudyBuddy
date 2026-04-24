<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $group_name = $_POST['group_name'];
    $subject = $_POST['subject'];
    $description = $_POST['description'];
    $user_id = 1;

    $sql = "INSERT INTO study_groups (group_name, subject, description, created_by)
            VALUES ('$group_name', '$subject', '$description', '$user_id')";

    if ($conn->query($sql) === TRUE) {

        $group_id = $conn->insert_id;
        $conn->query("INSERT INTO group_members (group_id, user_id, role)
                      VALUES ('$group_id', '$user_id', 'Admin')");

        header("Location: group_success.php?id=" . $group_id);
        exit();

    } else {
        echo "Error: " . $conn->error;
    }
}
?>
