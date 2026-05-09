<?php
require "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $username = $_POST['username'];
    $major = $_POST['major'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $query = "INSERT INTO user(email, username, major, password)
              VALUES('$email', '$username', '$major', '$password')";

    if (mysqli_query($database, $query)) {
        header("Location: login.php");
        exit();
    } else {
        $error = "Registration failed. Email or username may already exist.";
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

<div class="container auth-box card">
    <h2>Register</h2>

    <?php if (isset($error)) { ?>
        <div class="notice"><?php echo $error; ?></div>
    <?php } ?>

    <form method="POST">
        <label>Email</label>
        <input type="email" name="email" required>

        <label>Username</label>
        <input type="text" name="username" required>

        <label>Major</label>
        <input type="text" name="major">

        <label>Password</label>
        <input type="password" name="password" required>

        <button class="btn">Register</button>
    </form>
</div>

</body>
</html>
