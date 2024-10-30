<?php
include("securityauth.php");
include("database.php");
restrictAccessByRole(['staff']);

if (isset($_GET['delete_id'])) {
    // Sanitize the input to prevent SQL injection
    $delete_id = mysqli_real_escape_string($con, $_GET['delete_id']);

    // Perform the deletion by executing the SQL DELETE query
    $sql = "DELETE FROM testresult WHERE result_id = '$delete_id'";
    if (mysqli_query($con, $sql)) {
        // Deletion successful, redirect the user to the same page
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    } else {
        // Deletion failed, display an error message
        echo "Error deleting test result: " . mysqli_error($con);
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select User</title>
    <link href="css/styles.css" rel="stylesheet" />

</head>
<body>
    <div class="container mt-5">
    <h1 class="mt-4 mb-4">Create New Health Report</h1>
        <div class="form-group">
            <label for="memberselected">Member: </label>
            <select class="form-control" id="memberselected" name="memberselected" required>
                <option value="">Select Member</option>
                <?php
                // Fetch and display packages with visibility = 1 from the database
                $sql = "SELECT * FROM users where role='member'AND acc_activated = 1";
                $result = $con->query($sql);
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='" . $row['user_id'] . "'>" . $row['first_name'] .' ' . $row['last_name'] .  "</option>";
                    }
                } else {
                    echo "<option value=''>No member available</option>";
                }
                ?>
            </select>
            <!-- Call JavaScript function on button click to redirect -->
            <button onclick="redirectToCreateHealthReport()" class="btn btn-primary mt-3">Next</button> 
            <!-- Back button -->
            <a href="staff_home.php" class="btn btn-secondary mt-3">Back</a>
        </div>
    </div>

    <div class="container mt-5">
        <h2>Recent Test Results</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Result ID</th>
                    <th>User ID</th>
                    <th>Test Date</th>
                    <th>Status</th>
                    <!-- Add more table headers as needed -->
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch and display test results from the database
                $sql = "SELECT * FROM testresult ORDER BY result_id DESC";
                $result = $con->query($sql);
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['result_id'] . "</td>";
                        echo "<td>" . $row['user_id'] . "</td>";
                        echo "<td>" . $row['test_date'] . "</td>";
                        echo "<td>" . $row['status'] . "</td>";
                        // Add more table cells for other columns from the testresult table
                        echo "<td>";
                        echo "<a href='staff_edithealthreport.php?id=" . $row['result_id'] . "' class='btn btn-primary'>Edit</a>";
                        echo "</td>";
                        echo "<td>";
                        echo '<td><a href="?delete_id=' . $row['result_id'] . '&table=result_id" onclick="return confirm(\'Are you sure you want to delete this Result ID : ' . $row['result_id'] . '?\')" >Delete</a></td>';
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>No test results available</td></tr>";
                }
                ?>
            </tbody>
        </table>

    </div>

    <!-- JavaScript function to redirect to create health report page -->
    <script>
        function redirectToCreateHealthReport() {
            // Get the selected user ID from the dropdown
            var userId = document.getElementById("memberselected").value; // Use "memberselected" instead of "user_id"
            // Redirect to the create health report page with the selected user ID as a query parameter
            window.location.href = "staff_createhealthreport01.php?user_id=" + userId; // Change the URL to point to createhealth01.php
        }
    </script>
</body>
</html>