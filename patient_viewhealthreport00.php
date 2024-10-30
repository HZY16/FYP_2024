<?php
include("securityauth.php");

include("database.php");
restrictAccessByRole(['member']);

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
    <title>Review Health Report</title>
    
    <link href="css/styles.css" rel="stylesheet" />
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-8">
                <h1 class="mt-4 mb-4">Review Health Report</h1>
            </div>
            <div class="col-4 text-right">
                <a href="patient_home.php" class="btn btn-secondary mt-3">Back</a>
            </div>
            <br><hr><br>
        </div>
    </div>
    <div class="container mt-5">
        <h2>Recent Test Results</h2>
        <div class="row">
            <?php
            // Fetch and display test results from the database
            $sql = "SELECT * FROM testresult where user_id= $userID ORDER BY result_id DESC";
            $result = $con->query($sql);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    ?>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Result ID: <?php echo $row['result_id']; ?></h5>
                                <p class="card-text">User ID: <?php echo $row['user_id']; ?></p>
                                <p class="card-text">Test Date: <?php echo $row['test_date']; ?></p>
                                <p class="card-text">Status: <?php echo $row['status']; ?></p>
                                <a href='patient_viewhealthreport01.php?id=<?php echo $row['result_id']; ?>' class='btn btn-primary'>View Detail</a>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo "<p>No test results available</p>";
            }
            ?>
        </div>
    </div>
    

    <!-- Bootstrap JS -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
