<?php
include("securityauth.php");
restrictAccessByRole(['admin']);
include("databasegetdata.php");
include("database.php");

// Overview
    // 执行查询以获取客户数量
    $query1 = $db->prepare("SELECT COUNT(*) FROM users WHERE role = 'member'");
    $query2 = $db->prepare("SELECT COUNT(*) FROM users WHERE role = 'staff' or role = 'admin'");
    $query3 = $db->prepare("SELECT COUNT(*) AS total FROM information_schema.tables WHERE table_schema = 'fyp'");
    $query4 = $db->prepare("SELECT COUNT(*) FROM homefeedback");
    //$query5 =a $db->prepare("SELECT COUNT(*) FROM complainfeedback");


    $query1->execute();
    $query2->execute();
    $query3->execute();
    $query4->execute();
    //$query5->execute();

    $customerCount = $query1->fetchColumn();
    $staffCount = $query2->fetchColumn();
    $tableCount = $query3->fetchColumn();

    $homeFeedbackCount = $query4->fetchColumn();
    //$complainFeedbackCount = $query5->fetchColumn();
    //$totalFeedbackCount = $homeFeedbackCount + $complainFeedbackCount;

   

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <script src="js/scripts.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script> <!-- Bootstrap core JS-->
    
    <!--Last Updated time-->
    <script>
    function displayDate() {
    var d = new Date();
    document.getElementById("lastupdatetime").innerHTML = d;
    }
    </script>
    <script>
        window.onload = function() {
            var scrollPos = localStorage.getItem('scrollPos');
            if (scrollPos) window.scrollTo(0, scrollPos);
        }

        window.onbeforeunload = function() {
            localStorage.setItem('scrollPos', window.scrollY);
        }
    </script>
    
    <link href="css/styles.css" rel="stylesheet" />
    <title>Admin Dashboard</title>



</head>

<body id="page-top" onload="displayDate()">
        <!-- Navigation-->
        <nav class="navbar navbar-expand-lg navbar-light fixed-top py-3" id="mainNav">
            <div class="container px-4 px-lg-5">
                <a class="navbar-brand" href="#page-top">MEDVault</a>
                <button class="navbar-toggler navbar-toggler-right" type="button" data-bs-toggle="collapse" data-bs-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
                <div class="collapse navbar-collapse" id="navbarResponsive">
                    <ul class="navbar-nav ms-auto my-2 my-lg-0">
                    <li class="nav-item"><a class="nav-link" href="#overview">Overview</a></li>
                        <li class="nav-item"><a class="nav-link" href="#manage">Manage</a></li>
                        <li class="nav-item"><a class="nav-link" href="#database">Database</a></li>
                        <li class="nav-item"><a class="nav-link" href="#setting">Setting</a></li>
                        <li class="nav-item"><a href="#" class="nav-link" onclick="logOut()">Log Out</a></li>
                    </ul>
                </div>
            </div>
        </nav>


        
        <!-- header -->
        <section class="page-section bg-secondary" id="top">
            <div class="container px-4 px-lg-5">
                <div class="row gx-4 gx-lg-5 justify-content-center">
                    <div class="col-lg-8 text-center">
                        <h2 class="text-white mt-0">MEDVault Healthcare</h2>
                        <hr class="divider divider-light" />
                        <p class="text-white-75 mb-4">Welcome back.<br><br>Keep your face always toward the sunshine, and shadows will fall behind you."<br> — Walt Whitman </p><br>
                        <a class="btn btn-light btn-xl" href="#overview">Get Started!</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Overview -->
        <section class="page-section" id="overview">
            <div class="container px-4 px-lg-5">
                <h2 class="text-center mt-0">Overview Status</h2>
                <hr>
                <div class="row gx-4 gx-lg-5">
                    <div class="col-lg-3 col-md-6 text-center">
                        <div class="mt-5">
                            <h3 class="h4 mb-2"><a class="nav-link" href="#manage">Customer</h3>
                            <br><br>
                            <h1><?php echo $customerCount; ?></h1></a>
                            
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 text-center">
                        <div class="mt-5">
                            <h3 class="h4 mb-2"><a class="nav-link" href="#manage">Staff & Admin</h3>
                            <br><br>
                            <h1><?php echo $staffCount; ?></h1></a>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 text-center">
                        <div class="mt-5">
                            <h3 class="h4 mb-2"><a class="nav-link" href="#database">Database</h3>
                            <br><br>
                            <h1><?php echo $tableCount; ?></h1></a>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 text-center">
                        <div class="mt-5">
                            <h3 class="h4 mb-2"><a class="nav-link" href="#">Feedback</h3>
                            <br><br>
                            <h1><?php echo $homeFeedbackCount; ?></h1></a>
                        </div>
                    </div>
                </div>
                <br><br><hr><br><br>
                <h6>Last Updated Time:</h6> <p id="lastupdatetime"></p>
            </div>
        
        </section>

        <!--Manage User-->
        <section class="page-section bg-white-smoke" id="manage">
            <h1 class = "mb-12 text-center">User & Token Management</h1><br><br>
            <div class="container px-4 px-lg-5 text-center justify-content-center">
                <p class="text-dark-75 mb-4">A comprehensive tool designed to efficiently handle user accounts. Seamlessly manage user profiles, edit details, and oversee staff tokens. Our platform facilitates activation token management, empowering users to seamlessly verify their accounts.</p><br>
                <!--<input class="btn btn-primary btn-xl " type="button" value="Generate Staff Key" action="admin_mauser_generatestaffkey.php">&nbsp;&nbsp;-->
                <!--<form action="generatestaffkey.php" method="get">
                    <input class="btn btn-primary btn-xl" type="submit" value="Generate Staff Key">
                </form>-->
                <a class="btn btn-light btn-xl" href="admin_manageuser.php">Go to detail user manager</a>
            </div>
        </section>

        <!--Redirect SQL-->
        <section class="page-section bg-columbia-blue text-black" id="database">
            <div class="container px-4 px-lg-5 text-center justify-content-center">
                <h2 class="mb-5 ">Manage MEDVault Database</h2>
                <p class="text-dark-75 mb-4">Navigating and administering the MEDVault database effortlessly. Seamlessly access and control your MySQL portal, ensuring smooth and efficient data management for your healthcare platform.</p><br>
                <a class="btn btn-light btn-xl" href="http://localhost/phpmyadmin/" target="_blank">Go to phpmyadmin</a>&nbsp;&nbsp;
                
            </div>
        </section>

        <!--Setting-->
        <section class="page-section bg-mint-cream text-black" id="setting">
            <div class="container px-4 px-lg-5 text-center justify-content-center">
                <h2 class="mb-5 ">Setting your Profile or Security</h2>
                <p class="text-dark-75 mb-4">Empower yourself by customizing your personal account profile and strengthening security measures. Seamlessly manage your profile settings and passwords with ease, ensuring a secure and personalized experience tailored to your needs.</p><br>
                
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