<?php
    include("securityauth.php");
    restrictAccessByRole(['admin']);
    include("database.php");

    if (isset($_GET['id'])) {
        $userid = $_GET['id'];

        // Generate random password (8 characters)
        $randomPassword = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8);

        // Update the user's password and set acc_activated to 0
        $sql = "UPDATE users SET password = ?, acc_activated = 0, last_modify_date = NOW() WHERE user_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$randomPassword, $userid]);

        //Retrieve user role
        $sql_role = "SELECT role FROM users WHERE user_id = ?";
        $stmt_role = $db->prepare($sql_role);
        $stmt_role->execute([$userid]);
        $user_role = $stmt_role->fetchColumn();

        //Retrieve user email
        $sql_email = "SELECT email FROM users WHERE user_id = ?";
        $stmt_email = $db->prepare($sql_email);
        $stmt_email->execute([$userid]);
        $user_email = $stmt_email->fetchColumn();

        // Check if user exists in users2fa table and 2fa_function is 1
        $query2fa = $db->prepare("SELECT 2fa_function FROM users2fa WHERE user_id = ?");
        $query2fa->execute([$userid]);
        $result2fa = $query2fa->fetch(PDO::FETCH_ASSOC);
        if ($result2fa && $result2fa['2fa_function'] == 1) {
            // Update 2fa_function to 0
            $update2fa = $db->prepare("UPDATE users2fa SET 2fa_function = 0, endisabletime=NOW() WHERE user_id = ?");
            $update2fa->execute([$userid]);
        }

        // Delete all data associated with the user_id in usersactivate table
        $deleteUserActivate = $db->prepare("DELETE FROM usersactivate WHERE userid = ?");
        $deleteUserActivate->execute([$userid]);
        

        // Generate VKey
        $encryption_key = bin2hex(random_bytes(16));
        $insert1 = $db->prepare("INSERT INTO `usersactivate` (roles, vkey, used, generate_date, used_date, userid) VALUES (?, ?, ?, NOW(), NULL, ?)");
        $insert1->execute([$user_role, $encryption_key, '0', $userid]);


        if($insert1){
            $query1 = $db->prepare("SELECT * FROM usersactivate WHERE userid = $userid");
            $query1->execute();
            $result1 = $query1->fetch(PDO::FETCH_ASSOC);
            $vkey = $result1['vkey'];

            $to = $user_email;
            $subject = "MEDVault - Your account has been reset.";   
            $message = "Hello users, Please click the following link to reactivated your account : <a href='http://localhost/fyphome/securityEmailVerifyReset.php?vkey=$vkey&userid=$userid'>Click Here</a> to verify." ;
            $headers = "From: tp063998@gmail.com"; 
            $headers .= "MEDVault Email Verification Service" . "\r\n";
            $headers .= "Content-type: text/html;charset=UTF-8" . "\r\n";

            mail($to,$subject,$message,$headers);
            // Display success message
            echo "<script>
                    alert('User reset successfully. Vkey = $encryption_key' );
                    window.location.href='admin_manageuser.php';
            </script>";
            exit();
        } else{
            //email cannot send out
            echo "<script>
                alert('Email Service somethings wrong, please try again later!' );
                window.location.href='admin_manageuser.php';
            </script>";
            exit();
        }
        
        } else {
        // Handle case when user ID is not provided
        echo "<script>
            alert('Handle case when user ID is not provided. Unsuccessful Reset.');
            window.location.href='admin_manageuser.php';
         </script>";

    }

    
?>
