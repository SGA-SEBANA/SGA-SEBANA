<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags-->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="SGA-SEBANA">
    <meta name="author" content="SGA-SEBANA Team">
    <meta name="keywords" content="sga sebana">

    <!-- Title Page-->
    <title><?= $title ?? 'Auth' ?></title>

    <!-- Fontfaces CSS-->
    <link href="/SGA-SEBANA/public/assets/css/font-face.css" rel="stylesheet" media="all">
    <link href="/SGA-SEBANA/public/assets/vendor/fontawesome-7.1.0/css/all.min.css" rel="stylesheet" media="all">
    <link href="/SGA-SEBANA/public/assets/vendor/mdi-font/css/material-design-iconic-font.min.css" rel="stylesheet" media="all">

    <!-- Bootstrap CSS-->
    <link href="/SGA-SEBANA/public/assets/vendor/bootstrap-5.3.8.min.css" rel="stylesheet" media="all">

    <!-- Vendor CSS-->
    <link href="/SGA-SEBANA/public/assets/css/aos.css" rel="stylesheet" media="all">
    <link href="/SGA-SEBANA/public/assets/vendor/css-hamburgers/hamburgers.min.css" rel="stylesheet" media="all">
    <link href="/SGA-SEBANA/public/assets/css/swiper-bundle-12.0.3.min.css" rel="stylesheet" media="all">
    <link href="/SGA-SEBANA/public/assets/vendor/perfect-scrollbar/perfect-scrollbar-1.5.6.css" rel="stylesheet" media="all">

    <!-- Main CSS-->
    <link href="/SGA-SEBANA/public/assets/css/theme.css" rel="stylesheet" media="all">
    <style>
        /* Permite formularios largos (como afiliacion) sin recorte vertical */
        .page-wrapper {
            overflow-y: auto !important;
            overflow-x: hidden !important;
        }
        .page-content--bge5 {
            min-height: 100vh;
            height: auto !important;
            padding: 24px 0;
        }
        .login-wrap {
            padding-top: 2vh;
            padding-bottom: 2vh;
        }
    </style>

</head>

<body class="animsition">
    <div class="page-wrapper">
        <div class="page-content--bge5">
            <div class="container">
                <div class="login-wrap">
                    <div class="login-content">
                        <?= $content ?? '' ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Jquery JS-->
    <script src="/SGA-SEBANA/public/assets/js/vanilla-utils.js"></script>

    <!-- Bootstrap JS-->
    <script src="/SGA-SEBANA/public/assets/vendor/bootstrap-5.3.8.bundle.min.js"></script>
    
    <!-- Vendor JS       -->
    <script src="/SGA-SEBANA/public/assets/vendor/perfect-scrollbar/perfect-scrollbar-1.5.6.min.js"></script>
    <script src="/SGA-SEBANA/public/assets/vendor/chartjs/chart.umd.js-4.5.1.min.js"></script>

    <!-- Main JS-->
    <script src="/SGA-SEBANA/public/assets/js/bootstrap5-init.js"></script>
    <script src="/SGA-SEBANA/public/assets/js/main.js"></script>
    <script src="/SGA-SEBANA/public/assets/js/swiper-bundle-12.0.3.min.js"></script>
    <script src="/SGA-SEBANA/public/assets/js/aos.js"></script>
    <script src="/SGA-SEBANA/public/assets/js/modern-plugins.js"></script>

</body>

</html>
