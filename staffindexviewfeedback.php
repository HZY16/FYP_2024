<?php
include("securityauth.php");
require('database.php');

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Check if all required fields are submitted
  if (isset($_POST['full_name']) && isset($_POST['email']) && isset($_POST['phone_number']) && isset($_POST['subject']) && isset($_POST['feedback'])) {
      // Retrieve form data
      $fullName = $_POST['full_name'];
      $email = $_POST['email'];
      $phoneNumber = $_POST['phone_number'];
      $subject = $_POST['subject'];
      $feedback = $_POST['feedback'];
      $status = 'Wait for Support'; // Assuming initial status is 'Pending'

    // Prepare and execute SQL insert statement
    $insert = $db->prepare("INSERT INTO `homefeedback` (full_name, email, phone_number, subject, feedback, status, feedback_date) VALUES (?, ?, ?, ?, ?, ?, NOW())");
    $insert->execute([$fullName, $email, $phoneNumber, $subject, $feedback, $status]);
  }
    // Check if delete_feedback button is clicked
    if (isset($_POST['delete_feedback'])) {
        $feedback_id = $_POST['delete_feedback'];
        $stmt = $db->prepare("DELETE FROM homefeedback WHERE id = ?");
        $stmt->execute([$feedback_id]);
    }

    // Check if mark_successful button is clicked
    if (isset($_POST['mark_successful'])) {
        $feedback_id = $_POST['mark_successful'];
        $stmt = $db->prepare("UPDATE homefeedback SET status = 'Successful' WHERE id = ?");
        $stmt->execute([$feedback_id]);
    }
}

// Fetch feedback from database
$stmt = $db->query("SELECT * FROM `homefeedback`");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Management</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet" />
</head>
<body>
    <div class="container">
        <div class="col-xl-12 col-lg-12 col-md-12"> 
            <h1>Feedback Management</h1>
            <hr><br>
            <div class="container mt-3">
                <div class="row">
                    <div class="col-lg-3">
                        <div class="card">
                            <div class="card-header">
                                Add New Feedback
                            </div>
                            <div class="card-body">
                                <form method="post">
                                <div class="form-group">
                                    <label for="full_name">Full Name:</label>
                                    <input type="text" class="form-control" id="full_name" name="full_name" required>
                                </div>
                                <div class="form-group">
                                    <label for="email">Email:</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                                <div class="form-group">
                                    <label for="phone_number">Phone Number:</label>
                                    <input type="tel" class="form-control" id="phone_number" name="phone_number" required>
                                </div>
                                <div class="form-group">
                                    <label for="subject">Subject:</label>
                                    <input type="text" class="form-control" id="subject" name="subject" required>
                                </div>
                                <div class="form-group">
                                    <label for="feedback_text">Feedback:</label>
                                    <textarea class="form-control" id="feedback_text" name="feedback" rows="3" required></textarea>
                                </div>
                                    <br>
                                    <button type="submit" class="btn btn-primary">Add Feedback</button>
                                    <a href="staff_home.php" class="btn btn-secondary">Back</a>
                                </form>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="col-lg-9">
                        
                        <div class="card">
                            <div class="card-header">
                                Existing Feedback
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Full Name</th>
                                                <th>Email</th>
                                                <th>Phone Number</th>
                                                <th>Subject</th>
                                                <th>Feedback</th>
                                                <th>Status</th>
                                                <th>Date</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- PHP code to fetch existing feedback and display them -->
                                            <?php
                                            foreach ($rows as $row) {
                                                echo '<tr>';
                                                echo '<td>' . $row["id"] . '</td>';
                                                echo '<td>' . $row["full_name"] . '</td>';
                                                echo '<td>' . $row["email"] . '</td>';
                                                echo '<td>' . $row["phone_number"] . '</td>';
                                                echo '<td>' . $row["subject"] . '</td>';
                                                echo '<td>' . $row["feedback"] . '</td>';
                                                echo '<td>' . $row["status"] . '</td>';
                                                echo '<td>' . $row["feedback_date"] . '</td>';
                                                echo '<td>';
                                                echo "<form method='post' action=''>";
                                                echo "<button type='submit' name='delete_feedback' value='" . $row['id'] . "' onclick='return confirm(\"Are you sure you want to delete this feedback?\");' class='btn btn-danger'><i class='fas fa-trash'></i></button>&nbsp;";
                                                echo "<button type='submit' name='mark_successful' value='" . $row['id'] . "' onclick='return confirm(\"Are you sure you want to mark this feedback as successful?\");' class='btn btn-success'><i class='fas fa-check'></i></button>";
                                                echo "</form>";
                                                echo '</td>';
                                                echo '</tr>';
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

    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
