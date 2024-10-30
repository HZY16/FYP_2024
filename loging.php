<!DOCTYPE html>
<html>
<head>
    <title>Login with 2FA</title>
    <link href="css/styles.css" rel="stylesheet" />
</head>
<body>
    <?php
    require('database.php');
    session_start();

    if (isset($_POST['email'])) {
        $email = $_REQUEST['email'];
        $password = $_REQUEST['password'];

        // Query to check if the email exists in the database
        $query = "SELECT * FROM `users` WHERE email='$email' AND role IN ('member', 'staff', 'admin')";
        $result = mysqli_query($con, $query) or die(mysqli_error($con));
        $rows = mysqli_num_rows($result);

        // Check if the email exists in the users table
        if ($rows == 1) {
            $user = mysqli_fetch_assoc($result);

            // Check if the password matches
            if ($password === $user['password']) {
                // Check if 2FA is enabled for the user
                $user_id = $user['user_id'];
                $user_acc_activated = $user['acc_activated'];
                $stmt_check_2fa = $con->prepare("SELECT 2fa_function FROM users2fa WHERE user_id = ?");
                $stmt_check_2fa->bind_param("i", $user_id);
                $stmt_check_2fa->execute();
                $stmt_check_2fa->bind_result($twofa_status);
                $stmt_check_2fa->fetch();
                $stmt_check_2fa->close();

                // If 2FA is enabled, prompt for OTP verification
                if ($twofa_status == 1 && $user_acc_activated ==1 ) {
                    function generateOTP($length = 16)
                    {
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

                    // Handle OTP verification
                    if (isset($_POST['verify_otp'])) {
                        $user_otp = $_POST['verification_code'];
                        $otp = $_SESSION['otp'];
                        if ($user_otp == $otp) {
                            // OTP is valid, proceed with login
                            $_SESSION['email'] = $_POST['email'];
                            echo "<script>alert('Correct OTP!');</script>";
                            header("Location: securitychecklogin.php");
                            exit();
                        } else {
                            // Invalid OTP
                            echo "<script>alert('Invalid OTP. Please try again.');</script>";
                        }
                    }

                    // Handle OTP sending
                    if (isset($_POST['get_otp'])) {
                        $otp = generateOTP();
                        $_SESSION['otp'] = $otp;

                        $current_timestamp = time();

                        $to = $email;
                        $subject = "MEDVault - Alert you request OTP!";
                        $message = "Hello user, your OTP is <kbd><strong>$otp</strong></kbd>, if this not your action please <a href='http://localhost/fyphome/securitydeactivateaccount.php?user_id=$user_id&timestamp=$current_timestamp'>Click Me (2 minutes to expiration)</a> to deactivate your account, go to forget password to change your account.";
                        $headers = "From: tp063998@gmail.com\r\n";
                        $headers .= "Content-type: text/html;charset=UTF-8\r\n";

                        if (mail($to, $subject, $message, $headers)) {
                            echo "<script>alert('Successful to send OTP. Please check your email.');</script>";
                        } else {
                            echo "<script>alert('Failed to send OTP. Please try again.');</script>";
                        }
                    }

                    // Display the form
                    echo "<div class='container'>";
                    echo "<div class='col-xl-12 col-lg-12 col-md-12'>";
                    echo "<div class='p-6'>";
                    echo "<div class='text-center'>";
                    echo "<h1 class='h4 text-gray-900 mb-4'>Login to MEDVault System With 2FA</h1";
                    echo "</div><br><br>";
                    echo "<form method='post'>";
                  
                    echo "<input type='hidden' name='email' value='$email'>";
                    echo "<input type='hidden' name='password' value='$password'>";
                    echo "<input type='text' name='verification_code' placeholder='Enter OTP'>";
                    echo "<button type='submit' name='get_otp'>Get OTP</button><br><br>";
                    echo "<button type='submit' class='btn btn-success' name='verify_otp'>Verify OTP</button><br><br>";
                    echo "<a href='index.html'>Back to Homepage</a>";
                    echo "</form>";
                    echo "</div>";
                    echo "</div>";
                    echo "</div>";
                } else {
                    // 2FA is not enabled, proceed with login
                    $_SESSION['email'] = $email;
                    if ($user['acc_activated'] == 1) {
                        switch ($user['role']) {
                            case 'member':
                                header("Location: patient_home.php");
                                break;
                            case 'staff':
                                header("Location: staff_home.php");
                                break;
                            case 'admin':
                                header("Location: admin_home.php");
                                break;
                        }
                        exit();
                    } else {
                        echo "<script>
                                alert('Your account is disabled/not activated. Please try forget password to activate it back. If you have any queries, please contact us.');
                                window.location.href='login.html';
                            </script>";
                            session_destroy();
                    }
                }
            } else {
                // Incorrect password
                echo "<script>
                        alert('Incorrect password. Please try again.');
                        window.location.href='login.html';
                    </script>";
            }
        } else {
            // Email does not exist
            echo "<script>
                    alert('Your account does not exist. Please register.');
                    window.location.href='login.html';
                </script>";
        }
    }
    ?>
</body>
</html>