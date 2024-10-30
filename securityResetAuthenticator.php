<?php
// Do connection to database and security (Login Checked)
include("securityauth.php");
include("database.php");

// Check if the users2fa table contains the userID and 2fa_function = 1
$stmt_check_user = $con->prepare("SELECT COUNT(*) FROM users2fa WHERE user_id = ? AND 2fa_function = 1");
$stmt_check_user->bind_param("i", $userID);
$stmt_check_user->execute();
$stmt_check_user->bind_result($user_count);
$stmt_check_user->fetch();
$stmt_check_user->close();

// If the userID exists and 2fa_function = 1, allow the user to access the page
if ($user_count > 0) {
    // Continue with the rest of your code...
} else {
    // If the userID does not exist in the table or 2fa_function is not 1
    echo "<script>
    alert('Two-factor authentication is not activated for your account.');
    window.location.href = 'settingSecurity.php';
    </script>";
    exit();
}

    // Verify the password and update 2FA status if password is correct
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $password = $_POST['password'];
        
        // Retrieve password from the database for the user
        $stmt_check_password = $con->prepare("SELECT password FROM users WHERE user_id = ?");
        $stmt_check_password->bind_param("i", $userID);
        $stmt_check_password->execute();
        $stmt_check_password->bind_result($db_password);
        $stmt_check_password->fetch();
        $stmt_check_password->close(); // Close the statement
        
        // Verify the password
        if ($password == $db_password) { 
            // Update 2FA status in the database
            $stmt_update_2fa = $con->prepare("UPDATE users2fa SET 2fa_function = 0, endisabletime = NOW() WHERE user_id = ?");
            if ($stmt_update_2fa) { // Check if prepare() succeeded
                $stmt_update_2fa->bind_param("i", $userID);
                $stmt_update_2fa->execute();
                $stmt_update_2fa->close(); // Close the statement
                
                // Display success message and redirect
                echo "<script>
                        alert('Two-factor authentication disabled successfully.');
                        window.location.href = 'securitychecklogin.php'; 
                    </script>";
                exit; // Stop further execution of the script
            } else {
                // Handle prepare() failure
                echo "Prepare failed: (" . $con->errno . ") " . $con->error;
            }
        } else {
            // Display error message if the password is incorrect
            $error_message = "Incorrect password. Please try again.";
            echo "<script>
                    alert('Incorrect password. Please try again.');
                </script>";
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
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Function to show password section when the acknowledge button is clicked
            document.getElementById("acknowledgeButton").addEventListener("click", function() {
                document.getElementById("acknowledgeSection").style.display = "none";
                document.getElementById("passwordSection").style.display = "block";
            });

            // Function to submit the password form
            document.getElementById("passwordForm").addEventListener("submit", function(event) {
                event.preventDefault(); // Prevent form submission

                // Get the password entered by the user
                var password = document.getElementById("password").value;

                // Send AJAX request to verify the password and update the database
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "verify_password.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        var response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            // Show success message and hide password section
                            document.getElementById("passwordSection").style.display = "none";
                            document.getElementById("successMessage").style.display = "block";
                        } else {
                            alert("Incorrect password. Please try again.");
                        }
                    }
                };
                xhr.send("password=" + password);
            });
        });
    </script>

   
    <link href="css/styles.css" rel="stylesheet" />
    <title>Setting Profile</title>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="text-center mb-4">
                    <h1 class="h1 text-gray-900">Reset Two-Factor Authentication (2FA)</h1>
                    <hr>
                </div>
                <ul class="list-unstyled">
                    <li class="mb-3">
                        <h3>1. What is 2FA?</h3>
                        <p>2FA adds an extra layer of security to your account by requiring a second form of verification beyond just your password. In this case, an OTP (One-Time Password) is sent to your registered email address.</p>
                        <h5>Benefits of 2FA:</h5>
                        <ul>
                            <li><strong>Enhanced Security</strong> - Significantly reduces the risk of unauthorized access to your account.</li>
                            <li><strong>Protection Against Phishing</strong> - Makes it harder for attackers to gain access even if they obtain your password.</li>
                            <li><strong>Compliance Requirements</strong> - Helps meet regulatory standards and industry best practices.</li>
                            <li><strong>User Awareness and Control</strong> - Provides notifications about suspicious login attempts.</li>
                        </ul>
                        <br>
                        <h5>Danger of turning off 2FA:</h5>
                        <ul>
                            <li><strong>Increased Risk of Unauthorized Access</strong> - If your password is compromised, attackers can easily breach your account without the additional layer of verification provided by 2FA.</li>
                            <li><strong>Higher Likelihood of Account Takeover</strong> - Cybercriminals can exploit vulnerabilities to take control of your account, potentially using it for malicious purposes.</li>
                            <li><strong>Exposure to Identity Theft</strong> - Disabling 2FA exposes you to identity theft, where attackers can steal your personal information and engage in fraudulent activities.</li>
                            <li><strong>Compromised Privacy</strong> -  Unauthorized access to your account can result in the exposure of private information, including personal messages, financial data, and sensitive documents</li>
                        </ul>
                        <br>

                        <div id="acknowledgeSection" class="alert alert-danger" role="alert">
                            <h6>Do you acknowledge the negative impact of two-factor authentication (2FA) being turned off?</h6>
                            <center><button id="acknowledgeButton" class="w-50 btn btn-warning">Yes, I acknowledge</button><br><br>
                            <a href="#" class="w-50 btn btn-secondary" onclick="goBack()">Back</a>
                        </center>
                        </div>

                    </li>
                    <br><br>
                    <div class="form-group">

                        <li>
                        

                        <div id="passwordSection" style="display: none;">
                        <!-- Display error message if password is incorrect -->
                           
                            <h3>2. Verify Your Identity</h3>
                            <p>Key in your login password and disable the Two-Factor Authentication (2FA).</p>
                            <form method="post">
                                <div class="form-floating mb-3">
                                    <input type="password" id="password" name="password" class="form-control" placeholder="Password" required/>
                                    <label class="control-label form-label">Password</label>
                                    <span class="text-danger"></span>
                                </div>
                                <?php if (isset($error_message)) : ?>
                                    <div class="alert alert-danger"><?php echo $error_message; ?></div>
                                <?php endif; ?>
                                <div class="text-center mt-4">
                                    <button type="submit" class="w-100 btn btn-lg btn-success">Yes, I want to disable 2FA!</button><br><br>
                                    <a href="#" class="w-100 btn btn-lg btn-secondary" onclick="goBack()">Back</a>
                                </div>
                            </form>
                   
                        </div>
                        
                
                </li>

                    
                </ul>
            </div>
        </div>
    </div>
    </div>
    
</body>
</html>
