<?php
// Include database connection file
require_once("config.php");

// Fetch navbar data
$stmt = $conn->prepare("SELECT * FROM homepage_navbar WHERE id = 1");
$stmt->execute();
$navbarData = $stmt->get_result()->fetch_assoc();

// Fetch carousel data
$stmt = $conn->prepare("SELECT * FROM homepage_carousel ORDER BY id");
$stmt->execute();
$carouselData = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Fetch about data
$stmt = $conn->prepare("SELECT * FROM homepage_about WHERE id = 1");
$stmt->execute();
$aboutData = $stmt->get_result()->fetch_assoc();

// Fetch service data
$stmt = $conn->prepare("SELECT * FROM homepage_service WHERE id = 1");
$stmt->execute();
$serviceData = $stmt->get_result()->fetch_assoc();

// Fetch team data
$stmt = $conn->prepare("SELECT * FROM homepage_team");
$stmt->execute();
$teamData = $stmt->get_result();

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Finanza - Financial Services Website Template</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Favicon -->
    <link href="homepage-img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@500;600;700&family=Open+Sans:wght@400;500&display=swap" rel="stylesheet">  

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="homepage-lib/animate/animate.min.css" rel="stylesheet">
    <link href="homepage-lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="homepage-css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="homepage-css/style.css" rel="stylesheet">
</head>

<body>
    <!-- Spinner Start -->
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;"></div>
    </div>
    <!-- Spinner End -->

<!-- Navbar Start -->
<div class="container-fluid fixed-top px-0 wow fadeIn" data-wow-delay="0.1s">
    <div class="top-bar row gx-0 align-items-center d-none d-lg-flex">
        <div class="col-lg-6 px-5 text-start">
            <small><i class="fa fa-map-marker-alt text-primary me-2"></i><?php echo $navbarData['address']; ?></small>
            <small class="ms-4"><i class="fa fa-clock text-primary me-2"></i><?php echo $navbarData['hours']; ?></small>
        </div>
        <div class="col-lg-6 px-5 text-end">
            <small><i class="fa fa-envelope text-primary me-2"></i><?php echo $navbarData['email']; ?></small>
            <small class="ms-4"><i class="fa fa-phone-alt text-primary me-2"></i><?php echo $navbarData['phone']; ?></small>
        </div>
    </div>

    <nav class="navbar navbar-expand-lg navbar-light py-lg-0 px-lg-5 wow fadeIn" data-wow-delay="0.1s">
        <a href="index.php" class="navbar-brand ms-4 ms-lg-0">
            <h1 class="display-5 text-primary m-0">Finanza</h1>
        </a>
        <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="navbar-nav ms-auto p-4 p-lg-0">
                <a href="index.php" class="nav-item nav-link active">Home</a>
                <a href="#about" class="nav-item nav-link">About</a>
                <a href="#service" class="nav-item nav-link">Services</a>
                <a href="#contact" class="nav-item nav-link">Contact</a>
                <a href="login.php" class="nav-item nav-link">Log In</a>
            </div>
            <div class="d-none d-lg-flex ms-2">
                <a class="btn btn-light btn-sm-square rounded-circle ms-3" href="<?php echo $navbarData['facebook_link']; ?>">
                    <small class="fab fa-facebook-f text-primary"></small>
                </a>
                <a class="btn btn-light btn-sm-square rounded-circle ms-3" href="<?php echo $navbarData['twitter_link']; ?>">
                    <small class="fab fa-twitter text-primary"></small>
                </a>
                <a class="btn btn-light btn-sm-square rounded-circle ms-3" href="<?php echo $navbarData['linkedin_link']; ?>">
                    <small class="fab fa-linkedin-in text-primary"></small>
                </a>
            </div>
        </div>
    </nav>
</div>
<!-- Navbar End -->

