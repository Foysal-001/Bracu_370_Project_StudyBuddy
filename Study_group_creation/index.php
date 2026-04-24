<!DOCTYPE html>
<html>
<head>
    <title>Create Study Group</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h2>Create Study Group</h2>

    <form action="create_group.php" method="POST">

        <label>Group Name</label>
        <input type="text" name="group_name" required>

        <label>Subject</label>
        <input type="text" name="subject" required>

        <label>Description</label>
        <textarea name="description" required></textarea>

        <button type="submit">Create Group</button>

    </form>
</div>

</body>
</html>