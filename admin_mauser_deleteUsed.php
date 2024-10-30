<?php
include("securityauth.php");
restrictAccessByRole(['admin']);
include("database.php");

if (isset($_GET['table'])) {
    $table = $_GET['table'];

    // Define SQL query based on the table and condition
    if ($table === 'usersstaffkey') {
        $sql = "DELETE FROM usersstaffkey WHERE used = 0";
        $anchor = "#stafftoken";
    } elseif ($table === 'usersactivate') {
        $sql = "DELETE FROM usersactivate WHERE used = 0";
        $anchor = "#activatedtoken";
    }

    try {
        // Prepare and execute the SQL query
        $stmt = $db->prepare($sql);
        $stmt->execute();

        // Redirect back to the appropriate tab after deletion
        header("Location: admin_manageuser.php" . $anchor);
        exit();
    } catch (PDOException $e) {
        // Handle any PDO exceptions (database errors)
        echo "Error: " . $e->getMessage();
    }
}
?>