<!-- Carousel Start -->
<div class="container-fluid p-0 mb-5 wow fadeIn" data-wow-delay="0.1s">
    <div id="header-carousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
        <div class="carousel-inner">
            <?php foreach ($carouselData as $index => $item): ?>
            <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                <img class="w-100" src="<?php echo $item['image_url']; ?>" alt="Image">
                <div class="carousel-caption">
                    <div class="container">
                        <div class="row justify-content-start">
                            <div class="col-lg-<?php echo $index % 2 === 0 ? '8' : '7'; ?>">
                                <p class="d-inline-block border border-white rounded text-primary fw-semi-bold py-1 px-3 animated slideInDown"><?php echo $item['caption_text']; ?></p>
                                <h1 class="display-1 mb-4 animated slideInDown"><?php echo $item['caption_title']; ?></h1>
                                <a href="<?php echo $item['button_link']; ?>" class="btn btn-primary py-3 px-5 animated slideInDown"><?php echo $item['button_text']; ?></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#header-carousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#header-carousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
</div>
<!-- Carousel End -->

<!-- About Start -->
<div class="container-xxl py-5" id="about">
    <div class="container">
        <div class="row g-4 align-items-end mb-4">
            <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.1s">
                <img class="img-fluid rounded" src="<?php echo $aboutData['image_url']; ?>">
            </div>
            <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.3s">
                <p class="d-inline-block border rounded text-primary fw-semi-bold py-1 px-3">About Us</p>
                <h1 class="display-5 mb-4"><?php echo $aboutData['about_title']; ?></h1>
                <p class="mb-4"><?php echo $aboutData['about_text']; ?></p>
                <div class="border rounded p-4">
                    <nav>
                        <div class="nav nav-tabs mb-3" id="nav-tab" role="tablist">
                            <button class="nav-link fw-semi-bold active" id="nav-story-tab" data-bs-toggle="tab" data-bs-target="#nav-story" type="button" role="tab" aria-controls="nav-story" aria-selected="true">Story</button>
                            <button class="nav-link fw-semi-bold" id="nav-mission-tab" data-bs-toggle="tab" data-bs-target="#nav-mission" type="button" role="tab" aria-controls="nav-mission" aria-selected="false">Mission</button>
                            <button class="nav-link fw-semi-bold" id="nav-vision-tab" data-bs-toggle="tab" data-bs-target="#nav-vision" type="button" role="tab" aria-controls="nav-vision" aria-selected="false">Vision</button>
                        </div>
                    </nav>
                    <div class="tab-content" id="nav-tabContent">
                        <div class="tab-pane fade show active" id="nav-story" role="tabpanel" aria-labelledby="nav-story-tab">
                            <p><?php echo $aboutData['story_text']; ?></p>
                        </div>
                        <div class="tab-pane fade" id="nav-mission" role="tabpanel" aria-labelledby="nav-mission-tab">
                            <p><?php echo $aboutData['mission_text']; ?></p>
                        </div>
                        <div class="tab-pane fade" id="nav-vision" role="tabpanel" aria-labelledby="nav-vision-tab">
                            <p><?php echo $aboutData['vision_text']; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="border rounded p-4 wow fadeInUp" data-wow-delay="0.1s">
            <div class="row g-4">
                <div class="col-lg-4 wow fadeIn" data-wow-delay="0.1s">
                    <div class="h-100">
                        <div class="d-flex">
                            <div class="flex-shrink-0 btn-lg-square rounded-circle bg-primary">
                                <i class="<?php echo $aboutData['feature1_icon']; ?> text-white"></i>
                            </div>
                            <div class="ps-3">
                                <h4><?php echo $aboutData['feature1_title']; ?></h4>
                                <span><?php echo $aboutData['feature1_text']; ?></span>
                            </div>
                            <div class="border-end d-none d-lg-block"></div>
                        </div>
                        <div class="border-bottom mt-4 d-block d-lg-none"></div>
                    </div>
                </div>
                <div class="col-lg-4 wow fadeIn" data-wow-delay="0.3s">
                    <div class="h-100">
                        <div class="d-flex">
                            <div class="flex-shrink-0 btn-lg-square rounded-circle bg-primary">
                                <i class="<?php echo $aboutData['feature2_icon']; ?> text-white"></i>
                            </div>
                            <div class="ps-3">
                                <h4><?php echo $aboutData['feature2_title']; ?></h4>
                                <span><?php echo $aboutData['feature2_text']; ?></span>
                            </div>
                            <div class="border-end d-none d-lg-block"></div>
                        </div>
                        <div class="border-bottom mt-4 d-block d-lg-none"></div>
                    </div>
                </div>
                <div class="col-lg-4 wow fadeIn" data-wow-delay="0.5s">
                    <div class="h-100">
                        <div class="d-flex">
                            <div class="flex-shrink-0 btn-lg-square rounded-circle bg-primary">
                                <i class="<?php echo $aboutData['feature3_icon']; ?> text-white"></i>
                            </div>
                            <div class="ps-3">
                                <h4><?php echo $aboutData['feature3_title']; ?></h4>
                                <span><?php echo $aboutData['feature3_text']; ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- About End -->

