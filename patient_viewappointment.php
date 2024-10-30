<?php
//do connection to databse and security(Login Checked)
include("securityauth.php");
restrictAccessByRole(['member']);
include("database.php");

// Store user login email into $emaillogin
$emaillogin = $_SESSION['email'];
$user_id = $_SESSION["user_id"]; // Assuming user is logged in and you have user ID in session

// Handle appointment cancellation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["cancel_appointment"])) {
    $appointment_id = $_POST["cancel_appointment"];
    $stmt = $db->prepare("UPDATE appointment SET appointment_status = 'Cancelled' WHERE appointment_id = ?");
    $stmt->execute([$appointment_id]);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment History</title>
    <script> function goBack() { window.history.back(); }</script>
    <script>
        function confirmCancel() {
            return confirm('Are you sure you want to cancel this appointment?');
        }
    </script>

    <!-- Include Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet" />
</head>
<body>
    <div class="container">
    <br>
        <div class="d-flex justify-content-between align-items-center mt-4 mb-4">
            <h1>Appointment History</h1>
            <a href="patient_home.php" class="btn btn-primary btn-xl">Back to homepage</a>
        </div>
        <hr><br>
        <h2>Upcoming Appointments</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Appoint ID</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Package ID</th>
                    <th>Package Name</th>
                    <th>Status</th>
                    
                </tr>
            </thead>
            <tbody>
            <?php
                // Fetch and display upcoming appointments from the database
                $stmt = $db->prepare("SELECT * FROM appointment WHERE user_id = ? AND appoint_date >= CURDATE() AND appointment_status = 'Upcoming' ORDER BY appoint_date ASC");
                $stmt->execute([$user_id]);
                $upcoming_appointments = $stmt->fetchAll();

                if (count($upcoming_appointments) > 0) {
                    foreach ($upcoming_appointments as $appointment) {
                        // Fetch the package for this appointment
                        $stmtpackage = $db->prepare("SELECT * FROM appoint_package WHERE package_id = ?");
                        $stmtpackage->execute([$appointment['package_id']]);
                        $package = $stmtpackage->fetch();

                        echo "<tr>";
                        echo "<td>" . $appointment['appointment_id'] . "</td>";
                        echo "<td>" . $appointment['appoint_date'] . "</td>";
                        echo "<td>" . $appointment['appoint_time'] . "</td>";
                        echo "<td>" . $appointment['package_id'] . "</td>";
                        echo "<td>" . $package['package_name'] . "</td>"; 
                        echo "<td>" . $appointment['appointment_status'] . "</td>";
                        echo "<td>";
                        echo "<form method='post' onsubmit='return confirmCancel()'>";
                        echo "<button type='submit' name='cancel_appointment' value='" . $appointment['appointment_id'] . "' class='btn btn-danger'>Cancel Booking</button>";
                        echo "</form>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>No upcoming appointment at the moment.</td></tr>";
                }
                ?>

            </tbody>
        </table>

        <h2 class="mt-4">Past Appointments</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Appointment ID</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Package ID</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            <?php
            // Fetch and display past appointments from the database
            $stmt = $db->prepare("SELECT * FROM appointment WHERE user_id = ? AND (appoint_date < CURDATE() OR appointment_status != 'Upcoming') ORDER BY appoint_date DESC");
            $stmt->execute([$user_id]);
            $past_appointments = $stmt->fetchAll();
            if (count($past_appointments) > 0) {
                foreach ($past_appointments as $appointment) {
                    echo "<tr>";
                    echo "<td>" . $appointment['appointment_id'] . "</td>";
                    echo "<td>" . $appointment['appoint_date'] . "</td>";
                    echo "<td>" . $appointment['appoint_time'] . "</td>";
                    echo "<td>" . $appointment['package_id'] . "</td>";
                    echo "<td>" . $appointment['appointment_status'] . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No data at the moment.</td></tr>";
            }
            ?>
            </tbody>
        </table>
    </div>

    <!-- Include Bootstrap JS and jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
