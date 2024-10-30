<?php
    session_start();
    include "database.php";

    // Function to require the user to login + Function to check if the user is logged in
        function isLoggedIn() {
            return isset($_SESSION['email']);
        }

        function requireLogin() {
            if (!isLoggedIn()) {
                // If the user is not logged in, display a warning message
                echo '<script>alert("Your Session is expired, You required to login first!");</script>';
                // Then redirect to the login page
                echo '<script>window.location.href = "index.html";</script>';
                session_destroy();
                exit;
            }
        }

    // Function to restrict access based on user role
        function restrictAccessByRole($allowedRoles) {
            // Check if the user's role is allowed to access the page
            if (!in_array($_SESSION['role'], $allowedRoles)) {
                // If the user's role is not allowed, display a warning message
                echo '<script>alert("You do not have permission to access this page!");</script>';
                // Then redirect to a suitable page
                //echo '<script>window.location.href = "index.html";</script>';
                echo '<script> window.history.back();</script>';
                exit;
            }
        }

        requireLogin();
        isLoggedIn();


    // Fetch user's role from the database
        $emaillogin = $_SESSION['email'];
        $sqlcheckuser = "SELECT role, user_id, acc_activated FROM users WHERE email = ?";
        $checkuser = $con->prepare($sqlcheckuser);
        $checkuser->bind_param("s", $emaillogin);
        $checkuser->execute();
        $resultofuser = $checkuser->get_result();
        $userData = $resultofuser->fetch_assoc();

    // Store user's role/user id in session variable
        $_SESSION['role'] = isset($userData['role']) ? $userData['role'] : '';
        $_SESSION['user_id'] = isset($userData['user_id']) ? $userData['user_id'] : '';
        $_SESSION['acc_activated'] = isset($userData['acc_activated']) ? $userData['acc_activated'] : '';

        if (!$userData) {          
            echo "<script>
                    alert('No user found with the provided email.');
                    window.location.href='index.html';
                </script>";
        } else {
            $userRole = $userData['role'];
            $userID = $userData['user_id'];
            $acc_activated = $userData['acc_activated'];
            //echo "This line from securityauth.php: Your Role is: " . $userRole . ", and Your User ID is: " . $userID, "Account Activated: " . $acc_activated; 
        }




        
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>

<script>
    var sessionTimeoutDuration = 300000; // 300 seconds = 5 Minutes
    var logoutTimer;

    function resetLogoutTimer() {
        clearTimeout(logoutTimer); // Reset the timer
        logoutTimer = setTimeout(logoutUser, sessionTimeoutDuration); // Set a new timer
        
        // Calculate remaining time
        var remainingTime = sessionTimeoutDuration - 10000; // 10 seconds before timeout
        
        // Show popup notification 5 seconds before timeout
        setTimeout(showPopupNotification, remainingTime);
    }

    function showPopupNotification() {
        // Create a popup notification
        var popup = document.createElement('div');
        popup.innerHTML = 'Your session will expire in 10 seconds. Please refresh to close the notification.';
        popup.style.position = 'fixed';
        popup.style.top = '10px';
        popup.style.left = '50%';
        popup.style.transform = 'translateX(-50%)';
        popup.style.padding = '10px';
        popup.style.background = '#f8d7da';
        popup.style.border = '1px solid #f5c6cb';
        popup.style.borderRadius = '5px';
        popup.style.color = '#721c24';
        popup.style.zIndex = '9999';
        document.body.appendChild(popup);
        
        // Close popup when clicked anywhere
        document.addEventListener('click', closePopupNotification);
    }

    function closePopupNotification() {
        // Remove popup notification
        var popup = document.querySelector('.popup-notification');
        if (popup) {
            popup.parentNode.removeChild(popup);
        }
    }

    function logoutUser() {
        // Perform logout action here, such as redirecting to the logout page
        window.location.href = 'logout.php';
    }

    // Add event listeners to track user activity
    document.addEventListener('mousemove', resetLogoutTimer);
    document.addEventListener('keypress', resetLogoutTimer);

    // Start the timer when the page loads
    window.onload = resetLogoutTimer;
    </script>

</body>
</html>
