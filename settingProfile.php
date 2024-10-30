<?php
    //do connection to databse and security(Login Checked)
        include("securityauth.php");

        include("database.php");
        //include("databasegetdata.php");


    // Store user login email into $emaillogin
        $emaillogin = $_SESSION['email'];

    
    // Read data from database
        // Define the fields to be selected and Prepare the SQL query
        $fields = ['user_id','first_name', 'last_name', 'email', 'phone_number', 'dob', 'gender', 'password', 'role'];
        $sql = "SELECT " . implode(', ', $fields) . " FROM users WHERE email = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("s", $emaillogin);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        $userid = $user['user_id'];
        //echo " /This line From setting Profile Your User ID is: " . $userid;
        $userrole = $user['role'];
        //echo "Your Role is: " . $userrole;


        $fields1 = ['address_id','user_id', 'address', 'state', 'postcode', 'city'];
        $sql1 = "SELECT " . implode(', ', $fields1) . " FROM usersaddress WHERE user_id = ?";
        $stmt1 = $con->prepare($sql1);
        $stmt1->bind_param("s", $userid);
        $stmt1->execute();
        $result1 = $stmt1->get_result();
        $user1 = $result1->fetch_assoc();

        // Get states
        $sql_states = "SELECT state_code, state_name FROM state";
        $result_states = mysqli_query($con, $sql_states);

        // Get postcode
        $sql_postcode = "SELECT distinct postcode FROM postcode ";
        $result_postcode = mysqli_query($con, $sql_postcode);

        // Get city
        $sql_city = "SELECT distinct post_office FROM postcode ";
        $result_city = mysqli_query($con, $sql_city);

    //Determine the role and give rbac to edit profile 
        // Check user role and permissions
        switch ($user['role']) {
            case 'admin':
                // Admin can edit all information
                $canEditAll = true;
                $canEditPhoneAndAddress = true;
                break;
            case 'staff':
                // Staff can edit all information except role
                $canEditAll = false;
                $canEditPhoneAndAddress = true;
                break;
            case 'member':
                // Member can only edit phone number and address
                $canEditAll = false;
                $canEditPhoneAndAddress = true;
                break;
            default:
                // Handle other roles or errors
                break;
        }
        
    //Update or Insert data to databses

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get the new values from the form
        $new_first_name = strtoupper($_POST['firstname']);
        $new_last_name = strtoupper($_POST['lastname']);
        $new_phone_number = $_POST['phone_number'];
        $new_gender = $_POST['gender']; 
        $new_dob = $_POST['dob']; 
        $new_address = $_POST['address'];
        $new_state = $_POST['state'];
        $new_postcode = $_POST['postcode'];
        $new_city = $_POST['city'];
    
        // Update users table
        $sql2 = "UPDATE users SET phone_number = ? WHERE email = ?";
        $stmt2 = $con->prepare($sql2);
        $stmt2->bind_param("ss", $new_phone_number, $emaillogin);
        $stmt2->execute();

        $sql2 = "UPDATE users SET first_name = ?, last_name = ?, gender = ?,dob = ?, phone_number = ? WHERE user_id = ?";
        $stmt2 = $db->prepare($sql2);
        $stmt2->execute([$new_first_name, $new_last_name, $new_gender, $new_dob, $new_phone_number, $userid]);




        // Check if the user's address exists in the database
        $sql_check_address = "SELECT * FROM usersaddress WHERE user_id = ?";
        $stmt_check_address = $con->prepare($sql_check_address);
        $stmt_check_address->bind_param("i", $userid);
        $stmt_check_address->execute();
        $result_check_address = $stmt_check_address->get_result();

        // If the address does not exist, perform an insert operation
        if ($result_check_address->num_rows == 0) {
            $sql_insert_address = "INSERT INTO usersaddress (user_id, address, state, postcode, city) VALUES (?, ?, ?, ?, ?)";
            $stmt_insert_address = $con->prepare($sql_insert_address);
            $stmt_insert_address->bind_param("issss", $userid, $new_address, $new_state, $new_postcode, $new_city);
            $stmt_insert_address->execute();
        } else {
            // Update usersaddress table
            $sql3 = "UPDATE usersaddress SET address = ?, state = ?, postcode = ?, city = ? WHERE user_id = ?";
            $stmt3 = $con->prepare($sql3);
            $stmt3->bind_param("sssss", $new_address, $new_state, $new_postcode, $new_city, $userid);
            $stmt3->execute();
        }

        // Update last modify date time
        $sql4 = "UPDATE users SET last_modify_date = NOW() WHERE email = ?";
        $stmt4 = $con->prepare($sql4);
        $stmt4->bind_param("s", $emaillogin);
        $stmt4->execute();

        // Redirect to appropriate page after successful update/insert
        if ($stmt2 && ($stmt3 || $stmt_insert_address) && $stmt4) {
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
                alert('User update successfully.');
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

    <title>Setting Profile</title>
</head>
<body>
    <div class="col-xl-12 col-lg-12 col-md-12"> <!--Use to difference screen width auto align -->
        <div class="p-6">
            <div class="text-center">
                <h1 class="h4 text-gray-900 mb-4">Setting your profile</h1>
            </div>

            <div class="row gx-4 gx-lg-5 justify-content-center mb-5">
                <div class="col-lg-6">
                    <form id="profileForm" method="post">
                        <div class="form-group row">
                            <div class="col-sm-6 mb-3 mb-sm-0">
                                <label>First Name</label>
                                <input type="text" class="form-control form-control-user" name="firstname" value="<?php echo htmlspecialchars($user['first_name']); ?>" >
                                
                            </div>
                            <div class="col-sm-6">
                                <label>Last Name</label>
                                <input type="text" class="form-control form-control-user" name="lastname" value="<?php echo htmlspecialchars($user['last_name']); ?>">
                                
                            </div>
                        </div>

                        <br>

                        <div class="form-group row">
                            
                            <div class="col-sm-4 mb-3 mb-sm-0">
                                <label>Gender</label>
                                <select name="gender" class="form-select" required>
                                    <option value="">Select Gender</option>
                                    <option value="MALE" <?php echo ($user['gender'] == 'MALE') ? 'selected' : ''; ?>>Male</option>
                                    <option value="FEMALE" <?php echo ($user['gender'] == 'FEMALE') ? 'selected' : ''; ?>>Female</option>
                                </select>
                            </div>


                            <div class="col-sm-4">
                                <label>Date of Birth</label>
                                <input type="date" class="form-control form-control-user" name="dob" value="<?php echo htmlspecialchars($user['dob']); ?>">
                            </div>
                            <div class="col-sm-4">
                                <label>Phone Number</label>
                                <input type="text" class="form-control form-control-user" name="phone_number" value="<?php echo htmlspecialchars($user['phone_number']); ?>">
                            </div>
                        </div>
                
                        <br>

                        <div class="form-group row">
                            <div class="col-sm-12 mb-3 mb-sm-0">
                                <label>Address</label>
                                <?php
                                $addressValue = isset($user1['address']) ? htmlspecialchars($user1['address']) : '';
                                ?>
                                <input type="text" class="form-control form-control-user" name="address" value="<?php echo $addressValue; ?>" required>
                            </div>
                        </div>

                        <br>

                        <div class="form-group row">
                            <div class="col-sm-5">
                                <label>State</label>
                                <select name="state" id="state" class="form-select" required>
                                <option value="" >Select State</option>
                                <?php
                                if ($result_states->num_rows > 0) {
                                    while ($row_states = mysqli_fetch_assoc($result_states)) {
                                        $selected = ($row_states['state_code'] == $user1['state']) ? 'selected="selected"' : '';
                                        echo '<option value="' . $row_states['state_code'] . '" ' . $selected . '>' . $row_states['state_name'] . '</option>';
                                    }
                                }
                                ?>
                            </select>
                            </div>
                            
                            <div class="col-sm-3">
                                <label>Postcode</label>
                                <select name="postcode" id="postcode" class="form-select" required>
                                    <option value="">Select Postcode</option>
                                    <?php
                                    if ($result_postcode->num_rows > 0) {
                                        while ($row_postcode = mysqli_fetch_assoc($result_postcode)) {
                                            $selected = ($row_postcode['postcode'] == $user1['postcode']) ? 'selected="selected"' : '';
                                            echo '<option value="' . $row_postcode['postcode'] . '" ' . $selected . '>' . $row_postcode['postcode'] . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-sm-4">
                                <label>City</label>
                                <select name="city" id="city" class="form-select" required>
                                    <option value="">Select City</option>
                                    <?php
                                    if ($result_city->num_rows > 0) {
                                        while ($row_city = mysqli_fetch_assoc($result_city)) {
                                            $selected = ($row_city['post_office'] == $user1['city']) ? 'selected="selected"' : '';
                                            echo '<option value="' . $row_city['post_office'] . '" ' . $selected . '>' . $row_city['post_office'] . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <br>
                        <br>
                        <br>
                        <br>

                        <div class="container px-4 px-lg-5 text-center justify-content-center">
                            <input class="btn btn-primary btn-xl " type="submit" value="Edit Profile">&nbsp;&nbsp;&nbsp;&nbsp;
                            <a href="#" class="btn btn-light btn-xl" onclick="goBack()">Back</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>