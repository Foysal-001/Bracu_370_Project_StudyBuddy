<?php
require "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT user_id, name, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row["password"])) {
            $_SESSION["user_id"] = $row["user_id"];
            $_SESSION["name"] = $row["name"];
            go("home.php");
        }
    }

    flash_set("Invalid email or password.");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php require "header.php"; ?>
<div class="container auth-box">
    <?php flash_show(); ?>
    <div class="card">
        <h2>Login</h2>
        <form method="POST">
            <label>Email</label>
            <input type="email" name="email" required>

            <label>Password</label>
            <input type="password" name="password" required>

            <button class="btn" type="submit">Login</button>
            <a class="btn gray" href="index.php">Back</a>
        </form>
    </div>
</div>
</body>
</html>
