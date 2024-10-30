<?php
    //do connection to databse and security(Login Checked)
        include("securityauth.php");
        include("database.php");
        //include("databasegetdata.php");
        restrictAccessByRole(['staff']);
        


    // Store user login email into $emaillogin
    $emaillogin = $_SESSION['email'];

    // Function to toggle package visibility
    function toggleVisibility($con, $package_id) {
        // Retrieve the current visibility status of the package
        $visibility_sql = "SELECT visibility FROM appoint_package WHERE package_id = $package_id";
        $visibility_result = $con->query($visibility_sql);

        if ($visibility_result->num_rows > 0) {
            $row = $visibility_result->fetch_assoc();
            $new_visibility = $row["visibility"] == 1 ? 0 : 1;

            // Update the visibility status of the package
            $update_sql = "UPDATE appoint_package SET visibility = $new_visibility WHERE package_id = $package_id";

            if ($con->query($update_sql) === TRUE) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    // Handle form submission to add new package
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Retrieve package data from the form
        $package_name = $_POST["package_name"];
        $package_desc = $_POST["package_desc"];
        $package_price = $_POST["package_price"];

        // Prepare SQL statement to insert package into the database
        $sql = "INSERT INTO appoint_package (package_name, package_desc, package_price) VALUES ('$package_name', '$package_desc', '$package_price')";

        // Execute SQL statement
        if ($con->query($sql) === TRUE) {
            // If the insertion was successful, redirect the user to the package management page
            header("Location: staff_createpackage.php");
            exit();
        } else {
            // If an error occurred, display an error message
            echo "Error: " . $sql . "<br>" . $con->error;
        }
    }

    // Handle delete package functionality
    if(isset($_GET["action"]) && $_GET["action"] == "delete" && isset($_GET["package_id"])) {
        $package_id = $_GET["package_id"];

        // Prepare SQL statement to delete the package
        $delete_sql = "DELETE FROM appoint_package WHERE package_id = $package_id";

        // Execute SQL statement
        if ($con->query($delete_sql) === TRUE) {
            // Redirect the user to refresh the package list
            header("Location: staff_createpackage.php");
            exit();
        } else {
            echo "Error deleting record: " . $con->error;
        }
    }

    // Handle toggle visibility functionality
    if(isset($_GET["action"]) && $_GET["action"] == "toggle_visibility" && isset($_GET["package_id"])) {
        $package_id = $_GET["package_id"];

        // Call toggleVisibility function to handle visibility toggling
        if(toggleVisibility($con, $package_id)) {
            // Redirect the user to refresh the package list
            header("Location: staff_createpackage.php");
            exit();
        } else {
            echo "Error toggling visibility.";
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
        // Function to toggle package visibility
        function toggleVisibility(package_id) {
        // AJAX request to toggle the visibility status in the database
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                // Reload the page to reflect changes
                window.location.reload();
            }
        };
        xhttp.open("GET", "toggle_visibility.php?package_id=" + package_id, true);
        xhttp.send();
    }
    </script>

    <script>
        // Function to confirm package deletion
        function confirmDelete(package_id) {
            if (confirm("Are you sure you want to delete this package?")) {
                // If user confirms deletion, send request to server
                var xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        // Reload the page to reflect changes
                        window.location.reload();
                    }
                };
                xhttp.open("GET", "staff_createpackage.php?action=delete&package_id=" + package_id, true);
                xhttp.send();
            }
        }
    </script>



    <link href="css/styles.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <title>Package Management</title>
</head>
<body>
    <div class=container>
        <div class="col-xl-12 col-lg-12 col-md-12"> 
            <h1>Package Management</h1>
            <hr><br>
            <div class="container mt-3">
                <div class="row">
                    <div class="col-lg-3">
                        <div class="card">
                            <div class="card-header">
                                Add New Package
                            </div>
                            <div class="card-body">
                                <form method="post">
                                    <div class="form-group">
                                        <label for="package_name">Package Name:</label>
                                        <input type="text" class="form-control" id="package_name" name="package_name" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="package_desc">Description:</label>
                                        <textarea class="form-control" id="package_desc" name="package_desc" rows="3"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="package_price">Price:</label>
                                        <input type="text" class="form-control" id="package_price" name="package_price" required>
                                    </div>
                                    <br>
                                    <button type="submit" class="btn btn-primary">Add Package</button>&nbsp;
                                    <a href="staff_home.php" class="btn btn-secondary">Back</a>
                                </form>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="col-lg-9">
                        
                        <div class="card">
                            <div class="card-header">
                                Existing Packages
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Package Name</th>
                                                <th>Description</th>
                                                <th>Price</th>
                                                <th>Action</th>
                                                <th>View</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- PHP code to fetch existing packages and display them -->
                                            <?php
                                            // Include necessary files for database connection
                                            include("database.php");

                                            // Query to fetch existing packages
                                            $sql = "SELECT * FROM appoint_package";
                                            $result = $con->query($sql);

                                            // Check if there are any packages
                                            if ($result->num_rows > 0) {
                                                // Output data of each row
                                                while($row = $result->fetch_assoc()) {
                                                    echo '<tr>';
                                                    echo '<td>' . $row["package_id"] . '</td>';
                                                    echo '<td>' . $row["package_name"] . '</td>';
                                                    echo '<td>' . $row["package_desc"] . '</td>';
                                                    echo '<td>RM' . $row["package_price"] . '</td>';
                                                    echo '<td><button class="btn btn-sm btn-danger" onclick="confirmDelete(' . $row["package_id"] . ')">Delete</button></td>';
                                                    echo '<td>';
                                                    if ($row["visibility"] == 1) {
                                                        echo '<a href="staff_createpackage.php?action=toggle_visibility&package_id=' . $row["package_id"] . '" class="btn btn-sm btn-light"><i class="far fa-eye"></i></a>';
                                                    } else {
                                                        echo '<a href="staff_createpackage.php?action=toggle_visibility&package_id=' . $row["package_id"] . '" class="btn btn-sm btn-light"><i class="far fa-eye-slash"></i></a>';
                                                    }
                                                    echo '</td>';
                                                    echo '</tr>';
                                                }
                                            } else {
                                                echo '<tr><td colspan="5">No packages found</td></tr>';
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>