<!-- Service Start -->
<div class="container-xxl service py-5" id="service">
    <div class="container">
        <div class="text-center mx-auto wow fadeInUp" data-wow-delay="0.1s" style="max-width: 600px;">
            <p class="d-inline-block border rounded text-primary fw-semi-bold py-1 px-3">Our Services</p>
            <h1 class="display-5 mb-5"><?php echo $serviceData['main_title']; ?></h1>
        </div>
        <div class="row g-4 wow fadeInUp" data-wow-delay="0.3s">
            <div class="col-lg-4">
                <div class="nav nav-pills d-flex justify-content-between w-100 h-100 me-4">
                    <button class="nav-link w-100 d-flex align-items-center text-start border p-4 mb-4 active" data-bs-toggle="pill" data-bs-target="#tab-pane-1" type="button">
                        <h5 class="m-0"><i class="fa fa-bars text-primary me-3"></i><?php echo $serviceData['service1_title']; ?></h5>
                    </button>
                    <button class="nav-link w-100 d-flex align-items-center text-start border p-4 mb-4" data-bs-toggle="pill" data-bs-target="#tab-pane-2" type="button">
                        <h5 class="m-0"><i class="fa fa-bars text-primary me-3"></i><?php echo $serviceData['service2_title']; ?></h5>
                    </button>
                    <button class="nav-link w-100 d-flex align-items-center text-start border p-4 mb-4" data-bs-toggle="pill" data-bs-target="#tab-pane-3" type="button">
                        <h5 class="m-0"><i class="fa fa-bars text-primary me-3"></i><?php echo $serviceData['service3_title']; ?></h5>
                    </button>
                    <button class="nav-link w-100 d-flex align-items-center text-start border p-4 mb-0" data-bs-toggle="pill" data-bs-target="#tab-pane-4" type="button">
                        <h5 class="m-0"><i class="fa fa-bars text-primary me-3"></i><?php echo $serviceData['service4_title']; ?></h5>
                    </button>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="tab-content w-100">
                    <div class="tab-pane fade show active" id="tab-pane-1">
                        <div class="row g-4">
                            <div class="col-md-6" style="min-height: 350px;">
                                <div class="position-relative h-100">
                                    <img class="position-absolute rounded w-100 h-100" src="<?php echo $serviceData['service1_image']; ?>" style="object-fit: cover;" alt="">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h3 class="mb-4">25 Years Of Experience In Financial Support</h3>
                                <p class="mb-4"><?php echo $serviceData['service1_text']; ?></p>
                                <p><i class="fa fa-check text-primary me-3"></i>Secured Loans</p>
                                <p><i class="fa fa-check text-primary me-3"></i>Credit Facilities</p>
                                <p><i class="fa fa-check text-primary me-3"></i>Cash Advanced</p>
                                <a href="" class="btn btn-primary py-3 px-5 mt-3">Read More</a>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="tab-pane-2">
                        <div class="row g-4">
                            <div class="col-md-6" style="min-height: 350px;">
                                <div class="position-relative h-100">
                                    <img class="position-absolute rounded w-100 h-100" src="<?php echo $serviceData['service2_image']; ?>" style="object-fit: cover;" alt="">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h3 class="mb-4">25 Years Of Experience In Financial Support</h3>
                                <p class="mb-4"><?php echo $serviceData['service2_text']; ?></p>
                                <p><i class="fa fa-check text-primary me-3"></i>Secured Loans</p>
                                <p><i class="fa fa-check text-primary me-3"></i>Credit Facilities</p>
                                <p><i class="fa fa-check text-primary me-3"></i>Cash Advanced</p>
                                <a href="" class="btn btn-primary py-3 px-5 mt-3">Read More</a>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="tab-pane-3">
                        <div class="row g-4">
                            <div class="col-md-6" style="min-height: 350px;">
                                <div class="position-relative h-100">
                                    <img class="position-absolute rounded w-100 h-100" src="<?php echo $serviceData['service3_image']; ?>" style="object-fit: cover;" alt="">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h3 class="mb-4">25 Years Of Experience In Financial Support</h3>
                                <p class="mb-4"><?php echo $serviceData['service3_text']; ?></p>
                                <p><i class="fa fa-check text-primary me-3"></i>Secured Loans</p>
                                <p><i class="fa fa-check text-primary me-3"></i>Credit Facilities</p>
                                <p><i class="fa fa-check text-primary me-3"></i>Cash Advanced</p>
                                <a href="" class="btn btn-primary py-3 px-5 mt-3">Read More</a>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="tab-pane-4">
                        <div class="row g-4">
                            <div class="col-md-6" style="min-height: 350px;">
                                <div class="position-relative h-100">
                                    <img class="position-absolute rounded w-100 h-100" src="<?php echo $serviceData['service4_image']; ?>" style="object-fit: cover;" alt="">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h3 class="mb-4">25 Years Of Experience In Financial Support</h3>
                                <p class="mb-4"><?php echo $serviceData['service4_text']; ?></p>
                                <p><i class="fa fa-check text-primary me-3"></i>Secured Loans</p>
                                <p><i class="fa fa-check text-primary me-3"></i>Credit Facilities</p>
                                <p><i class="fa fa-check text-primary me-3"></i>Cash Advanced</p>
                                <a href="" class="btn btn-primary py-3 px-5 mt-3">Read More</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Service End -->

    <!-- Callback Start -->
    <div class="container-fluid callback my-5 pt-5" id="contact-form">
        <div class="container pt-5">
            <div class="row justify-content-center">
                <div class="col-lg-7">
                    <div class="bg-white border rounded p-4 p-sm-5 wow fadeInUp" data-wow-delay="0.5s">
                        <div class="text-center mx-auto wow fadeInUp" data-wow-delay="0.1s" style="max-width: 600px;">
                            <p class="d-inline-block border rounded text-primary fw-semi-bold py-1 px-3">Get In Touch</p>
                            <h1 class="display-5 mb-5">Request A Call-Back</h1>
                        </div>
                        <form class="row g-3" id="contact-form" action="contact-submit.php" method="post">
                            <div class="col-sm-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" name="name" id="name" placeholder="Your Name">
                                    <label for="name">Your Name</label>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-floating">
                                    <input type="email" class="form-control" name="email" id="email" placeholder="Your Email">
                                    <label for="mail">Your Email</label>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" name="mobile" id="mobile" placeholder="Your Mobile">
                                    <label for="mobile">Your Mobile</label>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" name="subject" id="subject" placeholder="Subject">
                                    <label for="subject">Subject</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating">
                                    <textarea class="form-control" name="message" placeholder="Leave a message here" id="message" style="height: 100px"></textarea>
                                    <label for="message">Message</label>
                                </div>
                            </div>
                            <div class="col-12 text-center">
                                <button class="btn btn-primary w-100 py-3" type="submit">Submit Now</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Callback End -->

