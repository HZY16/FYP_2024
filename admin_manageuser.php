<?php
    include("securityauth.php");
    restrictAccessByRole(['admin']);
    include("database.php");
    include("databasegetdata.php");

    // Check if the delete_id parameter is set in the URL
    if (isset($_GET['delete_id'])) {
        $deleteID = $_GET['delete_id'];
        $table = $_GET['table']; // Get the table parameter from the URL


        // Retrieve the ID of the currently logged-in user from $userData
        $loggedInUserID = $_SESSION['user_id'];

        // Check if the user is trying to delete themselves
        if ($deleteID == $loggedInUserID && !isset($_GET['ignore_check'])) {
            // Prevent deletion of the currently logged-in user and redirect with an error message
            echo "<script>
                alert('You cannot delete yourself.');
                window.location.href='admin_manageuser.php';
            </script>";
            exit();
        }

        // Check if the table is "users" and the user being deleted is an admin
        if ($table === 'users') {
            $checkAdmin = $pdo->prepare("SELECT COUNT(*) AS admin_count FROM users WHERE role = 'admin'");
            $checkAdmin->execute();
            $adminCount = $checkAdmin->fetchColumn();

            if ($adminCount <= 1) {
                // If there is only one admin user, prevent deletion and redirect with error message
                echo "<script>
                    alert('You cannot delete the only admin user.');
                    window.location.href='admin_manageuser.php';
                </script>";
                exit();
            }
        }
        
        // Proceed with deletion if there is more than one admin user or if the table is not "users"
        switch ($table) {
            case 'users':
                $sql = "DELETE FROM users WHERE user_id = ?";
                break;
            case 'usersstaffkey':
                $sql = "DELETE FROM usersstaffkey WHERE userstaff_id = ?";
                break;
            case 'usersactivate':
                $sql = "DELETE FROM usersactivate WHERE usersactivate_id = ?";
                break;
            default:
                // Handle unknown table or provide an error message
                break;
        }
        
        if (isset($sql)) {
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$deleteID]);
            header("Location: admin_manageuser.php");
            exit(); // Add exit() to stop the script execution after redirection
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    
    <!-- Include Bootstrap CSS -->
    <link href="css/styles.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <!-- Use for navigation can change page -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
    $(document).ready(function() {
        $('.nav-link').click(function() {
            var tab = $(this).data('tab');
            $('.tabcontent').hide();
            $('#' + tab).show();
        });
    });
    </script>

    <!--Sorting Table-->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const getCellValue = (tr, idx) => tr.children[idx].innerText || tr.children[idx].textContent;

            const comparer = (idx, asc) => (a, b) => ((v1, v2) =>
                v1 !== '' && v2 !== '' && !isNaN(v1) && !isNaN(v2) ? v1 - v2 : v1.toString().localeCompare(v2)
            )(getCellValue(asc ? a : b, idx), getCellValue(asc ? b : a, idx));

            // Add event listeners to all th elements within active tab contents
            document.querySelectorAll('.tabcontent table th').forEach(th => th.addEventListener('click', () => {
                const table = th.closest('.tabcontent').querySelector('table'); // Find the table within the closest parent tab content
                const tbody = table.querySelector('tbody');
                const hdrIndex = Array.prototype.indexOf.call(th.parentNode.children, th);
                const sortDirection = th.dataset.sortDirection === 'asc' ? 'desc' : 'asc';

                Array.from(tbody.querySelectorAll('tr'))
                    .sort(comparer(hdrIndex, sortDirection === 'asc'))
                    .forEach(tr => tbody.appendChild(tr));

                // Reset sort direction for all th elements within the active tab's table
                table.querySelectorAll('th').forEach(th => th.dataset.sortDirection = null);
                // Set sort direction for the current th element
                th.dataset.sortDirection = sortDirection;
            }));
        });
    </script>

    <!--Use for search function -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Get search inputs and buttons for each table
            var searchInput_allusers = document.getElementById("userSearch");
            var searchInput_stafftoken = document.getElementById("usersstaffkeySearch");
            var searchInput_activatedtoken = document.getElementById("usersactivateSearch");
            
            var searchButton_allusers = document.getElementById("searchButton_allusers");
            var searchButton_stafftoken = document.getElementById("searchButton_stafftoken");
            var searchButton_activatedtoken = document.getElementById("searchButton_activatedtoken");
            
            var clearButton_allusers = document.getElementById("clearButton_allusers");
            var clearButton_stafftoken = document.getElementById("clearButton_stafftoken");
            var clearButton_activatedtoken = document.getElementById("clearButton_activatedtoken");

            // Add event listeners for each search button for allusers table
            searchButton_allusers.addEventListener("click", function() {
                searchTable(searchInput_allusers, "#allusers");
            });
            
            // Add event listeners for each search button for stafftoken table
            searchButton_stafftoken.addEventListener("click", function() {
                searchTable(searchInput_stafftoken, "#stafftoken");
            });
            
            // Add event listeners for each search button for activatedtoken table
            searchButton_activatedtoken.addEventListener("click", function() {
                searchTable(searchInput_activatedtoken, "#activatedtoken");
            });

            // Add event listeners for each clear button for allusers table
            clearButton_allusers.addEventListener("click", function() {
                clearSearch(searchInput_allusers, "#allusers");
            });
            
            // Add event listeners for each clear button for stafftoken table
            clearButton_stafftoken.addEventListener("click", function() {
                clearSearch(searchInput_stafftoken, "#stafftoken");
            });
            
            // Add event listeners for each clear button for activatedtoken table
            clearButton_activatedtoken.addEventListener("click", function() {
                clearSearch(searchInput_activatedtoken, "#activatedtoken");
            });

            // Add event listeners for Enter key press on search inputs for allusers table
            searchInput_allusers.addEventListener("keypress", function(event) {
                if (event.key === "Enter") {
                    searchButton_allusers.click();
                }
            });
            
            // Add event listeners for Enter key press on search inputs for stafftoken table
            searchInput_stafftoken.addEventListener("keypress", function(event) {
                if (event.key === "Enter") {
                    searchButton_stafftoken.click();
                }
            });
            
            // Add event listeners for Enter key press on search inputs for activatedtoken table
            searchInput_activatedtoken.addEventListener("keypress", function(event) {
                if (event.key === "Enter") {
                    searchButton_activatedtoken.click();
                }
            });

            // Function to search table based on input and table selector
            function searchTable(searchInput, tableSelector) {
                var searchTerm = searchInput.value.trim().toLowerCase();
                var rows = document.querySelectorAll(tableSelector + " tbody tr");

                rows.forEach(function(row) {
                    var cells = row.querySelectorAll("td");
                    var found = false;

                    cells.forEach(function(cell) {
                        if (cell.textContent.trim().toLowerCase().indexOf(searchTerm) > -1) {
                            found = true;
                        }
                    });

                    row.style.display = found ? "" : "none";
                });
            }

            // Function to clear search input and display all rows based on input and table selector
            function clearSearch(searchInput, tableSelector) {
                searchInput.value = "";
                var rows = document.querySelectorAll(tableSelector + " tbody tr");
                rows.forEach(function(row) {
                    row.style.display = "";
                });
            }
        });
    </script>






