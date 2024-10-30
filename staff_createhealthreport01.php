<?php
     include("securityauth.php");
     include("database.php");
    restrictAccessByRole(['staff']);

    // Check if the user ID is provided in the query parameter
if (isset($_GET['user_id'])) {
    $userId = $_GET['user_id'];

    // Fetch the user information based on the user ID
    $sql = "SELECT * FROM users WHERE user_id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $userId); // Assuming user_id is an integer
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if a user with the provided ID exists
    if ($result->num_rows > 0) {
        $member = $result->fetch_assoc();
    } else {
        // Handle case where user does not exist
        echo "<script>
            alert('Error: User does not exist!');
            window.location.href='staff_createhealthreport00.php';
        </script>";
        exit(); // Stop the script if the staff key is invalid
    }
} else {
    // Handle case where user ID is not provided
    echo "<script>
    alert('Error: User ID post error!');
        window.location.href='staff_createhealthreport00.php';
    </script>";
    exit(); // Stop the script if the staff key is invalid
    exit;
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Define variables to store form data
    $userId = $_GET['user_id'];
    
    $height = $_POST['height'];
    $weight = $_POST['weight'];
    $bmi = $_POST['bmi'];
    $body_fat_percentage = $_POST['bodyfat'];
    //$skeleton_muscle_mass = $_POST['skeletonmusclemass'];
    $skeleton_muscle_mass = !empty($_POST['skeletonmusclemass']) ? $_POST['skeletonmusclemass'] : NULL;
    $waist_hip_ratio = $_POST['waisthipratio'];
    $blood_pressure = $_POST['bloodpressure'];
    $pulse_rate = $_POST['pulserate'];
    $respiratory_rate = $_POST['respiratoryrate'];
    $chest_circumferences = $_POST['chestcircumferences'];
    $interpretationgeneral = $_POST['interpretationgeneral'];


    $hemoglobin = $_POST['hemoglobin'];
    $hematocrit = $_POST['hematocrit'];
    $rbc = $_POST['rbc'];
    $mch = $_POST['mch'];
    $mchc = $_POST['mchc'];
    $rdw = $_POST['rdw'];
    //$abo = $_POST['abo'];
    $abo = !empty($_POST['abo']) ? $_POST['abo'] : NULL; // Check if ABO field is empty
    //$rhd = $_POST['rhd'];
    $rhd = !empty($_POST['rhd']) ? $_POST['rhd'] : NULL;
    $fastingbloodglucose = $_POST['fastingbloodglucose'];
    $HbA1c = $_POST['HbA1c'];
    $Insulin = $_POST['Insulin'];
    $uricacid = $_POST['uricacid'];
    $triglyceride = $_POST['triglyceride'];
    $totalcholesterol = $_POST['totalcholesterol'];
    $hdl = $_POST['hdl'];
    $ldl = $_POST['ldl'];
    $interpretationblood = $_POST['interpretationblood'];


    //$appearance = $_POST['appearance'];
    $appearance = !empty($_POST['appearance']) ? $_POST['appearance'] : NULL;
    //$protein = $_POST['protein'];
    $protein = !empty($_POST['protein']) ? $_POST['protein'] : NULL;
    //$glucose = $_POST['glucose'];
    $glucose = !empty($_POST['glucose']) ? $_POST['glucose'] : NULL;
    //$bilirubin = $_POST['bilirubin'];
    $bilirubin = !empty($_POST['bilirubin']) ? $_POST['bilirubin'] : NULL;
    //$urobilinogen = $_POST['urobilinogen'];
    $urobilinogen = !empty($_POST['urobilinogen']) ? $_POST['urobilinogen'] : NULL;
    //$occultblood = $_POST['occultblood'];
    $occultblood = !empty($_POST['occultblood']) ? $_POST['occultblood'] : NULL;
    //$ketonebodies = $_POST['ketonebodies'];
    $ketonebodies = !empty($_POST['ketonebodies']) ? $_POST['ketonebodies'] : NULL;
    $ph = $_POST['ph'];
    $interpretationurine = $_POST['interpretationurine'];

    $rightear = $_POST['rightear'];
    $leftear = $_POST['leftear'];
    $interpretationaudiometry = $_POST['interpretationaudiometry'];

    //$liver = $_POST['liver'];
    $liver = !empty($_POST['liver']) ? $_POST['liver'] : NULL;
    //$hepatic = $_POST['hepatic'];
    $hepatic = !empty($_POST['hepatic']) ? $_POST['hepatic'] : NULL;
    //$gallbladder = $_POST['gallbladder'];
    $gallbladder = !empty($_POST['gallbladder']) ? $_POST['gallbladder'] : NULL;
    //$intrahepaticbileduct = $_POST['intrahepaticbileduct'];
    $intrahepaticbileduct = !empty($_POST['intrahepaticbileduct']) ? $_POST['intrahepaticbileduct'] : NULL;
    //$commonbileduct = $_POST['commonbileduct'];
    $commonbileduct = !empty($_POST['commonbileduct']) ? $_POST['commonbileduct'] : NULL;
    //$kidney = $_POST['kidney'];
    $kidney = !empty($_POST['kidney']) ? $_POST['kidney'] : NULL;
    //$pancreas = $_POST['pancreas'];
    $pancreas = !empty($_POST['pancreas']) ? $_POST['pancreas'] : NULL;
    //$spleenothers = $_POST['spleenothers'];
    $spleenothers = !empty($_POST['spleenothers']) ? $_POST['spleenothers'] : NULL;
    $interpretationultrasound = $_POST['interpretationultrasound'];


    $sql = "INSERT INTO testresult (user_id, test_date, status, height, weight, bmi, body_fat_percentage, skeleton_muscle_mass, waist_hip_ratio, blood_pressure, pulse_rate, respiratory_rate, chest_circumferences, interpretationgeneral, 
    hemoglobin, hematocrit, red_blood_cell_count, mean_corpuscular_hemoglobin, mean_corpuscular_hemoglobin_concentration, red_cell_distribution_width, blood_grouping_ABO, blood_grouping_Rh, fasting_blood_glucose, HbA1c, insulin, uric_acid, triglyceride, total_cholesterol, HDL_cholesterol, LDL_cholesterol, interpretationblood, 
    appearance, protein, glucose, bilirubin, urobilinogen, occult_blood, ketone_bodies, pH, interpretationurine, 
    right_ear_result, left_ear_result, interpretationaudio, 
    liver, hepatic, gall_bladder, intrahepatic_bile_duct, common_bile_duct, kidney, pancreas, spleen_others, interpretationultrasound) 
    VALUES ('$userId', NOW(),'Successful','$height', '$weight', '$bmi', '$body_fat_percentage', '$skeleton_muscle_mass', '$waist_hip_ratio', '$blood_pressure', '$pulse_rate', '$respiratory_rate', '$chest_circumferences', '$interpretationgeneral', 
        '$hemoglobin', '$hematocrit', '$rbc', '$mch', '$mchc', '$rdw', '$abo', '$rhd', '$fastingbloodglucose', '$HbA1c', '$Insulin', '$uricacid', '$triglyceride', '$totalcholesterol', '$hdl', '$ldl', '$interpretationblood', 
        '$appearance', '$protein', '$glucose', '$bilirubin', '$urobilinogen', '$occultblood', '$ketonebodies', '$ph', '$interpretationurine', 
        '$rightear', '$leftear', '$interpretationaudiometry', 
        '$liver', '$hepatic', '$gallbladder', '$intrahepaticbileduct', '$commonbileduct', '$kidney', '$pancreas', '$spleenothers', '$interpretationultrasound')";

    // Execute the SQL query
    if(mysqli_query($con, $sql)){
    echo "<script>
        alert('Records inserted successfully');
        window.location.href='staff_createhealthreport00.php';
    </script>";
    } else{
        echo "<script>
            alert('ERROR: Could not able to execute $sql');
        </script>";
    }
}

