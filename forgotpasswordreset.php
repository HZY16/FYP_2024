<?php
    session_start(); // Start the session

    require('database.php');

    if (isset($_GET['email']) && isset($_GET['key'])) {
        $email = $_GET['email'];
        $temp_key = $_GET['key'];
    
        // Check if the provided email and key exist in the tempkey table
        $sql_check_temp_key = "SELECT * FROM tempkey WHERE email = ? AND tkey = ?";
        $stmt_check_temp_key = $db->prepare($sql_check_temp_key);
        $stmt_check_temp_key->execute([$email, $temp_key]);
        $result = $stmt_check_temp_key->fetch();
    
        if ($result) {
            // Temporary key and email match, allow the user to reset their password
            if (isset($_POST['new_password']) && isset($_POST['confirm_password'])) {
                $new_password = $_POST['new_password'];
                $confirm_password = $_POST['confirm_password'];
    
                if ($new_password === $confirm_password) {
                    // Passwords match, update the password in the database
                    //$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    $sql_update_password = "UPDATE users SET password = ?, acc_activated = 1, last_modify_date = NOW() WHERE email = ?";
                    $stmt_update_password = $db->prepare($sql_update_password);
                    $stmt_update_password->execute([$new_password, $email]);
                    //$stmt_update_password->execute([$hashed_password, $email]);
    
                    // Delete the temporary key from the database
                    $sql_delete_temp_key = "DELETE FROM tempkey WHERE email = ?";
                    $stmt_delete_temp_key = $db->prepare($sql_delete_temp_key);
                    $stmt_delete_temp_key->execute([$email]);
    
                    // Inform the user that the password has been updated
                    session_destroy();
                    echo "<script>
                        alert('Password updated successfully.');
                        window.location.href='index.html';
                    </script>";
                    
                } else {
                    // Passwords don't match, inform the user
                    session_destroy();
                    echo "<script>
                    alert('Passwords do not match. Please try again.');
                    </script>";
                }
            }
        } else {
            // Temporary key and email don't match, display an error message or redirect to an error page
            echo "<script>
            alert('Invalid reset link. Please try again.');
            window.location.href='index.html';
            </script>";
        }
    } else {
        // Email or key parameters are missing in the URL, display an error message
        echo "<script>
        alert('Invalid reset link. Please try again.');
        window.location.href='index.html';
        </script>";
    }
?>


<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Reset Password</title>
        <link href="css/styles.css" rel="stylesheet" />
    </head>

    <body class="bg-gradient-primary">
        <div class="container">
            <div class="col-xl-12 col-lg-12 col-md-12"> <!--Use to difference screen width auto align -->
                <div class="p-6">

                    <div class="text-center">
                        <h1 class="h4 text-gray-900 mb-4">Reset Password</h1>
                    </div>
                    
                    <div class="row gx-4 gx-lg-5 justify-content-center mb-5">
                        <div class="col-lg-6">
                            <form method="post">                   
                                <div class="form-group">
                                    <label>New Password</label>
                                    <input type="password" class="form-control form-control-user"  name="new_password" id="new_password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*\W).{8,}" title="Must contain at least one number, one uppercase and one lowercase letter, and one special symbol. Minimum 8 characters." required>
                                </div>

                                <br>
                                <div class="form-group">
                                    <label>Confirm Password</label>
                                    <input type="password" class="form-control form-control-user"  name="confirm_password" id="confirm_password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*\W).{8,}" title="Must contain at least one number, one uppercase and one lowercase letter, and one special symbol. Minimum 8 characters." required>
                                </div>

                                <br>

                                <!-- Button -->
                                <br>
                                <div class="d-grid">
                                    <input class="btn btn-primary btn-xl " type="submit" value="Reset Password">
                                </div>
                            
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>