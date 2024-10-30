<?php
require('database.php');

    if (isset($_POST['email'])){
        $email = $_REQUEST['email'];
        
        // Prepare SQL query to check if email exists
        $sql_email = "SELECT email FROM users WHERE email = ?";
        $stmt_email = $db->prepare($sql_email);
        $stmt_email->execute([$email]);
        
        // Fetch the email from the result
        $user_email = $stmt_email->fetchColumn();

        // Check if email exists
        if ($user_email) {

            // Prepare SQL query to check if email exists in tempkey table
            $sql_check_tempkey = "SELECT COUNT(*) FROM tempkey WHERE email = ?";
            $stmt_check_tempkey = $db->prepare($sql_check_tempkey);
            $stmt_check_tempkey->execute([$email]);
            $tempkey_count = $stmt_check_tempkey->fetchColumn();

            // Generate a temporary key
            $temp_key = bin2hex(random_bytes(16)); // Generates a random 32-character hexadecimal string
            
            // Store the temporary key in the database
            if ($tempkey_count > 0) {
                // Email already exists in tempkey table, update the temporary key
                $sql_update_temp_key = "UPDATE tempkey SET tkey = ? WHERE email = ?";
                $stmt_update_temp_key = $db->prepare($sql_update_temp_key);
                $stmt_update_temp_key->execute([$temp_key, $email]);
            } else {
                // Email doesn't exist in tempkey table, insert a new row
                $sql_insert_temp_key = "INSERT INTO tempkey (email, tkey) VALUES (?, ?)";
                $stmt_insert_temp_key = $db->prepare($sql_insert_temp_key);
                $stmt_insert_temp_key->execute([$email, $temp_key]);
            }


            // Send email with the reset link containing the temporary key
            


            $reset_link = 'http://localhost/fyphome/forgotpasswordreset.php?email=' . urlencode($email) . '&key=' . $temp_key;
            $to = $user_email;
            $subject = "MEDVault - Your account has been reset.";   
            $message = "Hello users, Please click the following link to reset password your account : <a href='$reset_link'>Click Here</a> to reset." ;
            $headers = "From: tp063998@gmail.com"; 
            $headers .= "MEDVault Email Verification Service" . "\r\n";
            $headers .= "Content-type: text/html;charset=UTF-8" . "\r\n";

            $mail_sent = mail($to,$subject,$message,$headers);

            if ($mail_sent) {
                // Alert the user to check their email
                echo "<script>
                    alert('Please Check Your Email.');
                    window.location.href='index.html';
                </script>";
            } else {
                // Alert the user if the email failed to send
                echo "<script>
                    alert('Failed to send email. Please try again later.');
                    window.location.href='index.html';
                </script>";
            }

        } else {
            // Email does not exist, inform the user
            echo "<script>
                alert('The provided email is invalid.' );
                window.location.href='index.html';
            </script>";
        }
        
    }
?>


