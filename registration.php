<?php
    require('database.php');

    if (isset($_REQUEST['email'])) {
        $firstName = strtoupper($_REQUEST['firstName']);
        $lastName = strtoupper($_REQUEST['lastName']);
        $email = $_REQUEST['email'];
        $phoneNumber = $_REQUEST['phoneNumber'];
        $dob = $_REQUEST['dob'];
        $gender = $_REQUEST['gender'];
        $password = $_REQUEST['password'];
        $repassword = $_REQUEST['repassword'];
        $roles = $_REQUEST['roles'];

        // Check if passwords match
        if ($password != $repassword) {
            echo "<script>
                    alert('Your passwords do not match. Please check again!');
                    window.location.href='index.html';
                    </script>";
            exit();
        } else {
            $db = new PDO('mysql:host=localhost;dbname=fyp', 'root', '');

            // Check if email already exists
            $check = $db->prepare("SELECT * FROM users WHERE email = ?");
            $check->execute([$email]);

            if ($check->rowCount() > 0) {
                echo "<script>
                        alert('Email address already exists, please choose another email.');
                        window.location.href='index.html';
                    </script>";
            } else {
                if ($roles == 'staff') {
                    // The user is a staff member, check the staff token
                    if (isset($_REQUEST['stafftoken'])) {
                        $stafftoken = $_REQUEST['stafftoken'];
                        $checkStaffKey = $db->prepare("SELECT * FROM usersstaffkey WHERE staffkey = ? AND used = '0'");
                        $checkStaffKey->execute([$stafftoken]);
                        $staffKeyRow = $checkStaffKey->fetch(PDO::FETCH_ASSOC);
                
                        if ($staffKeyRow) {
                            // Staff key exists and is unused, mark it as used
                            $insert = $db->prepare("INSERT INTO `users` (first_name, last_name, gender, dob, email, phone_number, password, role, register_date) VALUES (?, ?, ?, ?, ?,?,?, ?, NOW())");
                            $insert->execute([$firstName, $lastName, $gender, $dob, $email, $phoneNumber, $password, $roles]);

                            $sql = "SELECT * FROM users WHERE email = ?";
                            $stmt = $db->prepare($sql);
                            $stmt->execute([$email]);
                            $user = $stmt->fetch(PDO::FETCH_ASSOC);
                            $userid = $user['user_id'];

                            $updateStaffKey = $db->prepare("UPDATE usersstaffkey SET used = '1', used_date = NOW(), user_id= '$userid' WHERE staffkey = ?");
                            $updateStaffKey->execute([$stafftoken]);

                            $encryption_key = bin2hex(random_bytes(16));
                            $insert1 = $db->prepare("INSERT INTO `usersactivate` (roles, vkey, used, generate_date, used_date, userid) VALUES (?, ?, ?, NOW(), NULL, ?)");
                            $insert1->execute([$roles, $encryption_key, '0', $userid]);

                            
                            if($insert1){
                                $query1 = $db->prepare("SELECT * FROM usersactivate WHERE userid = $userid");
                                $query1->execute();
                                $result1 = $query1->fetch(PDO::FETCH_ASSOC);
                                $vkey = $result1['vkey'];
    
                                $to = $email;
                                $subject = "MEDVault - Your account activation link";   
                                $message = "Hello Staff, Please click the following link to verify your email : <a href='http://localhost/fyphome/securityEmailVerify.php?vkey=$vkey&userid=$userid'>Click Here</a> to verify." ;
                                $headers = "From: tp063998@gmail.com"; 
                                $headers .= "MEDVault Email Verification Service" . "\r\n";
                                $headers .= "Content-type: text/html;charset=UTF-8" . "\r\n";
            
                                mail($to,$subject,$message,$headers);
                                // Display success message
                                echo "<script>
                                        alert('Your Staff key used successfully. Please verify your account.');
                                        window.location.href='index.html';
                                    </script>";
                                exit();
    
                            }else{
                                //email cannot send out
                                echo "<script>
                                    alert('Email Service somethings wrong, please try again later!' );
                                    window.location.href='index.html';
                                </script>";
                                exit();
                            }
                        } else {
                            // Staff key does not exist or is already used
                            echo "<script>
                                    alert('Error: Your staff key expired! Any issues call Admin!');
                                    window.location.href='index.html';
                                </script>";
                            exit(); // Stop the script if the staff key is invalid
                        }
                    }
                }else{
                    // Hash the password
                    // $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                    // Insert the new user
                    $insert = $db->prepare("INSERT INTO `users` (first_name, last_name, gender, dob, email, phone_number, password, role, register_date) VALUES (?, ?, ?, ?, ?,?,?, ?, NOW())");
                    //$insert->execute([$firstName, $lastName, $email, $phoneNumber, $hashedPassword, $roles]);
                    $insert->execute([$firstName, $lastName, $gender, $dob, $email, $phoneNumber, $password, $roles]);

                    if ($insert) {
                        //bin2hex() 函数是 PHP 中用于将二进制数据转换为十六进制表示的函数。
        
                        $encryption_key = bin2hex(random_bytes(16));
        
                        $sql = "SELECT * FROM users WHERE email = ?";
                        $stmt = $db->prepare($sql);
                        $stmt->execute([$email]);
                        $user = $stmt->fetch(PDO::FETCH_ASSOC);
                        $userid = $user['user_id'];
        
                        $insert1 = $db->prepare("INSERT INTO `usersactivate` (roles, vkey, used, generate_date, used_date, userid) VALUES (?, ?, ?, NOW(), NULL, ?)");
                        $insert1->execute([$roles, $encryption_key, '0', $userid]);

                        if($insert1){
                            $query1 = $db->prepare("SELECT * FROM usersactivate WHERE userid = $userid");
                            $query1->execute();
                            $result1 = $query1->fetch(PDO::FETCH_ASSOC);
                            $vkey = $result1['vkey'];


                            $to = $email;
                            $subject = "MEDVault - Your account activation link";   
                            $message = "Hello Users, Please click the following link to verify your email : <a href='http://localhost/fyphome/securityEmailVerify.php?vkey=$vkey&userid=$userid'>Click Here</a> to verify." ;
                            $headers = "From: tp063998@gmail.com"; 
                            $headers .= "MEDVault Email Verification Service" . "\r\n";
                            $headers .= "Content-type: text/html;charset=UTF-8" . "\r\n";
        
                            mail($to,$subject,$message,$headers);
                            // Display success message
                            echo "<script>
                                    alert('User registered successfully. Please do verify.');
                                    window.location.href='index.html';
                                </script>";
                            exit();
                        }else{
                            //email cannot send out
                            echo "<script>
                                alert('Email Service somethings wrong, please try again later!' );
                                window.location.href='index.html';
                            </script>";
                            exit();
                        }
                    } else {
                        // If there was an error registering the user
                        "<script>
                            alert('There was an error registering the user.');
                            window.location.href='index.html';
                        </script>";
                    }
                }
                
            }
        }
    }
?>