<!-- Team Start -->
<div class="container-xxl py-5" id="team">
    <div class="container">
        <div class="text-center mx-auto wow fadeInUp" data-wow-delay="0.1s" style="max-width: 600px;">
            <p class="d-inline-block border rounded text-primary fw-semi-bold py-1 px-3">Our Team</p>
            <h1 class="display-5 mb-5">Exclusive Team</h1>
        </div>
        <div class="row g-4">
            <?php while ($team = $teamData->fetch_assoc()) { ?>
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="team-item">
                        <img class="img-fluid rounded" src="<?php echo $team['image']; ?>" alt="">
                        <div class="team-text">
                            <h4 class="mb-0"><?php echo $team['name']; ?></h4>
                            <div class="team-social d-flex">
                                <?php if ($team['facebook']) { ?>
                                    <a class="btn btn-square rounded-circle mx-1" href="<?php echo $team['facebook']; ?>"><i class="fab fa-facebook-f"></i></a>
                                <?php } ?>
                                <?php if ($team['twitter']) { ?>
                                    <a class="btn btn-square rounded-circle mx-1" href="<?php echo $team['twitter']; ?>"><i class="fab fa-twitter"></i></a>
                                <?php } ?>
                                <?php if ($team['instagram']) { ?>
                                    <a class="btn btn-square rounded-circle mx-1" href="<?php echo $team['instagram']; ?>"><i class="fab fa-instagram"></i></a>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<!-- Team End -->

<!-- Footer Start -->
<div class="container-fluid bg-dark text-light footer mt-5 py-5 wow fadeIn" data-wow-delay="0.1s">
    <div class="container py-5">
        <div class="row g-5">
            <div class="col-lg-4 col-md-6">
                <h4 class="text-white mb-4">Our Office</h4>
                <p class="mb-2"><i class="fa fa-map-marker-alt me-3"></i><?php echo $footerData['address']; ?></p>
                <p class="mb-2"><i class="fa fa-phone-alt me-3"></i><?php echo $footerData['phone']; ?></p>
                <p class="mb-2"><i class="fa fa-envelope me-3"></i><?php echo $footerData['email']; ?></p>
                <div class="d-flex pt-2">
                    <a class="btn btn-square btn-outline-light rounded-circle me-2" href="<?php echo $footerData['twitter']; ?>"><i class="fab fa-twitter"></i></a>
                    <a class="btn btn-square btn-outline-light rounded-circle me-2" href="<?php echo $footerData['facebook']; ?>"><i class="fab fa-facebook-f"></i></a>
                    <a class="btn btn-square btn-outline-light rounded-circle me-2" href="<?php echo $footerData['youtube']; ?>"><i class="fab fa-youtube"></i></a>
                    <a class="btn btn-square btn-outline-light rounded-circle me-2" href="<?php echo $footerData['linkedin']; ?>"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <h4 class="text-white mb-4">Services</h4>
                <!-- Populate services dynamically from the database -->
                <?php
                $services = explode(",", $footerData['services']);
                foreach ($services as $service) {
                    echo '<a class="btn btn-link" href="">' . $service . '</a>';
                }
                ?>
            </div>
            <div class="col-lg-4 col-md-6">
                <h4 class="text-white mb-4">Quick Links</h4>
                <!-- Populate quick links dynamically from the database -->
                <?php
                $quickLinks = explode(",", $footerData['quick_links']);
                foreach ($quickLinks as $link) {
                    echo '<a class="btn btn-link" href="">' . $link . '</a>';
                }
                ?>
            </div>
        </div>
    </div>
</div>
<!-- Footer End -->

    <!-- Copyright Start -->
    <div class="container-fluid copyright py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    &copy; <a class="border-bottom" href="#">Your Site Name</a>, All Right Reserved.
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <!--/*** This template is free as long as you keep the footer author’s credit link/attribution link/backlink. If you'd like to use the template without the footer author’s credit link/attribution link/backlink, you can purchase the Credit Removal License from "https://htmlcodex.com/credit-removal". Thank you for your support. ***/-->
                    Designed By <a class="border-bottom" href="https://datalytika.net">Datalytika</a>
                </div>
            </div>
        </div>
    </div>
    <!-- Copyright End -->


    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square rounded-circle back-to-top"><i class="bi bi-arrow-up"></i></a>


    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="homepage-lib/wow/wow.min.js"></script>
    <script src="homepage-lib/easing/easing.min.js"></script>
    <script src="homepage-lib/waypoints/waypoints.min.js"></script>
    <script src="homepage-lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="homepage-lib/counterup/counterup.min.js"></script>

    <!-- Template Javascript -->
    <script src="homepage-js/main.js"></script>
    <script src="homepage-js/response-handler.js"></script>
</body>

</html>