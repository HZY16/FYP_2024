<?php
     include("securityauth.php");
     include("database.php");
    restrictAccessByRole(['member']);

    // Check if the result ID is provided in the query parameter
    if (isset($_GET['id'])) {
        $resultId = $_GET['id']; // Corrected variable name

        // Fetch the test result information based on the result ID
        $sql = "SELECT * FROM testresult WHERE result_id = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("i", $resultId);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if a test result with the provided ID exists
        if ($result->num_rows > 0) {
            $testResult = $result->fetch_assoc();

            // Now fetch the user information based on the user_id from the test result
            $userId = $testResult['user_id'];
            $sqlUser = "SELECT * FROM users WHERE user_id = ?";
            $stmtUser = $con->prepare($sqlUser);
            $stmtUser->bind_param("i", $userId);
            $stmtUser->execute();
            $resultUser = $stmtUser->get_result();

            // Check if the user exists
            if ($resultUser->num_rows > 0) {
                $member = $resultUser->fetch_assoc();
            } else {
                // Handle case where user does not exist
                echo "<script>
                    alert('Error: User not found!');
                    window.location.href='patient_viewhealthreport00.php';
                </script>";
                exit();
            }
        } else {
            // Handle case where test result does not exist
            echo "<script>
                alert('Error: Test result not found!');
                window.location.href='patient_viewhealthreport00.php';
            </script>";
            exit();
        }
    } else {
        // Handle case where result ID is not provided
        echo "<script>
            alert('Error: Result ID not provided!');
            window.location.href='patient_viewhealthreport00.php';
        </script>";
        exit();
    }

?>





