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

    // If the userID exists and 2fa_function = 1, prevent the user from accessing the page
    if ($user_count > 0) {
        echo "<script>
                alert('You have already activated two-factor authentication.');
                window.location.href = 'settingSecurity.php'; 
            </script>";
        exit; // Stop further execution of the script
    }

    // Store user login email into $emaillogin
    $emaillogin = $_SESSION['email'];


    // Handle form submission for verifying OTP
    if(isset($_POST['verify_otp'])) {
        // Retrieve OTP entered by user
        $user_otp = $_POST['verification_code'];

        // Retrieve OTP from session
        $otp_from_session = $_SESSION['otp'];

        // Compare entered OTP with generated OTP
        if($user_otp == $otp_from_session) {
            // OTP is valid, save to database

            // Check if the user exists in users2fa table
            $stmt_check_user = $con->prepare("SELECT COUNT(*) FROM users2fa WHERE user_id = ?");
            $stmt_check_user->bind_param("i", $userID);
            $stmt_check_user->execute();
            $stmt_check_user->bind_result($user_count);
            $stmt_check_user->fetch();
            $stmt_check_user->close();
            
            // If user exists, update the record; otherwise, insert a new record
            if ($user_count > 0) {
                // User exists, update 2fa_function column to 1
                $update_stmt = $con->prepare("UPDATE users2fa SET 2fa_function = 1, endisabletime = NOW() WHERE user_id = ?");
                $update_stmt->bind_param("i", $userID);
                $update_stmt->execute();
                $update_stmt->close();
            } else {
                // User does not exist, insert new record into users2fa table
                $insert_stmt = $con->prepare("INSERT INTO users2fa (user_id, 2fa_function, endisabletime) VALUES (?, 1, NOW())");
                $insert_stmt->bind_param("i", $userID);
                $insert_stmt->execute();
                $insert_stmt->close();
            }

            // Display success message
            echo "<script>
                    alert('Two-factor authentication activated successfully.');  
                    window.location.href = 'securitychecklogin.php';  
                </script>";
        } else {
            // Invalid OTP, display error message
            echo "<script>
                    alert('Invalid OTP. Please try again.');
                </script>";
        }
        
        // Clear OTP from session
        unset($_SESSION['otp']);
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="js/scripts.js"></script>
    <script> function goBack() { window.history.back(); }</script>
    <!--handle the timer for the OTP button-->
    <script>
       document.addEventListener("DOMContentLoaded", function() {
            // JavaScript code to handle the timer for the OTP button
            function startTimer(duration, display) {
                var timer = localStorage.getItem("timer") || duration; // Retrieve timer value from local storage or set to default duration
                var interval = setInterval(function () {
                    var seconds = parseInt(timer % 60, 10);

                    seconds = seconds < 10 ? "0" + seconds : seconds;

                    display.textContent = "00:" + seconds; // Change the display format to include minutes

                    localStorage.setItem("timer", timer); // Store current timer value in local storage

                    if (--timer < 0) {
                        clearInterval(interval);
                        document.getElementById("otpButton").disabled = false;
                        localStorage.removeItem("timer"); // Remove timer value from local storage when timer reaches 0
                    }
                }, 1000);
            }

            var otpButton = document.getElementById("otpButton");
            var display = document.querySelector('#time');
            var storedTimer = localStorage.getItem("timer");
            if (storedTimer && parseInt(storedTimer) > 0) {
                otpButton.disabled = true;
                startTimer(parseInt(storedTimer), display);
            }

            otpButton.addEventListener("click", function () {
                var tenSeconds = 60; // Change the countdown duration to 30 seconds
                startTimer(tenSeconds, display);
                this.disabled = true;

                // Show the OTP verification section
                var section3 = document.getElementById("section3");
                section3.style.display = "block";

                // Scroll down to the OTP verification section
                section3.scrollIntoView({ behavior: "smooth", block: "start" });
            });

            // Event listener to reset timer on beforeunload ( F5)
            window.onbeforeunload = function() {
                localStorage.removeItem("timer");
            };
        });
    </script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#otpButton').click(function() {
                $.ajax({
                    url: 'securityen2faemail.php', // Replace with the path to your PHP file
                    type: 'POST',
                    success: function(response) {
                    if (response.includes('You have already activated two-factor authentication.')) {
                        alert('You have already activated two-factor authentication.');
                    } else {
                        console.log('Response:', response);
                    }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', status, error);
                        alert('Failed to send OTP. Please try again later.');
                    }

                });
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
                    <h1 class="h1 text-gray-900">Setup Two-Factor Authentication (2FA)</h1>
                    <p class="lead">Protect your account with an extra layer of security!</p>
                    <hr><br>
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
                    </li>
                    <br><br>
                    <div class="form-group">

                        <li>
                        <h3>2. Check your email is correct?</h3>
                        <p>Click the "Get OTP" button to receive a One-Time Password via email. This OTP will be used to activate two-factor authentication.</p>

                        <form action="" method="post">
                            <div class="form-floating mb-3">
                                <input name="verification_code" class="form-control" autocomplete="off" value="<?php echo $emaillogin; ?>" disabled/>
                                <label class="control-label form-label">Email</label>
                                <span class="text-danger"></span>
                            </div>
                            
                            <div class="text-center mt-4 ">
                                <button type="button" name="get_otp" id="otpButton" class="w-100 btn btn-lg btn-success">Get OTP <span id="time"></span></button>
                            
                            </div>
                        </form>
                    </li>

                    <br><br>
                    <div id="section3" style="display: none;">
                    <li>
                        <h3>3. Enter the One Time Password!</h3>
                        <p>Once you have received the One-Time Password in your email, enter it in the confirmation box below to activate two-factor authentication.</p>

                        <form id="check-code" method="post">
                            <div class="form-floating mb-3">
                                <input name="verification_code" id="verification_code" class="form-control" autocomplete="off" placeholder="Please enter the code." required/>
                                <label class="control-label form-label">Verification Code</label>
                                <span class="text-danger"></span>
                            </div>
                            <div class="text-center mt-4 ">
                                <button type="submit" name="verify_otp" id="verifyButton" class="w-100 btn btn-lg btn-primary">Verify & Activate 2FA</button>
                                <div class="text-danger" role="alert"></div>
                                <br>
                            </div>
                    </li>
                    </div>
                    <a href="#" class="w-100 btn btn-lg btn-secondary" onclick="goBack()">Back</a>
                </ul>
            </div>
        </div>
    </div>
    </div>
    
</body>
</html>
