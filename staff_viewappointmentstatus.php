<?php
//do connection to database and security(Login Checked)
include("securityauth.php");
include("database.php");
restrictAccessByRole(['staff']);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['mark_successful'])) {
        $appointment_id = $_POST['mark_successful'];
        $stmt = $db->prepare("UPDATE appointment SET appointment_status = 'Successful' WHERE appointment_id = :appointment_id");
        $stmt->bindParam(':appointment_id', $appointment_id);
        $stmt->execute();
    }

    if (isset($_POST['cancel_appointment'])) {
        $appointment_id = $_POST['cancel_appointment'];
        $stmt = $db->prepare("UPDATE appointment SET appointment_status = 'Cancelled by Staff' WHERE appointment_id = :appointment_id");
        $stmt->bindParam(':appointment_id', $appointment_id);
        $stmt->execute();
    }

    if (isset($_POST['no_check_in'])) {
        $appointment_id = $_POST['no_check_in'];
        $stmt = $db->prepare("UPDATE appointment SET appointment_status = 'No Check-In' WHERE appointment_id = :appointment_id");
        $stmt->bindParam(':appointment_id', $appointment_id);
        $stmt->execute();
    }

    if (isset($_POST['reset_appointment'])) {
        $appointment_id = $_POST['reset_appointment'];
        $stmt = $db->prepare("UPDATE appointment SET appointment_status = 'Upcoming' WHERE appointment_id = :appointment_id");
        $stmt->bindParam(':appointment_id', $appointment_id);
        $stmt->execute();
    }
    
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff View</title>

    <!-- Include Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="css/styles.css" rel="stylesheet" />

