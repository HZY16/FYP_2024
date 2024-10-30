<?php
session_start();

include("securityauth.php");
include("database.php");


// Check if the users2fa table contains the userID
$stmt_check_user = $con->prepare("SELECT COUNT(*) FROM users2fa WHERE user_id = ? and 2fa_function = 1 ");
$stmt_check_user->bind_param("i", $userID);
$stmt_check_user->execute();
$stmt_check_user->bind_result($user_count);
$stmt_check_user->fetch();
$stmt_check_user->close();

// If the userID exists, prevent the user from getting the OTP
if ($user_count > 0) {
    echo "<script>
            alert('You have already activated two-factor authentication.');
            window.location.href = 'settingSecurity.php'; 
          </script>";
    exit; // Stop further execution of the script
}

function generateOTP($length = 16) {
    // Define the characters to be used for generating OTP
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*';

    // Get the total number of characters
    $charactersLength = strlen($characters);

    // Initialize an empty string to store the OTP
    $otp = '';

    // Generate random OTP characters
    for ($i = 0; $i < $length; $i++) {
        $otp .= $characters[rand(0, $charactersLength - 1)];
    }

    // Return the generated OTP
    return $otp;
}

// Generate OTP
$otp = generateOTP();

// Send OTP to user's email
$to = $emaillogin; // Assuming $emaillogin is defined somewhere
$subject = "MEDVault - Activate 2FA Function";
$message = "Hello user, your OTP is <kbd><strong>$otp</strong></kbd> ";
$headers = "From: tp063998@gmail.com\r\n";
$headers .= "Content-type: text/html;charset=UTF-8\r\n";

if (mail($to, $subject, $message, $headers)) {
    // Save OTP to session for verification
    $_SESSION['otp'] = $otp;
    //echo json_encode(array('success' => true));
    echo "<script>alert('Successful Send OTP)</script>";
} else {
    //echo json_encode(array('success' => false));
    echo "<script>alert('Unsuccessful Send OTP)</script>";
}

exit; // Terminate the script
?>
