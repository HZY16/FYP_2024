<?php
session_start();
include"database.php";

// Fetch user's role from the database
$emaillogin = $_SESSION['email'];
$sqlcheckuser = "SELECT role FROM users WHERE email = ?";
$stmt = $db->prepare($sqlcheckuser);
$stmt->execute([$emaillogin]);
$userData = $stmt->fetch(PDO::FETCH_ASSOC);

 // Store user's rolein session variable
 $_SESSION['role'] = isset($userData['role']) ? $userData['role'] : '';


// Check if the session variable is set
if (isset($_SESSION['email'])) {
    // User is logged in, redirect to their respective homepage based on role
    $role = $_SESSION['role'];
    switch ($role) {
        case 'admin':
            header("Location: admin_home.php");
            break;
        case 'staff':
            header("Location: staff_home.php");
            break;
        case 'member':
            header("Location: patient_home.php");
            break;
        default:
            // Redirect to the login page if role is not recognized
            header("Location: login.html");
            break;
    }
    exit(); // Stop executing further code
} else {
    // User is not logged in, redirect to the login page
    header("Location: login.html");
    exit(); // Stop executing further code
}
?>