<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <script src="js/scripts.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script> <!-- Bootstrap core JS-->

    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        /* Style for the scroll to bottom button */
        .scroll-button {
            position: fixed;
            z-index: 1000;
        }

        .scroll-to-bottom {
            bottom: 20px;
            right: 20px;
        }

        .scroll-to-top {
            top: 20px;
            right: 20px;
        }
    </style>
    <link href="css/styles.css" rel="stylesheet" />
    <title>Health Report</title>
    <style>

    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 20px;
    }

    .container {
        max-width: 800px;
        margin: 0 auto;
        background-color: #f5f5f5;
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    h1 {
        text-align: center;
        color: #333;
    }

    .patient-info,
    .test-results {
        margin-bottom: 20px;
    }

    h2 {
        color: #333;
        border-bottom: 1px solid #ccc;
        padding-bottom: 5px;
    }

    p {
        margin: 5px 0;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th, td {
        padding: 8px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    th {
        background-color: #f2f2f2;
    }
    .form-control {
    width: 100%;
    display: inline-block;
}
        
    </style>
</head>
    <body>
        <!-- Scroll to bottom button -->
        <button class="btn btn-primary scroll-button scroll-to-bottom" onclick="scrollToBottom()">
            <i class="fas fa-chevron-down"></i> <!-- Font Awesome icon for down arrow -->
        </button>

        <!-- Scroll to top button -->
        <button class="btn btn-secondary scroll-button scroll-to-top" onclick="scrollToTop()">
            <i class="fas fa-chevron-up"></i> <!-- Font Awesome icon for up arrow -->
        </button>

        <form method="post">
            <div class="container">
                <h1>Health Report</h1>
                <div class="patient-info">
                    <h2>Member Information</h2>
                    <p><strong>User ID:</strong> <?php echo $testResult['user_id']; ?></p>
                    <p><strong>Name:</strong> <?php echo $member['first_name']; ?> <?php echo $member['last_name']; ?></p>
                    <p><strong>Gender:</strong> <?php echo $member['gender']; ?></p>
                    <p><strong>Date of birth:</strong> <?php echo $member['dob']; ?></p>
                    <p><strong>Email</strong> <?php echo $member['email']; ?></p>
                    <p><strong>Phone</strong> <?php echo $member['phone_number']; ?></p>
                    <p><strong>Date of test:</strong> <?php echo $testResult['test_date']; ?></p>
                    <p><strong>Health Result record number:</strong> <?php echo $testResult['result_id']; ?></p>
                    <!--<p><strong>Clinical history:</strong> N/A - Routine check-up</p>-->
                    </table>
                </div>
            </div>

            <br>

            <div class="container">
                <div class="test-results">
                    <h2>General Measurement</h2>
                    <p>General Measurement includes part of anthropometric measurements and vital signs. It is used as a baseline for physical fitness and to access the progress of fitness and diagnostic criteria for cental obesity, which significantly increases the risk of Cardiovascular Disease, Hypertension, Diabetes Mellitus and etc.</p><br>
                    <table>
                        <tr>
                            <th>Screening Items</th>
                            <th>Result</th>
                            <th>Reference Range</th>
                        </tr>
                        <tr>
                            <td>Height (m)</td>
                            <td><input type="number" step="0.01" min="0" name="height" value="<?php echo $testResult['height']; ?>" disabled></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Body Weight (KG)</td>
                            <td><input type="number" step="0.01" min="0" name="weight" value="<?php echo $testResult['weight']; ?>" disabled></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>BMI</td>
                            <td><input type="number" step="0.01" min="0" name="bmi" value="<?php echo $testResult['BMI']; ?>" disabled></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Body Fat (%)</td>
                            <td><input type="number" step="0.01" min="0" name="bodyfat" value="<?php echo $testResult['body_fat_percentage']; ?>" disabled></td>
                            <td>≤30yrs: M:14-20 ; F:17-24 <br> >30yrs: M:17-23 ; F:20-27</td>
                        </tr>
                        <tr>
                            <td>Skeleton Muscle Mass</td>
                            <td>
                                <select class="form-control" name="skeletonmusclemass" disabled>
                                    <option value="" disabled selected>-Select an option-</option>
                                    <option value="under" <?php if ($testResult['skeleton_muscle_mass'] == 'under') echo 'selected'; ?>>Under</option>
                                    <option value="normal" <?php if ($testResult['skeleton_muscle_mass'] == 'normal') echo 'selected'; ?>>Normal</option>
                                    <option value="over" <?php if ($testResult['skeleton_muscle_mass'] == 'over') echo 'selected'; ?>>Over</option>
                                </select>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Waist/Hip Ratio</td>
                            <td><input type="number" step="0.01" min="0" name="waisthipratio" value="<?php echo $testResult['waist_hip_ratio']; ?>" disabled></td>
                            <td>M: < 0.91 ; F: < 0.88 </td>
                        </tr>
                        <tr>
                            <td>Blood Pressure (mmHg)</td>
                            <td><input type="number" step="0.01" min="0" name="bloodpressure" value="<?php echo $testResult['blood_pressure']; ?>" disabled></td>
                            <td>< 130/85</td>
                        </tr>
                        <tr>
                            <td>Pulse Rate</td>
                            <td><input type="number" step="0.01" min="0" name="pulserate" value="<?php echo $testResult['pulse_rate']; ?>" disabled></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Respiratory Rate (BPM)</td>
                            <td><input type="number" step="0.01" min="0" name="respiratoryrate" value="<?php echo $testResult['respiratory_rate']; ?>" disabled></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Chest Circumferences (cm)</td>
                            <td><input type="number" step="0.01" min="0" name="chestcircumferences" value="<?php echo $testResult['chest_circumferences']; ?>" disabled></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td >Interpretation</td>
                            <td colspan="2"><textarea name="interpretationgeneral" disabled><?php echo $testResult['interpretationgeneral']; ?></textarea></td>
                        </tr>

                    </table>
                </div>  
            </div>

            <br>

            <div class="container">
                <div class="test-results">
                    <h2>Blood Test</h2>
                    <p>Routine blood tests play an irreplaceable role in preventive health care. By checking the components of the blood are within the normal range, within a certain period. These include haemogram, blood sugar, lipids, liver and kidney functions, vitamins and hormonal levels, inflammatory and tumor markers, certain infectious diseases and etc..</p>
                    <br>
                    <table>
                        <tr>
                            <th>Test</th>
                            <th>Result</th>
                            <th>Reference Range</th>
                        </tr>
                        <tr>
                            <td><strong>Hemogram Parameter</strong></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Hemoglobin (Hb)</td>
                            <td><input type="number" step="0.01" min="0" name="hemoglobin" value="<?php echo $testResult['hemoglobin']; ?>" disabled></td>
                            <td>13.5 - 17.5 g/dL</td>
                        </tr>
                        <tr>
                            <td>Hematocrit (Hct)</td>
                            <td><input type="number" step="0.01" min="0" name="hematocrit" value="<?php echo $testResult['hematocrit']; ?>" disabled></td>
                            <td>38.3 - 48.6%</td>
                        </tr>
                        <tr>
                            <td>Red Blood Cell Count (RBC)</td>
                            <td><input type="number" step="0.01" min="0" name="rbc" value="<?php echo $testResult['red_blood_cell_count']; ?>" disabled></td>
                            <td>4.5 - 6.0 x10^6/μL</td>
                        </tr>
                        <tr>
                            <td>Mean Corpuscular Hemoglobin (MCH)</td>
                            <td><input type="number" step="0.01" min="0" name="mch" value="<?php echo $testResult['mean_corpuscular_hemoglobin']; ?>" disabled></td>
                            <td>27.0 - 33.0 pg</td>
                        </tr>
                        <tr>
                            <td>Mean Corpuscular Hemoglobin Concentration (MCHC)</td>
                            <td><input type="number" step="0.01" min="0" name="mchc" value="<?php echo $testResult['mean_corpuscular_hemoglobin_concentration']; ?>" disabled></td>
                            <td>31.5 - 35.5 g/dL</td>
                        </tr>
                        <tr>
                            <td>Red Cell Distribution Width (RDW)</td>
                            <td><input type="number" step="0.01" min="0" name="rdw" value="<?php echo $testResult['red_cell_distribution_width']; ?>" disabled></td>
                            <td>11.5 - 14.5%</td>
                        </tr>
                        <tr>
                            <td><strong>Blood Grouping</strong></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>ABO</td>
                            <td>
                                <select class="form-control" name="abo" disabled>
                                    <option value="" disabled selected>-Select an option-</option>
                                    <option value="A" <?php if ($testResult['blood_grouping_ABO'] == 'A') echo 'selected'; ?>>A</option>
                                    <option value="B" <?php if ($testResult['blood_grouping_ABO'] == 'B') echo 'selected'; ?>>B</option>
                                    <option value="AB" <?php if ($testResult['blood_grouping_ABO'] == 'AB') echo 'selected'; ?>>AB</option>
                                    <option value="O" <?php if ($testResult['blood_grouping_ABO'] == 'O') echo 'selected'; ?>>O</option>
                                </select>
                            </td>
                            <td></td>

                        </tr>
                        <tr>
                            <td>Rh(D)Factor</td>
                            <td>
                                <select class="form-control" name="rhd" disabled>
                                    <option value="" disabled selected>-Select an option-</option>
                                    <option value="Positive" <?php if ($testResult['blood_grouping_Rh'] == 'Positive') echo 'selected'; ?>>Positive</option>
                                    <option value="Negative" <?php if ($testResult['blood_grouping_Rh'] == 'Negative') echo 'selected'; ?>>Negative</option>
                                </select>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><strong>Blood Sugar</strong></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Fasting Blood Glucose </td>
                            <td><input type="number" step="0.01" min="0" name="fastingbloodglucose" value="<?php echo $testResult['fasting_blood_glucose']; ?>" disabled></td>
                            <td>Fasting: 3.9-5.6 mmol/L<br> Random: 3.9-7.7 mmol/L</td>
                        </tr>
                        <tr>
                            <td>HbA1c</td>
                            <td><input type="number" step="0.01" min="0" name="HbA1c" value="<?php echo $testResult['HbA1c']; ?>" disabled></td>
                            <td>4.3 - 5.8 %</td>
                        </tr>
                        <tr>
                            <td>Insulin</td>
                            <td><input type="number" step="0.01" min="0" name="Insulin" value="<?php echo $testResult['insulin']; ?>" disabled></td>
                            <td>2.6-24.9 μU/ml</td>
                        </tr>
                        <tr>
                            <td><strong>Uric Acid</strong></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Uric Acid</td>
                            <td><input type="number" step="0.01" min="0" name="uricacid" value="<?php echo $testResult['uric_acid']; ?>" disabled></td>
                            <td>M:0.19-0.40 ; F:0.14-0.33 mmol/L</td>
                        </tr>
                        <tr>
                            <td><strong>Blood Lipid Test</strong></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Triglyceride</td>
                            <td><input type="number" step="0.01" min="0" name="triglyceride" value="<?php echo $testResult['triglyceride']; ?>" disabled></td>
                            <td>0.40 - 1.70</td>
                        </tr>
                        <tr>
                            <td>Total Cholesterol</td>
                            <td><input type="number" step="0.01" min="0" name="totalcholesterol" value="<?php echo $testResult['total_cholesterol']; ?>" disabled></td>
                            <td>3.40-5.20</td>
                        </tr>
                        <tr>
                            <td>HDL Cholesterol</td>
                            <td><input type="number" step="0.01" min="0" name="hdl" value="<?php echo $testResult['HDL_cholesterol']; ?>" disabled></td>
                            <td>> 1.04</td>
                        </tr>
                        <tr>
                            <td>LDL Cholesterol</td>
                            <td><input type="number" step="0.01" min="0" name="ldl" value="<?php echo $testResult['LDL_cholesterol']; ?>" disabled></td>
                            <td>< 2.6</td>
                        </tr>
                        <tr>
                            <td >Interpretation</td>
                            <td colspan="2"><textarea name="interpretationblood" disabled><?php echo $testResult['interpretationblood']; ?></textarea></td>
                        </tr>
                    </table>
                </div>
            </div>

            <br>

            <div class="container">
                <div class="test-results">
                    <h2>Urine Test</h2>
                    <p>Urine tests include routine urine examinations, sediment, etc. that can detect the risk of urinary tract infection, kidney disease, diabetes, and other diseases.</p>
                    <br>
                    <table>
                        <tr>
                            <th>Screening Items</th>
                            <th>Result</th>
                            <th>Reference Range</th>
                        </tr>
                        <tr>
                            <td><strong>Urinalysis</strong></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Appearance</td>
                            <td>
                                <select class="form-control" name="appearance" disabled>
                                    <option value="" disabled selected>-Select an option-</option>
                                    <option value="Clear" <?php if ($testResult['appearance'] == 'Clear') echo 'selected'; ?>>Clear</option>
                                    <option value="Cloudy" <?php if ($testResult['appearance'] == 'Cloudy') echo 'selected'; ?>>Cloudy</option>
                                    <option value="Yellow OR Amber" <?php if ($testResult['appearance'] == 'Yellow OR Amber') echo 'selected'; ?>>Yellow OR Amber</option>
                                    <option value="Pink OR Red" <?php if ($testResult['appearance'] == 'Pink OR Red') echo 'selected'; ?>>Pink OR Red</option>
                                    <option value="Dark Brown" <?php if ($testResult['appearance'] == 'Dark Brown') echo 'selected'; ?>>Dark Brown</option>
                                    <option value="Orange" <?php if ($testResult['appearance'] == 'Orange') echo 'selected'; ?>>Orange</option>
                                    <option value="Blue or Green" <?php if ($testResult['appearance'] == 'Blue or Green') echo 'selected'; ?>>Blue or Green</option>
                                </select>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Protein</td>
                            <td>
                                <select class="form-control" name="protein" disabled>
                                    <option value="" disabled selected>-Select an option-</option>
                                    <option value="Positive" <?php if ($testResult['protein'] == 'Positive') echo 'selected'; ?>>Positive</option>
                                    <option value="Negative" <?php if ($testResult['protein'] == 'Negative') echo 'selected'; ?>>Negative</option>
                                </select>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Glucose</td>
                            <td>
                                <select class="form-control" name="glucose" disabled>
                                    <option value="" disabled selected>-Select an option-</option>
                                    <option value="Positive" <?php if ($testResult['glucose'] == 'Positive') echo 'selected'; ?>>Positive</option>
                                    <option value="Negative" <?php if ($testResult['glucose'] == 'Negative') echo 'selected'; ?>>Negative</option>
                                </select>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Bilirubin</td>
                            <td>
                                <select class="form-control" name="bilirubin" disabled>
                                    <option value="" disabled selected>-Select an option-</option>
                                    <option value="Positive" <?php if ($testResult['bilirubin'] == 'Positive') echo 'selected'; ?>>Positive</option>
                                    <option value="Negative" <?php if ($testResult['bilirubin'] == 'Negative') echo 'selected'; ?>>Negative</option>
                                </select>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Urobilinogen</td>
                            <td>
                                <select class="form-control" name="urobilinogen" disabled>
                                    <option value="" disabled selected>-Select an option-</option>
                                    <option value="Positive" <?php if ($testResult['urobilinogen'] == 'Positive') echo 'selected'; ?>>Positive</option>
                                    <option value="Negative" <?php if ($testResult['urobilinogen'] == 'Negative') echo 'selected'; ?>>Negative</option>
                                </select>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Occult Blood</td>
                            <td>
                                <select class="form-control" name="occultblood" disabled>
                                    <option value="" disabled selected>-Select an option-</option>
                                    <option value="Positive" <?php if ($testResult['occult_blood'] == 'Positive') echo 'selected'; ?>>Positive</option>
                                    <option value="Negative" <?php if ($testResult['occult_blood'] == 'Negative') echo 'selected'; ?>>Negative</option>
                                </select>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Ketone Bodies</td>
                            <td>
                                <select class="form-control" name="ketonebodies" disabled>
                                    <option value="" disabled selected>-Select an option-</option>
                                    <option value="Positive" <?php if ($testResult['ketone_bodies'] == 'Positive') echo 'selected'; ?>>Positive</option>
                                    <option value="Negative" <?php if ($testResult['ketone_bodies'] == 'Negative') echo 'selected'; ?>>Negative</option>
                                </select>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>pH</td>
                            <td><input type="number" step="0.01" min="0" name="ph" value="<?php echo $testResult['pH']; ?>" disabled></td>
                            <td>5.0 - 8.0</td>
                        </tr>
                    
                        <tr>
                            <td >Interpretation</td>
                            <td colspan="2"><textarea name="interpretationurine" disabled><?php echo $testResult['interpretationurine']; ?></textarea></td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <br>

            <div class="container">
                <div class="test-results">
                    <h2>Audiometry</h2>
                    <p>Noise pollution has become a modern plague that produces direct and cumulative adverse effects on our hearing by damaging the cells and membranes in the cochlear of the ear. We uses a professional-standard soundproof audiometric booth to carry out the hearing test for a more accurate result.</p>
                    <br>
                    <table>
                        <tr>
                            <th>Screening Items</th>
                            <th>Result</th>
                            <th>Reference Range</th>
                        </tr>
                        <tr>
                            <td>Right Ear</td>
                            <td><input type="number"  min="0" name="rightear" value="<?php echo $testResult['right_ear_result']; ?>" disabled></td>
                            <td>Normal: <= 30 <br>Mild: 31-50 <br>Moderate: 51-70<br>Severe: >70</td>
                        </tr>
                        <tr>
                            <td>Left Ear</td>
                            <td><input type="number"  min="0" name="leftear" value="<?php echo $testResult['left_ear_result']; ?>" disabled></td>
                            <td>Normal: <= 30 <br>Mild: 31-50 <br>Moderate: 51-70<br>Severe: >70</td>
                        </tr>
                        <tr>
                            <td >Interpretation</td>
                            <td colspan="2"><textarea name="interpretationaudiometry" disabled><?php echo $testResult['interpretationaudio']; ?></textarea></td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <br>

            <div class="container">
                <div class="test-results">
                    <h2>Ultrasound Examination</h2>
                    <p>Abdominal ultrasound is a safe and painless imaging test using sound waves to visualize the upper abdominal structures such as the liver, gall bladder, pancreas, spleen, kidneys, and the major blood vessels and their pathological changes.</p>
                    <br>
                    <table>
                        <tr>
                            <th>Screening Items</th>
                            <th>Result</th>
                            <th>Reference Range</th>
                        </tr>
                        <tr>
                            <td>Liver</td>
                            <td>
                            <select class="form-control" name="liver" disabled>
                                <option value="" disabled selected>-Select an option-</option>
                                <option value="Normal" <?php if ($testResult['liver'] == 'Normal') echo 'selected'; ?>>Normal</option>
                                <option value="Abnormal" <?php if ($testResult['liver'] == 'Abnormal') echo 'selected'; ?>>Abnormal</option>
                            </select>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Hepatic</td>
                            <td>
                                <select class="form-control" name="hepatic" disabled>
                                <option value="" disabled selected>-Select an option-</option>
                                <option value="Normal" <?php if ($testResult['hepatic'] == 'Normal') echo 'selected'; ?>>Normal</option>
                                <option value="Abnormal" <?php if ($testResult['hepatic'] == 'Abnormal') echo 'selected'; ?>>Abnormal</option>
                            </select>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Gall Bladder</td>
                            <td>
                                <select class="form-control" name="gallbladder" disabled>
                                <option value="" disabled selected>-Select an option-</option>
                                <option value="Normal" <?php if ($testResult['gall_bladder'] == 'Normal') echo 'selected'; ?>>Normal</option>
                                <option value="Abnormal" <?php if ($testResult['gall_bladder'] == 'Abnormal') echo 'selected'; ?>>Abnormal</option>
                            </select>
                            </td>
                            <td></td>
                        </tr>
                    
                        <tr>
                            <td>Intrahepatic Bile Duct</td>
                            <td>
                                <select class="form-control" name="intrahepaticbileduct" disabled>
                                <option value="" disabled selected>-Select an option-</option>
                                <option value="Normal" <?php if ($testResult['intrahepatic_bile_duct'] == 'Normal') echo 'selected'; ?>>Normal</option>
                                <option value="Abnormal" <?php if ($testResult['intrahepatic_bile_duct'] == 'Abnormal') echo 'selected'; ?>>Abnormal</option>
                            </select>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Common Bile Duct</td>
                            <td>
                                <select class="form-control" name="commonbileduct" disabled>
                                <option value="" disabled selected>-Select an option-</option>
                                <option value="Normal" <?php if ($testResult['common_bile_duct'] == 'Normal') echo 'selected'; ?>>Normal</option>
                                <option value="Abnormal" <?php if ($testResult['common_bile_duct'] == 'Abnormal') echo 'selected'; ?>>Abnormal</option>
                            </select>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Kidney</td>
                            <td>
                                <select class="form-control" name="kidney" disabled>
                                <option value="" disabled selected>-Select an option-</option>
                                <option value="Normal" <?php if ($testResult['kidney'] == 'Normal') echo 'selected'; ?>>Normal</option>
                                <option value="Abnormal" <?php if ($testResult['kidney'] == 'Abnormal') echo 'selected'; ?>>Abnormal</option>
                            </select>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Pancreas</td>
                            <td>
                                <select class="form-control" name="pancreas" disabled>
                                <option value="" disabled selected>-Select an option-</option>
                                <option value="Normal" <?php if ($testResult['pancreas'] == 'Normal') echo 'selected'; ?>>Normal</option>
                                <option value="Abnormal" <?php if ($testResult['pancreas'] == 'Abnormal') echo 'selected'; ?>>Abnormal</option>>
                            </select>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Spleen/Others</td>
                            <td>
                                <select class="form-control" name="spleenothers" disabled>
                                <option value="" disabled selected>-Select an option-</option>
                                <option value="Normal" <?php if ($testResult['spleen_others'] == 'Normal') echo 'selected'; ?>>Normal</option>
                                <option value="Abnormal" <?php if ($testResult['spleen_others'] == 'Abnormal') echo 'selected'; ?>>Abnormal</option>
                            </select>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td >Interpretation</td>
                            <td colspan="2"><textarea name="interpretationultrasound" disabled><?php echo $testResult['interpretationultrasound']; ?></textarea></td>
                        </tr>
                    </table>
                </div>
            </div>

            <br>
            <div class="container">
                <center>
                    <a href="patient_viewhealthreport00.php" class="btn btn-secondary">Return Page</a>
                </center>
            
            </div>
        </form>

        <!-- Bootstrap JS -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

        <!-- Custom script for scrolling to top and bottom -->
        <script>
            function scrollToBottom() {
                // Scroll to the bottom of the page smoothly
                window.scrollTo({
                    top: document.body.scrollHeight,
                    behavior: 'smooth'
                });
            }

            function scrollToTop() {
                // Scroll to the top of the page smoothly
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            }
        </script>
    </body>
</html>