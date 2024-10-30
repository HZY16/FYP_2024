<?php
    //do connection to databse and security(Login Checked)
        include("securityauth.php");
        include("database.php");
        //include("databasegetdata.php");


    // Store user login email into $emaillogin
        $emaillogin = $_SESSION['email'];

    
    // Read data from database
        // Define the fields to be selected and Prepare the SQL query
        $fields = ['user_id','first_name', 'last_name', 'email', 'phone_number', 'dob', 'gender', 'password', 'role','acc_activated'];
        $sql = "SELECT " . implode(', ', $fields) . " FROM users WHERE email = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("s", $emaillogin);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        $userid = $user['user_id'];
        //echo "Your User ID is: " . $userid;
        $userrole = $user['role'];
        //echo "Your Role is: " . $userrole;


    //Update password
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_password'])) {
        $old_password = $_POST['oldpassword'];
        $new_password = $_POST['newpassword'];
        $repeat_password = $_POST['repeatpassword'];

        // Check if old password matches the one in the database and edit the modify time
        //if (password_verify($old_password, $user['password'])) {
            if ($old_password == $user['password']) {
            
            // Check if new password and repeat password match
            if ($new_password === $repeat_password) {
                // Hash the new password
                   //$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                // Update password in the database
                $sql_update_password = "UPDATE users SET password = ? WHERE email = ?";
                $stmt_update_password = $con->prepare($sql_update_password);
                //$stmt_update_password->bind_param("si", $hashed_password, $userid);
                $stmt_update_password->bind_param("ss", $new_password, $emaillogin); // Bind parameters
                $stmt_update_password->execute();

                // Redirect to appropriate page after successful password change
                //////////echo "<script>alert('Password changed successfully.'); window.location.href='profile.php';</script>";

                // Update last modify date time
                $sql4 = "UPDATE users SET last_modify_date = NOW() WHERE email = ?";
                $stmt4 = $con->prepare($sql4);
                $stmt4->bind_param("s", $emaillogin);
                $stmt4->execute();

                // Redirect to appropriate page after successful update/insert
                if ($stmt_update_password && $stmt4) {
                    switch ($user['role']) {
                        case 'admin':
                            $redirectUrl = 'admin_home.php';
                            break;
                        case 'staff':
                            $redirectUrl = 'staff_home.php';
                            break;
                        case 'member':
                            $redirectUrl = 'patient_home.php';
                            break;
                        default:
                            echo "<script> alert('Your role is going wrong. Please Try Again Later :( ');</script>";
                            $redirectUrl = 'index.html';
                            break;
                    }
                    echo "<script>
                        alert('User Password update successfully.');
                        window.location.href='$redirectUrl';
                    </script>";
                    exit();
                } else {
                    "<script>
                        alert('There was an error edit the user profile.');
                        window.location.href='index.html';
                        session_destroy()
                    </script>";
                }

                exit();
            } else {
                echo "<script>alert('New password and repeat password do not match.');</script>";
            }
        } else {
            echo "<script>alert('Incorrect old password.');</script>";
        }
    }


    //Delete Account
        elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['deletePassword'])) {
            $entered_password = $_POST['deletePassword'];
            // Verify if the entered password matches the one in the database
            if ($entered_password == $user['password']) {
                $sql_delete_account = "UPDATE users SET acc_activated = 0 WHERE email = ?";
                $stmt_delete_account = $con->prepare($sql_delete_account);
                $stmt_delete_account->bind_param("s", $emaillogin);
                $stmt_delete_account->execute();

                //Update the last modify date time
                 $sql_update_last_modify = "UPDATE users SET last_modify_date = NOW() WHERE email = ?";
                 $stmt_update_last_modify = $con->prepare($sql_update_last_modify);
                 $stmt_update_last_modify->bind_param("s", $emaillogin);
                 $stmt_update_last_modify->execute();

                 header("Location: index.html");
                 session_destroy();
                 exit();
            }else{
                // Incorrect password, notify the user
                echo "<script>alert('Incorrect password. Account deletion failed.');</script>";
            }
        }
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="js/scripts.js"></script>
    <script> function goBack() { window.history.back(); }</script>
    <link href="css/styles.css" rel="stylesheet" />
    <script>
        function confirmDelete() {
            var confirmed = confirm('Are you sure you want to delete your account?');
            if (confirmed) {
                var password = prompt('Please enter your password to confirm:');
                if (password !== null && password !== '') {
                    // Set the password value in a hidden input field
                    document.getElementById('deletePassword').value = password;
                    // Submit the form
                    document.getElementById('deleteForm').submit();
                }
            }
        }
    </script>




    <title>Setting Profile</title>
</head>
<body>
    <div class="col-xl-12 col-lg-12 col-md-12"> <!--Use to difference screen width auto align -->
        <div class="p-6">
            <div class="text-center">
                <h1 class="h4 text-gray-900 mb-4">Setting your Account Security</h1>
            </div>

            <div class="row gx-4 gx-lg-5 justify-content-center mb-5">
                <div class="col-lg-6">
                    <form method="post" id="deleteForm">
                    
                        <div class="form-group row">
                            <div class="col-sm-12 mb-3 mb-sm-0">
                                <label>Email</label>
                                <input type="email" class="form-control form-control-user" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                            </div>
                        </div>

                        <br>

                        <div class="form-group row">
                            <div class="col-sm-12 mb-3 mb-sm-0">
                                <label>Old Password</label>
                                <input type="password" class="form-control form-control-user" name="oldpassword" required>
                            </div>
                        </div>

                        <br>

                        <div class="form-group row">
                            <div class="col-sm-6">
                                <label>New Password</label>
                                <input type="password" class="form-control form-control-user" name="newpassword" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*\W).{8,}" title="Must contain at least one number, one uppercase and one lowercase letter, and one special symbol. Minimum 8 characters." required>
                            </div>
                            <div class="col-sm-6">
                                <label>Repeat Password</label>
                                <input type="password" class="form-control form-control-user" name="repeatpassword" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*\W).{8,}" title="Must contain at least one number, one uppercase and one lowercase letter, and one special symbol. Minimum 8 characters." required>
                            </div>
                        </div>

                        <br>

                        <div class="form-group row">
                            <label>Two-factor authentication (2FA)</label><br>
                            <div class="col-sm-5">
                                <a id="enable-authenticator" href="./securityEnableAuthenticator" class="btn btn-primary">Set up Two-factor authentication</a><br><br>
                            </div><br><br>
                            <div class="col-sm-5">
                                <a id="reset-authenticator" href="./securityResetAuthenticator" class="btn btn-danger">Reset Two-factor authentication</a>
                            </div>
                        </div>

                        <br>
                        <br>
                        <br>
                        <div class="container px-4 px-lg-5 text-center justify-content-center">
                            <!--<input class="btn btn-secondary btn-xl" type="button" value="Delete Account" onclick="confirmDelete()">
                            <input type="hidden" id="deletePassword" name="deletePassword">-->
                            
                        </div>

                        <br>
                        <br>

                        <div class="container px-4 px-lg-5 text-center justify-content-center">
                            <!--<input class="btn btn-primary btn-xl " type="submit" value="Edit Profile">&nbsp;&nbsp;&nbsp;&nbsp;-->
                            <input class="btn btn-danger btn-xl" type="submit" name="change_password" value="Change Password">&nbsp;&nbsp;&nbsp;&nbsp;
                            <a href="#" class="btn btn-light btn-xl" onclick="goBack()">&nbsp; Back &nbsp;  </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>