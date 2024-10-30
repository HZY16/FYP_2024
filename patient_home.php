<?php
    include("database.php");
    include("securityauth.php");
    restrictAccessByRole(['member']);


?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <script src="js/scripts.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script> <!-- Bootstrap core JS-->
    <link href="css/styles.css" rel="stylesheet" />
    <title>Customer Dashboard</title>
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
                        <li class="nav-item"><a class="nav-link" href="#news">News</a></li>
                        <li class="nav-item"><a class="nav-link" href="#healthrecord">Health Record</a></li>
                        <li class="nav-item"><a class="nav-link" href="#setting">Setting</a></li>
                        <li class="nav-item"><a href="#" class="nav-link" onclick="logOut()">Log Out</a></li>
                    </ul>
                </div>
            </div>
        </nav>

    <!-- header -->
    <section class="page-section bg-indogo" id="top">
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
            <p class="text-dark-75 mb-4">Efficiently manage your appointments with our comprehensive tool. Seamlessly schedule, view, and edit appointments to keep your healthcare journey on track. Empowering you to take control of your health.</p><br>
            <a class="btn btn-light btn-xl" href="patient_appointment.php">Appointment</a> &nbsp;&nbsp;
            <a class="btn btn-light btn-xl" href="patient_viewappointment.php">Appointment History</a>
        </div>
    </section>


    <section class="page-section bg-columbia-blue" id="news">
    <div class="container">
        <h2 class="text-center mb-4">Healthcare & Wellness News</h2>
        <div class="row">

            <?php
            
            //Backup Key
            /*$apiKey = '933e09cbd3394333a934f8fd0c02e1f2';
            cociyiw954@em2lab.com
            702324e8ba134ea4ae12f9a41a3ae41a
            ganof79239@funvane.com
            d0447c425bdb49ae9346e57d7ddf5c80
            ladowa5692@picdv.com
            8da5bca5f4744b9887e341e42790efb9
            javay38840@idsho.com
            e080d10e6c444df69d0756fe0e0043b5
            */

            // Construct the URL with the API key and parameters
            //$url = 'https://newsapi.org/v2/everything?q=wellness&apiKey=46ff3eb7a60147cbbcb14b30ac22c02b';
            //$url = 'https://newsapi.org/v2/everything?q=Wellness&sortBy=popularity&apiKey=e080d10e6c444df69d0756fe0e0043b5';
            //$url = 'https://newsapi.org/v2/everything?q=healthcare+wellness&sortBy=popularity&apiKey=e080d10e6c444df69d0756fe0e0043b5';
            //$url = 'newsapi\news.json';
            $url = 'newsapi\healthcarewellness.json';
            // Load news data from the local JSON file
            $newsData = json_decode(file_get_contents($url), true);

            // Check if news articles are present
            if ($newsData && isset($newsData['articles'])) {
                // Get the total number of articles
                $totalArticles = count($newsData['articles']);

                // Shuffle the array of news articles
                shuffle($newsData['articles']);

                // Select random six articles
                $randomArticles = array_slice($newsData['articles'], 0, 3);

                // Loop through each random article
                foreach ($randomArticles as $article) {
                    ?>
                    <div class="col-md-4 col-lg-4 mb-4">
                        <div class="card h-100">
                            <?php if(isset($article['urlToImage']) && !empty($article['urlToImage'])) { ?>
                                <img src="<?php echo $article['urlToImage']; ?>" class="card-img-top" alt="Article Image">
                            <?php } ?>
                            <div class="card-body">
                                <h3 class="card-title"><?php echo $article['title']; ?></h3>
                                <p class="card-text"><?php echo $article['description']; ?></p>
                            </div>
                            <div class="card-footer">
                                <a href="<?php echo $article['url']; ?>" class="btn btn-primary" target='_blank'>Read More</a>
                            </div>
                        </div>
                    </div>
                <?php
                }
            } else {
                echo '<p class="text-center">No news articles found.</p>';
            }
            ?>
        </div>
    </div>
</section>

    <!--Health Record-->
    <section class="page-section bg-white-smoke" id="healthrecord">
        <h1 class = "mb-12 text-center">Health Record</h1><br><br>
        <div class="container px-4 px-lg-5 text-center justify-content-center">
            <p class="text-dark-75 mb-4">Explore your health journey effortlessly with our comprehensive platform. Seamlessly manage profiles, access detailed health records, and empower yourself with vital insights. </p><br>
            <a class="btn btn-light btn-xl" href="patient_viewhealthreport00.php">View Health Record</a>
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