</head>
<body>
    <div class="container">
        <h1 class="mt-4 mb-4">Staff View</h1>
        <a href="staff_home.php" class="btn btn-primary mb-3" style="float: right;">Back</a>
        

        <h2>Today's Appointments</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Appoint ID</th>
                <th>User ID</th>
                <th>Name</th>
                <th>Date</th>
                <th>Time</th>
                <th>Package ID</th>
                <th>Package Name</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
        <?php
            // Fetch and display today's appointments from the database
            $stmt = $db->prepare("SELECT appointment.*, users.first_name, users.last_name, appoint_package.package_name FROM appointment 
                                JOIN users ON appointment.user_id = users.user_id 
                                JOIN appoint_package ON appointment.package_id = appoint_package.package_id 
                                WHERE appoint_date = CURDATE() ORDER BY appoint_time ASC");
            $stmt->execute();
            $todays_appointments = $stmt->fetchAll();

            if (count($todays_appointments) > 0) {
                foreach ($todays_appointments as $appointment) {
                    echo "<tr>";
                    echo "<td>" . $appointment['appointment_id'] . "</td>";
                    echo "<td>" . $appointment['user_id'] . "</td>";
                    echo "<td>" . $appointment['first_name'] . " " . $appointment['last_name'] . "</td>";
                    echo "<td>" . $appointment['appoint_date'] . "</td>";
                    echo "<td>" . $appointment['appoint_time'] . "</td>";
                    echo "<td>" . $appointment['package_id'] . "</td>";
                    echo "<td>" . $appointment['package_name'] . "</td>";
                    echo "<td>" . $appointment['appointment_status'] . "</td>";
                    echo "<td>";
                    echo "<form method='post' action='' onsubmit='return confirm(\"Are you sure you want to mark this appointment as successful?\");'>";
                    echo "<button type='submit' name='mark_successful' value='" . $appointment['appointment_id'] . "' class='btn btn-success'><i class='fas fa-check'></i></button>";
                    echo "</form>";
                    echo "<form method='post' action='' onsubmit='return confirm(\"Are you sure you want to cancel this appointment?\");'>";
                    echo "<button type='submit' name='cancel_appointment' value='" . $appointment['appointment_id'] . "' class='btn btn-danger'><i class='fas fa-times'></i></button>";
                    echo "</form>";
                    echo "<form method='post' action='' onsubmit='return confirm(\"Are you sure you want to mark this appointment no checkin?\");'>";
                    echo "<button type='submit' name='no_check_in' value='" . $appointment['appointment_id'] . "' class='btn btn-info'><i class='fas fa-question'></i></button>";
                    echo "</form>";
                    echo "<form method='post' action='' onsubmit='return confirm(\"Are you sure you want to reset this appointment?\");'>";
                    echo "<button type='submit' name='reset_appointment' value='" . $appointment['appointment_id'] . "' class='btn btn-warning'><i class='fas fa-sync-alt'></i></button>";
                    echo "</form>";
                    echo "</td>";

                    //echo "<td>";
                    //echo "<form method='get' action='edit_appointment.php'>";
                    //echo "<button type='submit' name='edit_appointment' value='" . $appointment['appointment_id'] . "' class='btn btn-danger'>Edit</button>";
                    //echo "</form>";
                    //echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='8'>No appointments for today.</td></tr>";
            }
        ?>



            </tbody>
        </table>

        <h2 class="mt-4">Upcoming Appointments</h2>
<table class="table table-striped">
    <thead>
        <tr>
            <th>Appoint ID</th>
            <th>User ID</th>
            <th>Name</th>
            <th>Phone</th>
            <th>Date</th>
            <th>Time</th>
            <th>Package ID</th>
            <th>Package Name</th>
            <th>Status</th>
            <th>Action</th> <!-- New column for actions -->
        </tr>
    </thead>
    <tbody>
        <?php
        // Fetch and display upcoming appointments from the database
        $stmt = $db->prepare("SELECT * FROM appointment WHERE appoint_date > CURDATE() AND appointment_status != 'Cancelled' ORDER BY appoint_date ASC, appoint_time ASC");
        $stmt->execute();
        $upcoming_appointments = $stmt->fetchAll();

        if (count($upcoming_appointments) > 0) {
            foreach ($upcoming_appointments as $appointment) {
                // Fetch the user for this appointment
                $stmtuser = $db->prepare("SELECT * FROM users WHERE user_id = ?");
                $stmtuser->execute([$appointment['user_id']]);
                $user = $stmtuser->fetch();

                // Fetch the package for this appointment
                $stmtpackage = $db->prepare("SELECT * FROM appoint_package WHERE package_id = ?");
                $stmtpackage->execute([$appointment['package_id']]);
                $package = $stmtpackage->fetch();

                echo "<tr>";
                echo "<td>" . $appointment['appointment_id'] . "</td>";
                echo "<td>" . $appointment['user_id'] . "</td>";
                echo "<td>" . $user['first_name'] . " " . $user['last_name'] . "</td>";
                echo "<td>" . $user['phone_number'] . "</td>";
                echo "<td>" . $appointment['appoint_date'] . "</td>";
                echo "<td>" . $appointment['appoint_time'] . "</td>";
                echo "<td>" . $appointment['package_id'] . "</td>";
                echo "<td>" . $package['package_name'] . "</td>";
                echo "<td>" . $appointment['appointment_status'] . "</td>";

                // Action buttons for each appointment
                echo "<td>";
                echo "<form method='post' action='' onsubmit='return confirm(\"Are you sure you want to mark this appointment as successful?\");'>";
                    echo "<button type='submit' name='mark_successful' value='" . $appointment['appointment_id'] . "' class='btn btn-success'><i class='fas fa-check'></i></button>";
                    echo "</form>";
                    echo "<form method='post' action='' onsubmit='return confirm(\"Are you sure you want to cancel this appointment?\");'>";
                    echo "<button type='submit' name='cancel_appointment' value='" . $appointment['appointment_id'] . "' class='btn btn-danger'><i class='fas fa-times'></i></button>";
                    echo "</form>";
                    echo "<form method='post' action='' onsubmit='return confirm(\"Are you sure you want to mark this appointment no checkin?\");'>";
                    echo "<button type='submit' name='no_check_in' value='" . $appointment['appointment_id'] . "' class='btn btn-info'><i class='fas fa-question'></i></button>";
                    echo "</form>";
                    echo "<form method='post' action='' onsubmit='return confirm(\"Are you sure you want to reset this appointment?\");'>";
                    echo "<button type='submit' name='reset_appointment' value='" . $appointment['appointment_id'] . "' class='btn btn-warning'><i class='fas fa-sync-alt'></i></button>";
                    echo "</form>";
                echo "</td>";
                
                /*echo "<td>";
                echo "<form method='get' action='edit_appointment.php'>";
                echo "<button type='submit' name='edit_appointment' value='" . $appointment['appointment_id'] . "' class='btn btn-primary'>Edit</button>";
                echo "</form>";
                echo "</td>";*/

                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='9'>No upcoming appointments at the moment.</td></tr>";
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
