<?php
require('database.php');

// Check if user_id and timestamp parameters are provided
if(isset($_GET['user_id']) && isset($_GET['timestamp'])) {
    // Retrieve user_id and timestamp from the URL parameters
    $user_id = $_GET['user_id'];
    $timestamp = $_GET['timestamp'];

    // Get the current time
    $current_time = time();

    // Calculate the time difference
    $time_difference = $current_time - $timestamp;

    // Define the validity duration (2 minutes)
    $validity_duration = 120; // 2 minutes in seconds

    // Check if the time difference is within the validity duration
    if ($time_difference <= $validity_duration) {
        // Update the users table to deactivate the account
        $stmt = $con->prepare("UPDATE users SET acc_activated = 0, last_modify_date = NOW() WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();

        // Redirect to a confirmation page or display a success message
        echo "<script>
                alert('Your account has been deactivated successfully. Please go to forget password to change password!');
                window.location.href='index.html';
            </script>";
    } else {
        // Redirect to an error page or display an error message for expired link
        echo "<script>
                alert('Expired link. Please try again.');
                window.location.href='index.html';
            </script>";
    }
} else {
    // Redirect to an error page or display an error message for invalid request
    echo "<script>
            alert('Invalid request. Please try again.');
            window.location.href='index.html';
        </script>";
}
?>
