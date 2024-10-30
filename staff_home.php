<?php
include("securityauth.php");
restrictAccessByRole(['staff']);
?>





<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <script src="js/scripts.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script> <!-- Bootstrap core JS-->
    <link href="css/styles.css" rel="stylesheet" />
    <title>Staff Dashboard</title>
</head>
<body>
    <!-- Navigation-->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top py-3" id="mainNav">
            <div class="container px-4 px-lg-5">
                <a class="navbar-brand" href="#page-top">MEDVault</a>
                <button class="navbar-toggler navbar-toggler-right" type="button" data-bs-toggle="collapse" data-bs-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
                <div class="collapse navbar-collapse" id="navbarResponsive">
                    <ul class="navbar-nav ms-auto my-2 my-lg-0">
                    <li class="nav-item"><a class="nav-link" href="#appointment">Appointment</a></li>
                        <li class="nav-item"><a class="nav-link" href="#healthreport">Health Report</a></li>
                        <li class="nav-item"><a class="nav-link" href="#feedback">Feedback</a></li>
                        <li class="nav-item"><a class="nav-link" href="#setting">Setting</a></li>
                        <li class="nav-item"><a href="#" class="nav-link" onclick="logOut()">Log Out</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    
    <!-- header -->
    <section class="page-section bg-rose" id="top">
            <div class="container px-4 px-lg-5">
                <div class="row gx-4 gx-lg-5 justify-content-center">
                    <div class="col-lg-8 text-center">
                        <h2 class="text-white mt-0">MEDVault HealthCare</h2>
                        <hr class="divider divider-light" />
                        <p class="text-white-75 mb-4">Welcome back.<br><br>Keep your face always toward the sunshine, and shadows will fall behind you."<br> — Walt Whitman </p><br>
                        <a class="btn btn-light btn-xl" href="#appointment">Get Started!</a>
                    </div>
                </div>
            </div>
        </section>

    <!--Appointment-->
    <section class="page-section bg-periwinkle" id="appointment">
        <h1 class = "mb-12 text-center">Appointment</h1><br><br>
        <div class="container px-4 px-lg-5 text-center justify-content-center">
            <p class="text-dark-75 mb-4">Efficiently Manage Appointments and User Profiles. Seamlessly handle user accounts, edit details, and oversee staff tokens with our comprehensive platform. Activate tokens effortlessly, empowering users to verify their accounts seamlessly.</p><br>
            <a class="btn btn-light btn-xl" href="staff_appointment.php">Make Appointment</a> &nbsp;&nbsp;
            <a class="btn btn-light btn-xl" href="staff_viewappointmentstatus.php">Appointment Status</a> &nbsp;&nbsp;
            <a class="btn btn-light btn-xl" href="staff_createpackage">Manage Package</a>
        </div>
    </section>

     <!--Create Report-->
     <section class="page-section bg-white-smoke" id="healthreport">
        <h1 class = "mb-12 text-center">Health Report</h1><br><br>
        <div class="container px-4 px-lg-5 text-center justify-content-center">
            <p class="text-dark-75 mb-4">Empowering Health Management to Create member Personalized Health Report Today!</p><br>
            <!--<input class="btn btn-primary btn-xl " type="button" value="Generate Staff Key" action="admin_mauser_generatestaffkey.php">&nbsp;&nbsp;-->
            <!--<form action="generatestaffkey.php" method="get">
                <input class="btn btn-primary btn-xl" type="submit" value="Generate Staff Key">
            </form>-->
            <a class="btn btn-light btn-xl" href="staff_createhealthreport00.php">Create Health Report</a>
        </div>
    </section>


    <!--Feedback-->
    <section class="page-section bg-columbia-blue" id="feedback">
        <h1 class = "mb-12 text-center">Feedback</h1><br><br>
        <div class="container px-4 px-lg-5 text-center justify-content-center">
            <p class="text-dark-75 mb-4">Dive into valuable insights from our guests. Click below to view feedback.</p><br>
            <!--<input class="btn btn-primary btn-xl " type="button" value="Generate Staff Key" action="admin_mauser_generatestaffkey.php">&nbsp;&nbsp;-->
            <!--<form action="generatestaffkey.php" method="get">
                <input class="btn btn-primary btn-xl" type="submit" value="Generate Staff Key">
            </form>-->
            <a class="btn btn-light btn-xl" href="staffindexviewfeedback.php" >View Feedback</a>
        </div>
    </section>

    <!--Setting-->
    <section class="page-section bg-mint-cream text-black" id="setting">
        <div class="container px-4 px-lg-5 text-center justify-content-center">
            <h2 class="mb-5 ">Setting your Profile or Security</h2>
            <p class="text-dark-75 mb-4">Customize your profile and security settings effortlessly with just a click! Optimize your experience with our Profile and Security Settings options. </p><br>
            <a class="btn btn-light btn-xl" href="settingProfile">Profile Setting</a> &nbsp;&nbsp;
            <a class="btn btn-light btn-xl" href="settingSecurity">Security Setting</a>
        </div>
    </section>
    

     <!-- Footer-->
     <footer class="bg-light py-5">
            <div class="text-muted mb-5"><div class="small text-center text-muted">By HZY063988</div></div>
     </footer>
</body>
</html>