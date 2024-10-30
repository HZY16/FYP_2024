<?php
include("securityauth.php");
restrictAccessByRole(['admin']);
include("database.php");

if (isset($_GET['table'])) {
    $table = $_GET['table'];

    // Define SQL query based on the table and condition
    if ($table === 'users') {
        $sql = "DELETE FROM users WHERE acc_activated = 0";
        $anchor = "#allusers";
    } elseif ($table === 'usersstaffkey') {
        $sql = "DELETE FROM usersstaffkey WHERE used = 1";
        $anchor = "#stafftoken";
    } elseif ($table === 'usersactivate') {
        $sql = "DELETE FROM usersactivate WHERE used = 1";
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