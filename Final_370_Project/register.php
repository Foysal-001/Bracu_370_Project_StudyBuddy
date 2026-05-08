<?php
require "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if ($name == "" || $email == "" || $password == "") {
        flash_set("All fields are required.");
    } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $hashed);

        if ($stmt->execute()) {
            flash_set("Account created. You can login now.");
            go("login.php");
        } else {
            flash_set("Email already exists or something went wrong.");
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php require "header.php"; ?>
<div class="container auth-box">
    <?php flash_show(); ?>
    <div class="card">
        <h2>Create Account</h2>
        <form method="POST">
            <label>Name</label>
            <input type="text" name="name" required>

            <label>Email</label>
            <input type="email" name="email" required>

            <label>Password</label>
            <input type="password" name="password" required>

            <button class="btn" type="submit">Register</button>
            <a class="btn gray" href="index.php">Back</a>
        </form>
    </div>
</div>
</body>
</html>
