<?php
include 'db.php';

$search = "";
$hasSearched = false;
$result = null;

if (isset($_GET['search']) && trim($_GET['search']) !== "") {
    $hasSearched = true;
    $search = trim($_GET['search']);

    $sql = "SELECT * FROM study_groups 
            WHERE LOWER(group_name) LIKE LOWER(?) 
            OR LOWER(subject) LIKE LOWER(?)";

    $stmt = mysqli_prepare($conn, $sql);
    $likeSearch = "%" . $search . "%";

    mysqli_stmt_bind_param($stmt, "ss", $likeSearch, $likeSearch);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Search Study Groups</title>
    <link rel="stylesheet" href="style2.css">
</head>
<body>

<div class="page-box">

    <h2>Search Study Groups</h2>
    <p class="subtitle">Find groups by group name or subject</p>

    <form class="search-form" method="GET" action="search_groups.php">
        <input type="text" name="search"
               placeholder="Example: CSE370 or Database"
               value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit">Search</button>
    </form>

    <?php if (!$hasSearched) { ?>

        <p class="hint">Search something to see available groups.</p>

    <?php } else { ?>

        <h3 class="result-title">
            Results for "<?php echo htmlspecialchars($search); ?>"
        </h3>

        <?php
        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                ?>

                <div class="group-card">
                    <h3><?php echo htmlspecialchars($row['group_name']); ?></h3>

                    <p><b>Subject:</b> <?php echo htmlspecialchars($row['subject']); ?></p>

                    <p class="description">
                        <?php echo htmlspecialchars($row['description']); ?>
                    </p>

                    <form action="send_request.php" method="POST">
                        <input type="hidden" name="group_id" value="<?php echo $row['group_id']; ?>">
                        <button class="join-btn" type="submit">Join Request</button>
                    </form>
                </div>

                <?php
            }
        } else {
            echo "<p class='not-found'>No matching groups found.</p>";
        }
        ?>

    <?php } ?>

</div>

</body>
</html>