?>





<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <script src="js/scripts.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script> <!-- Bootstrap core JS-->
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
        <form method="post">
            <div class="container">
                <h1>Health Report</h1>
                <div class="patient-info">
                    <h2>Member Information</h2>
                    <p><strong>User ID:</strong> <?php echo $member['user_id']; ?></p>
                    <p><strong>Name:</strong> <?php echo $member['first_name']; ?> <?php echo $member['last_name']; ?></p>
                    <p><strong>Gender:</strong> <?php echo $member['gender']; ?></p>
                    <p><strong>Date of birth:</strong> <?php echo $member['dob']; ?></p>
                    <p><strong>Email</strong> <?php echo $member['email']; ?></p>
                    <p><strong>Phone</strong> <?php echo $member['phone_number']; ?></p>
                    <!--<p><strong>Date of test:</strong> February 12, 2023</p>
                    <p><strong>Medical record number:</strong> 00.991.23</p>
                    <p><strong>Clinical history:</strong> N/A - Routine check-up</p>-->
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
                            <td><input type="number" step="0.01" min="0" name="height"></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Body Weight (KG)</td>
                            <td><input type="number" step="0.01" min="0" name="weight"></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>BMI</td>
                            <td><input type="number" step="0.01" min="0" name="bmi"></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Body Fat (%)</td>
                            <td><input type="number" step="0.01" min="0" name="bodyfat"></td>
                            <td>≤30yrs: M:14-20 ; F:17-24 <br> >30yrs: M:17-23 ; F:20-27</td>
                        </tr>
                        <tr>
                            <td>Skeleton Muscle Mass</td>
                            <td>
                                <select class="form-control" name="skeletonmusclemass">
                                    <option value="" disabled selected>-Select an option-</option>
                                    <option value="under">Under</option>
                                    <option value="normal">Normal</option>
                                    <option value="over">Over</option>
                                </select>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Waist/Hip Ratio</td>
                            <td><input type="number" step="0.01" min="0" name="waisthipratio"></td>
                            <td>M: < 0.91 ; F: < 0.88 </td>
                        </tr>
                        <tr>
                            <td>Blood Pressure (mmHg)</td>
                            <td><input type="number" step="0.01" min="0" name="bloodpressure"></td>
                            <td>< 130/85</td>
                        </tr>
                        <tr>
                            <td>Pulse Rate</td>
                            <td><input type="number" step="0.01" min="0" name="pulserate"></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Respiratory Rate (BPM)</td>
                            <td><input type="number" step="0.01" min="0" name="respiratoryrate"></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Chest Circumferences (cm)</td>
                            <td><input type="number" step="0.01" min="0" name="chestcircumferences"></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td >Interpretation</td>
                            <td colspan="2"><textarea name="interpretationgeneral"></textarea></td>
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
                            <td><input type="number" step="0.01" min="0" name="hemoglobin"></td>
                            <td>13.5 - 17.5 g/dL</td>
                        </tr>
                        <tr>
                            <td>Hematocrit (Hct)</td>
                            <td><input type="number" step="0.01" min="0" name="hematocrit"></td>
                            <td>38.3 - 48.6%</td>
                        </tr>
                        <tr>
                            <td>Red Blood Cell Count (RBC)</td>
                            <td><input type="number" step="0.01" min="0" name="rbc"></td>
                            <td>4.5 - 6.0 x10^6/μL</td>
                        </tr>
                        <tr>
                            <td>Mean Corpuscular Hemoglobin (MCH)</td>
                            <td><input type="number" step="0.01" min="0" name="mch"></td>
                            <td>27.0 - 33.0 pg</td>
                        </tr>
                        <tr>
                            <td>Mean Corpuscular Hemoglobin Concentration (MCHC)</td>
                            <td><input type="number" step="0.01" min="0" name="mchc"></td>
                            <td>31.5 - 35.5 g/dL</td>
                        </tr>
                        <tr>
                            <td>Red Cell Distribution Width (RDW)</td>
                            <td><input type="number" step="0.01" min="0" name="rdw"></td>
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
                                <select class="form-control" name="abo">
                                    <option value="" disabled selected>-Select an option-</option>
                                    <option value="A">A</option>
                                    <option value="B">B</option>
                                    <option value="AB">AB</option>
                                    <option value="O">O</option>
                                </select>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Rh(D)Factor</td>
                            <td>
                                <select class="form-control" name="rhd">
                                    <option value="" disabled selected>-Select an option-</option>
                                    <option value="Positive">Positive</option>
                                    <option value="Negative">Negative</option>
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
                            <td><input type="number" step="0.01" min="0" name="fastingbloodglucose"></td>
                            <td>Fasting: 3.9-5.6 mmol/L<br> Random: 3.9-7.7 mmol/L</td>
                        </tr>
                        <tr>
                            <td>HbA1c</td>
                            <td><input type="number" step="0.01" min="0" name="HbA1c"></td>
                            <td>4.3 - 5.8 %</td>
                        </tr>
                        <tr>
                            <td>Insulin</td>
                            <td><input type="number" step="0.01" min="0" name="Insulin"></td>
                            <td>2.6-24.9 μU/ml</td>
                        </tr>
                        <tr>
                            <td><strong>Uric Acid</strong></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Uric Acid</td>
                            <td><input type="number" step="0.01" min="0" name="uricacid"></td>
                            <td>M:0.19-0.40 ; F:0.14-0.33 mmol/L</td>
                        </tr>
                        <tr>
                            <td><strong>Blood Lipid Test</strong></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Triglyceride</td>
                            <td><input type="number" step="0.01" min="0" name="triglyceride"></td>
                            <td>0.40 - 1.70</td>
                        </tr>
                        <tr>
                            <td>Total Cholesterol</td>
                            <td><input type="number" step="0.01" min="0" name="totalcholesterol"></td>
                            <td>3.40-5.20</td>
                        </tr>
                        <tr>
                            <td>HDL Cholesterol</td>
                            <td><input type="number" step="0.01" min="0" name="hdl"></td>
                            <td>> 1.04</td>
                        </tr>
                        <tr>
                            <td>LDL Cholesterol</td>
                            <td><input type="number" step="0.01" min="0" name="ldl"></td>
                            <td>< 2.6</td>
                        </tr>
                        <tr>
                            <td >Interpretation</td>
                            <td colspan="2"><textarea name="interpretationblood"></textarea></td>
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
                                <select class="form-control" name="appearance">
                                    <option value="" disabled selected>-Select an option-</option>
                                    <option value="Clear">Clear</option>
                                    <option value="Cloudy">Cloudy</option>
                                    <option value="Yellow OR Amber">Yellow OR Amber</option>
                                    <option value="Pink OR Red">Pink OR Red</option>
                                    <option value="Dark Brown">Dark Brown</option>
                                    <option value="Orange">Orange</option>
                                    <option value="Blue or Green">Blue or Green</option>
                                
                                </select>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Protein</td>
                            <td>
                                <select class="form-control" name="protein">
                                    <option value="" disabled selected>-Select an option-</option>
                                    <option value="Positive">Positive</option>
                                    <option value="Negative">Negative</option>
                                </select>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Glucose</td>
                            <td>
                                <select class="form-control" name="glucose">
                                    <option value="" disabled selected>-Select an option-</option>
                                    <option value="Positive">Positive</option>
                                    <option value="Negative">Negative</option>
                                </select>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Bilirubin</td>
                            <td>
                                <select class="form-control" name="bilirubin">
                                    <option value="" disabled selected>-Select an option-</option>
                                    <option value="Positive">Positive</option>
                                    <option value="Negative">Negative</option>
                                </select>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Urobilinogen</td>
                            <td>
                                <select class="form-control" name="urobilinogen">
                                    <option value="" disabled selected>-Select an option-</option>
                                    <option value="Positive">Positive</option>
                                    <option value="Negative">Negative</option>
                                </select>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Occult Blood</td>
                            <td>
                                <select class="form-control" name="occultblood">
                                    <option value="" disabled selected>-Select an option-</option>
                                    <option value="Positive">Positive</option>
                                    <option value="Negative">Negative</option>
                                </select>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Ketone Bodies</td>
                            <td>
                                <select class="form-control" name="ketonebodies">
                                    <option value="" disabled selected>-Select an option-</option>
                                    <option value="Positive">Positive</option>
                                    <option value="Negative">Negative</option>
                                </select>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>pH</td>
                            <td><input type="number" step="0.01" min="0" name="ph"></td>
                            <td>5.0 - 8.0</td>
                        </tr>
                    
                        <tr>
                            <td >Interpretation</td>
                            <td colspan="2"><textarea name="interpretationurine"></textarea></td>
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
                            <td><input type="number"  min="0" name="rightear"></td>
                            <td>Normal: <= 30 <br>Mild: 31-50 <br>Moderate: 51-70<br>Severe: >70</td>
                        </tr>
                        <tr>
                            <td>Left Ear</td>
                            <td><input type="number"  min="0" name="leftear"></td>
                            <td>Normal: <= 30 <br>Mild: 31-50 <br>Moderate: 51-70<br>Severe: >70</td>
                        </tr>
                        <tr>
                            <td >Interpretation</td>
                            <td colspan="2"><textarea name="interpretationaudiometry"></textarea></td>
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
                            <select class="form-control" name="liver">
                                <option value="" disabled selected>-Select an option-</option>
                                <option value="Normal">Normal</option>
                                <option value="Abnormal">Abnormal</option>
                            </select>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Hepatic</td>
                            <td>
                                <select class="form-control" name="hepatic">
                                <option value="" disabled selected>-Select an option-</option>
                                <option value="Normal">Normal</option>
                                <option value="Abnormal">Abnormal</option>
                            </select>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Gall Bladder</td>
                            <td>
                                <select class="form-control" name="gallbladder">
                                <option value="" disabled selected>-Select an option-</option>
                                <option value="Normal">Normal</option>
                                <option value="Abnormal">Abnormal</option>
                            </select>
                            </td>
                            <td></td>
                        </tr>

                        <tr>
                            <td>Intrahepatic Bile Duct</td>
                            <td>
                                <select class="form-control" name="intrahepaticbileduct">
                                <option value="" disabled selected>-Select an option-</option>
                                <option value="Normal">Normal</option>
                                <option value="Abnormal">Abnormal</option>
                            </select>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Common Bile Duct</td>
                            <td>
                                <select class="form-control" name="commonbileduct">
                                <option value="" disabled selected>-Select an option-</option>
                                <option value="Normal">Normal</option>
                                <option value="Abnormal">Abnormal</option>
                            </select>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Kidney</td>
                            <td>
                                <select class="form-control" name="kidney">
                                <option value="" disabled selected>-Select an option-</option>
                                <option value="Normal">Normal</option>
                                <option value="Abnormal">Abnormal</option>
                            </select>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Pancreas</td>
                            <td>
                                <select class="form-control" name="pancreas">
                                <option value="" disabled selected>-Select an option-</option>
                                <option value="Normal">Normal</option>
                                <option value="Abnormal">Abnormal</option>
                            </select>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Spleen/Others</td>
                            <td>
                                <select class="form-control" name="spleenothers">
                                <option value="" disabled selected>-Select an option-</option>
                                <option value="Normal">Normal</option>
                                <option value="Abnormal">Abnormal</option>
                            </select>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td >Interpretation</td>
                            <td colspan="2"><textarea name="interpretationultrasound"></textarea></td>
                        </tr>
                    </table>
                </div>
            </div>

            <br>
            <div class="container">
                <center>
                    <!--<button class="btn btn-primary">Save as Draft</button>-->
                    <button class="btn btn-success"  type="submit" name="submit">Submit</button>
                    <a href="staff_home.php" class="btn btn-secondary">Back to Home</a>
                </center>
            
            </div>
        </form>
    </body>
</html>