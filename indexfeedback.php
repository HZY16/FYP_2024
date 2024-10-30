<?php
require('database.php');


if (isset($_REQUEST['email'])) {
    $fullName = strtoupper($_REQUEST['name']);
    $email = $_REQUEST['email'];
    $phoneNumber = $_REQUEST['phoneNumber'];
    $subject = $_REQUEST['subject'];
    $feedback = $_REQUEST['message'];
    $status = 'Wait for Support';
    

    $db = new PDO('mysql:host=localhost;dbname=fyp', 'root', '');

    $insert = $db->prepare("INSERT INTO `homefeedback` (full_name, email, phone_number, subject, feedback, status ,feedback_date) VALUES (?, ?, ?, ?, ?, ?, NOW())");
    $insert->execute([$fullName, $email, $phoneNumber, $subject, $feedback, $status]);
    
    
    if ($insert) {

        echo "<script>
            alert('Messages successfully.');
            window.history.back();
            //windows.location.href='index.html';
          </script>";
            exit();
    }else {
        "<script>
            alert('Try again later.');
            window.location.href='index.html';
          </script>";
          exit();
    }
    }
?>
