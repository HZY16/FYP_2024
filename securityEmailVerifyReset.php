<?php
    require('database.php');

    if(isset($_GET['vkey'])){
        //Process Verification
        $vkey = $_GET['vkey'];
        $userid = $_GET['userid'];

        $query = "SELECT used, vkey FROM usersactivate WHERE used = 0 AND vkey = '$vkey' AND userid = '$userid' LIMIT 1";
        $resultSet = mysqli_query($con, $query);
        $rows = mysqli_num_rows($resultSet);

        if($rows == 1){
            //Validate The Email
            $update = mysqli_query($con, "UPDATE usersactivate SET used = 1, used_date = NOW() WHERE vkey = '$vkey' AND userid = '$userid' LIMIT 1");
            $update1 = mysqli_query($con, "UPDATE users SET acc_activated = 1 WHERE  user_id = '$userid' LIMIT 1");
            
        

            //Retrieve user password
            $sql_password = "SELECT password FROM users WHERE user_id = ?";
            $stmt_password = $db->prepare($sql_password);
            $stmt_password->execute([$userid]);
            $user_password = $stmt_password->fetchColumn();

            //Retrieve user email
            $sql_email = "SELECT email FROM users WHERE user_id = ?";
            $stmt_email = $db->prepare($sql_email);
            $stmt_email->execute([$userid]);
            $user_email = $stmt_email->fetchColumn();



            $to = $user_email;
            $subject = "MEDVault - Your account has been reset.";   
            $headers = "From: tp063998@gmail.com\r\n";
            //$headers .= "Reply-To: tp063998@gmail.com\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            $headers .= "MEDVault Email Verification Service" . "\r\n";

            //$message = "Hello users,\r\n Your account has been verified，your temporary password is <strong>$user_password</strong>, Please change your password.\r\n\r\n\r\n\r\n" ;
            $message = "Hello users,\r\n Your account has been verified, Please click 'Forget Password' to reset your password!.\r\n\r\n\r\n\r\n" ;

            // Sending the email
            if (mail($to, $subject, $message, $headers)) {
                // Email sent successfully
                echo "<script>
                        alert('Your account has been verified. An email has been sent with further instructions.');
                        window.location.href='index.html';
                    </script>";
                exit();
            } else {
                // Email sending failed
                echo "<script>
                        alert('Failed to send email. Please try again later or contact support.');
                        window.location.href='index.html';
                    </script>";
                exit();
            }
            
        }else{
            echo "<script>
                    alert('Your Verify Key is expired, please contact us to assist you.');
                    window.location.href='index.html';
                </script>";
            exit();
        }
    }else{
        echo "<script>
                alert('Something went wrong.');
                window.location.href='index.html';
            </script>";
        exit();
    }
?>