</head>
<body>
    <div class="container mt-4">
    <h1 class="mb-4">User Management</h1>
    <div class="d-flex justify-content-end">
        <a href="admin_home.php" class="btn btn-secondary mt-3">Back to Homepage</a>
    </div>
        <!-- Navigation Tabs -->
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <!--<a class="nav-link active" href="#">All Users</a>-->
                <a class="nav-link" href="#allusers" data-tab="allusers">All Users</a>
            </li>
            <li class="nav-item">

                <a class="nav-link" href="#stafftoken" data-tab="stafftoken">Staff Token</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#activatedtoken" data-tab="activatedtoken">User Activated </a>
            </li>
        </ul>

        <!-- User Table -->
        <div id="allusers" class="tabcontent">
           
             <!-- Search Bar -->
             <br>
             <div class="form-group row">
                <div class="col-sm-7 mb-3 mb-sm-0">
                    <input type="text" class="form-control" id="userSearch" placeholder="Search">
                </div>
                <div class="col-sm-5">
                    <button class="btn btn-primary" id="searchButton_allusers">Search</button> &nbsp;
                    <button class="btn btn-secondary" id="clearButton_allusers">Clear</button> &nbsp;&nbsp;
                    <td><a href="admin_mauser_delete.php?table=users" onclick="return confirm('Are you sure you want to delete No Activated Users?')"><button class="btn btn-danger mr-2">Delete</button></a></td>
                    <!--<button class="btn btn-success">Add Users</button>-->
                </div>
             </div>
            

            <table class="table mt-4 table-responsive-lg table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Activated</th>
                        <th>Register Date</th>
                        <th>Last Modify Date</th>
                        <th>Reset</th>
                        <th>Edit</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $sql = "SELECT * FROM users";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute();
                        $results = $stmt->fetchAll();
                        foreach ($results as $row) {
                            echo '<tr><td>' . $row['user_id'] . 
                                '</td><td>' . $row['first_name'].
                                '</td><td>' . $row['last_name'].
                                '</td><td>' . $row['email'].
                                '</td><td>' . $row['role'].
                                '</td><td>' . $row['acc_activated'].
                                '</td><td>' . $row['register_date'].
                                '</td><td>' . $row['last_modify_date']. '</td>';

                            echo '<td><a href="admin_mauser_reset.php?id=' . $row['user_id'] . '" onclick="return confirm(\'Are you sure you want to reset user with ID: ' . $row['user_id'] . '\nand Name: ' . $row['first_name'] .  $row['last_name'] . '?\')">Reset</a></td>';

                            //edit function                            
                            echo '<td><a href="admin_mauser_edit.php?id=' . $row['user_id'] . '">Edit</a></td>';

                            //delete function
                            /*if (isset($_GET['delete_id'])) {
                                $userId = $_GET['delete_id'];
                                $sql = "DELETE FROM users WHERE user_id = ?";
                                $stmt = $pdo->prepare($sql);
                                $stmt->execute([$userId]);
                                header("Location: admin_manageuser.php");
                                exit();
                            }*/
                            //echo '<td><a href="?delete_id=' . $row['user_id'] . '" onclick="return confirm(\'Are you sure you want to delete user with ID: ' . $row['user_id'] . '\nand Name: ' . $row['first_name'] . '?\')">Delete</a></td></tr>';
                            //echo '<td><a href="?delete_id=' . $row['user_id'] . '&table=users" onclick="return confirm(\'Are you sure you want to delete user with ID: ' . $row['user_id'] . '\nand Name: ' . $row['first_name'] .  $row['last_name'] . '?\')">Delete</a></td>';
                            echo '<td><a href="?delete_id=' . $row['user_id'] . '&table=users#allusers" onclick="return confirm(\'Are you sure you want to delete user with ID: ' . $row['user_id'] . '\nand Name: ' . $row['first_name'] .  $row['last_name'] . '?\')">Delete</a></td>';

                        }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Staff Token Table -->
        <div id="stafftoken" class="tabcontent" style="display: none;">

            <!-- Buttons -->
             <!-- Search Bar -->
             <br>
             <div class="form-group row">
                <div class="col-sm-6 mb-3 mb-sm-0">
                <input type="text" class="form-control" id="usersstaffkeySearch" placeholder="Search">
                </div>
                <div class="col-sm-6">
                    <button class="btn btn-primary" id="searchButton_stafftoken">Search</button> &nbsp;
                    <button class="btn btn-secondary" id="clearButton_stafftoken">Clear</button> &nbsp;&nbsp;
                    <td><a href="admin_mauser_delete.php?table=usersstaffkey" onclick="return confirm('Are you sure you want to delete *Used* Tokens?')"><button class="btn btn-danger">&#128465;USED</button></a></td> &nbsp;&nbsp;
                    <td><a href="admin_mauser_deleteUsed.php?table=usersstaffkey" onclick="return confirm('Are you sure you want to delete *No Used* Tokens?')"><button class="btn btn-danger">&#128465;No USED</button></a></td> &nbsp;&nbsp;
                    
                    <form action="admin_mauser_generatestaffkey.php" method="get" style="display: inline-block;">
                        <input class="btn btn-primary btn-xl" type="submit" value="Generate Key">
                    </form>
                </div>
             </div>
            


            <table class="table mt-4 table-responsive-lg table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Staff Key</th>
                        <th>Used (0/1)</th>
                        <th>Used Date</th>
                        <th>Generate Date</th>
                        <th>User ID</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $sql = "SELECT * FROM usersstaffkey";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute();
                        $results = $stmt->fetchAll();
                        foreach ($results as $row) {
                            echo '<tr><td>' . $row['userstaff_id'] . 
                                '</td><td>' . $row['staffkey'].
                                '</td><td>' . $row['used'].
                                '</td><td>' . $row['used_date'].
                                '</td><td>' . $row['generate_date'].
                                '</td><td>' . $row['user_id'].'</td>';

                            //delete function
                            /*if (isset($_GET['delete_id'])) {
                                $userId = $_GET['delete_id'];
                                $sql = "DELETE FROM usersstaffkey WHERE userstaff_id = ?";
                                $stmt = $pdo->prepare($sql);
                                $stmt->execute([$userId]);
                                header("Location: admin_manageuser.php");
                                exit();
                            }*/
                            //echo '<td><a href="?delete_id=' . $row['userstaff_id'] . '" onclick="return confirm(\'Are you sure you want to delete Staff Token with ID: ' . $row['userstaff_id'] . '?\')">Delete</a></td></tr>';
                            //echo '<td><a href="?delete_id=' . $row['userstaff_id'] . '&table=usersstaffkey" onclick="return confirm(\'Are you sure you want to delete Staff Token with ID: ' . $row['userstaff_id'] . '?\')">Delete</a></td>';
                            echo '<td><a href="?delete_id=' . $row['userstaff_id'] . '&table=usersstaffkey&ignore_check=true#stafftoken" onclick="return confirm(\'Are you sure you want to delete Staff Token with ID: ' . $row['userstaff_id'] . '?\')">Delete</a></td>';
                        }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Activated Token Table -->
        <div id="activatedtoken" class="tabcontent" style="display: none;">

            <!-- Buttons -->
            <!-- Search Bar -->
            <br>
            <div class="form-group row">
                <div class="col-sm-7 mb-3 mb-sm-0">
                    <input type="text" class="form-control" id= "usersactivateSearch" placeholder="Search">
                </div>
                <div class="col-sm-5">
                    <button class="btn btn-primary" id="searchButton_activatedtoken">Search</button> &nbsp;
                    <button class="btn btn-secondary" id="clearButton_activatedtoken">Clear</button> &nbsp;&nbsp;
                    <td><a href="admin_mauser_delete.php?table=usersactivate" onclick="return confirm('Are you sure you want to delete *Used* VKeys?')"><button class="btn btn-danger mr-2">&#128465;USED</button></a></td>
                    <!--<button class="btn btn-success">Add users</button>-->
                    <td><a href="admin_mauser_deleteUsed.php?table=usersactivate" onclick="return confirm('Are you sure you want to delete *No Used* VKeys?')"><button class="btn btn-danger mr-2">&#128465;No USED</button></a></td>
                    
                </div>
             </div>


            <table class="table mt-4 table-responsive-lg table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Vkey</th>
                        <th>Generate Date</th>
                        <th>Used (0/1)</th>
                        <th>Used Date</th>
                        <th>Role</th>
                        <th>User ID Used</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $sql = "SELECT * FROM usersactivate";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute();
                        $results = $stmt->fetchAll();
                        foreach ($results as $row) {
                            echo '<tr><td>' . $row['usersactivate_id'] . 
                                '</td><td>' . $row['vkey'].
                                '</td><td>' . $row['generate_date'].
                                '</td><td>' . $row['used'].
                                '</td><td>' . $row['used_date'].
                                '</td><td>' . $row['roles'].
                                '</td><td>' . $row['userid'].'</td>';

                            //delete function
                            /*if (isset($_GET['delete_id'])) {
                                $userId = $_GET['delete_id'];
                                $sql = "DELETE FROM usersactivate WHERE usersactivate_id = ?";
                                $stmt = $pdo->prepare($sql);
                                $stmt->execute([$userId]);
                                header("Location: admin_manageuser.php");
                                exit();
                            }*/
                            //echo '<td><a href="?delete_id=' . $row['usersactivate_id'] . '" onclick="return confirm(\'Are you sure you want to delete Activated Token with ID: ' . $row['usersactivate_id'] . '?\')">Delete</a></td></tr>';
                            //echo '<td><a href="?delete_id=' . $row['usersactivate_id'] . '&table=usersactivate" onclick="return confirm(\'Are you sure you want to delete Activated Token with ID: ' . $row['usersactivate_id'] . '?\')">Delete</a></td>';
                            echo '<td><a href="?delete_id=' . $row['usersactivate_id'] . '&table=usersactivate&ignore_check=true#activatedtoken" onclick="return confirm(\'Are you sure you want to delete Activated Token with ID: ' . $row['usersactivate_id'] . '?\')">Delete</a></td>';
                            
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Include Bootstrap JS (optional) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>