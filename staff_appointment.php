<?php
    // Establish database connection and ensure user is logged in
    include("securityauth.php");
    include("database.php");
    restrictAccessByRole(['staff']);

    // Retrieve logged-in user's email
    $emaillogin = $_SESSION['email'];

    // Handle form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get form data
        $appoint_date = $_POST["appoint_date"];
        $appoint_time = $_POST["appoint_time"];
        $package_id = $_POST["package_id"];
        $user_id = $_POST["user_id"]; // Get the selected user ID from the form

        // Check if the selected user already has an upcoming appointment
        $check = $db->prepare("SELECT * FROM appointment WHERE user_id = ? AND appointment_status = 'Upcoming'");
        $check->execute([$user_id]);
        $existing_appointment = $check->fetch();

        if ($existing_appointment) {
            echo "<script>
                alert('The selected patient already has an upcoming booking! Please check the *Appointment History*');
                window.location.href='patient_home.php';
            </script>";
        } else {
            // Book the appointment
            $insert1 = $db->prepare("INSERT INTO appointment (user_id, appoint_date, appoint_time, package_id, appointment_status) VALUES (?, ?, ?, ?, ?)");
            $insert1->execute([$user_id, $appoint_date, $appoint_time, $package_id, "Upcoming"]);

            if($insert1){
                echo "<script>
                alert('Appointment booked successfully');
                window.location.href='staff_home.php';
                </script>";
            } else {
                echo "<script>
                    alert('Error, please call us to book!');
                    window.location.href='staff_home.php';
                </script>";
            }
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
    <title>Book Appointment</title>
</head>
<body>
    <div class="col-xl-12 col-lg-12 col-md-12"> <!-- Use to differentiate screen width auto align -->
        <div class="p-6">
            <div class="row gx-4 gx-lg-5 justify-content-center mb-5">
                <div class="col-lg-6">
                    <div class="container">
                        <h1>Book Appointment</h1>
                        <br>
                        <form method="post">
                            <div class="form-group">
                                <label for="appoint_date">Date (Monday - Friday only):</label>
                                <input type="date" class="form-control" id="appoint_date" name="appoint_date" required onchange="checkDate()">
                            </div>
                            <br>
                            <div class="form-group">
                                <label for="appoint_time">Time:</label><br>
                                <input type="radio" id="time_slot1" name="appoint_time" value="8am - 10am" required>
                                <label for="time_slot1">8am - 10am</label><br>
                                <input type="radio" id="time_slot2" name="appoint_time" value="11am - 1pm" required>
                                <label for="time_slot2">11am - 1pm</label><br>
                                <input type="radio" id="time_slot3" name="appoint_time" value="2pm - 4pm" required>
                                <label for="time_slot3">2pm - 4pm</label><br>
                            </div>
                            <br>
                            <div class="form-group">
                                <label for="package_id">Package: </label>
                                <select class="form-control" id="package_id" name="package_id" required>
                                    <option value="">Select Package</option>
                                    <?php
                                    // Fetch and display packages with visibility = 1 from the database
                                    $sql = "SELECT package_id, package_name FROM appoint_package WHERE visibility = 1";
                                    $result = $con->query($sql);
                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<option value='" . $row['package_id'] . "'>" . $row['package_name'] . "</option>";
                                        }
                                    } else {
                                        echo "<option value=''>No packages available</option>";
                                    }
                                    ?>
                                </select>
                                <a href="patient_viewpackage.php" target="_blank">View All Available Packages</a>
                            </div>
                            <br>
                            <!-- Add this code to select the patient -->
                            <div class="form-group">
                                <label for="user_id">Select Patient:</label>
                                <select class="form-control" id="user_id" name="user_id" required>
                                    <option value="">Select Patient</option>
                                    <?php
                                        // Fetch and display users from the database
                                        $sql = "SELECT * FROM users WHERE role = 'member'";
                                        $result = $con->query($sql);
                                        if ($result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) {
                                                echo "<option value='" . $row['user_id'] . "'>" . $row['first_name'] ." " . $row['last_name'] .  " " . $row['phone_number'] . "</option>";
                                            }
                                        } else {
                                            echo "<option value=''>No patients available</option>";
                                        }
                                    ?>
                                </select>
                            </div>
                            <br><br><br>
                            <center>
                                <button type="submit" class="btn btn-primary">Book Appointment</button>
                                <a href="#" class="btn btn-secondary" onclick="goBack()">Back</a>
                            </center>
                        </form>
                    </div>

                    <!-- Link Bootstrap JS and jQuery -->
                    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
                    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
                    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

                </div>
            </div>
        </div>
    </div>
</body>
</html>
