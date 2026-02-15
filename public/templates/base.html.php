<?php
// Force login check - redirect to login if not authenticated
use App\Modules\Usuarios\Helpers\SecurityHelper;

if (!SecurityHelper::isAuthenticated()) {
    header('Location: /SGA-SEBANA/public/login');
    exit;
}

$authUser = SecurityHelper::getAuthUser();
?>
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
    <title><?= $title ?? 'SGA-SEBANA' ?></title>

    <!-- Fontfaces CSS-->
    <link href="/SGA-SEBANA/public/assets/css/font-face.css" rel="stylesheet" media="all">
    <link href="/SGA-SEBANA/public/assets/vendor/fontawesome-7.1.0/css/all.min.css" rel="stylesheet" media="all">
    <link href="/SGA-SEBANA/public/assets/vendor/mdi-font/css/material-design-iconic-font.min.css" rel="stylesheet"
        media="all">

    <!-- Bootstrap CSS-->
    <link href="/SGA-SEBANA/public/assets/vendor/bootstrap-5.3.8.min.css" rel="stylesheet" media="all">

    <!-- Vendor CSS-->
    <link href="/SGA-SEBANA/public/assets/css/aos.css" rel="stylesheet" media="all">
    <link href="/SGA-SEBANA/public/assets/vendor/css-hamburgers/hamburgers.min.css" rel="stylesheet" media="all">
    <link href="/SGA-SEBANA/public/assets/css/swiper-bundle-12.0.3.min.css" rel="stylesheet" media="all">
    <link href="/SGA-SEBANA/public/assets/vendor/perfect-scrollbar/perfect-scrollbar-1.5.6.css" rel="stylesheet"
        media="all">

    <!-- Main CSS-->
    <link href="/SGA-SEBANA/public/assets/css/theme.css" rel="stylesheet" media="all">
    <link href="/SGA-SEBANA/public/assets/css/custom-fixes.css?v=2" rel="stylesheet" media="all">

</head>

<body>
    <div class="page-wrapper">
        <!-- HEADER MOBILE-->
        <header class="header-mobile d-block d-lg-none">
            <div class="header-mobile__bar">
                <div class="container-fluid">
                    <div class="header-mobile-inner">
                        <a class="logo" href="/SGA-SEBANA/public/">
                            <img src="/SGA-SEBANA/public/assets/img/icon/sebana_logo-removebg.png" alt="SGA-SEBANA" />
                        </a>
                        <button class="hamburger hamburger--slider" type="button">
                            <span class="hamburger-box">
                                <span class="hamburger-inner"></span>
                            </span>
                        </button>
                    </div>
                </div>
            </div>
            <nav class="navbar-mobile">
                <div class="container-fluid">
                    <ul class="navbar-mobile__list list-unstyled">
                        <li>
                            <a href="/SGA-SEBANA/public/home">
                                <i class="fas fa-tachometer-alt"></i>Panel</a>
                        </li>
                        <li>
                            <a href="/SGA-SEBANA/public/afiliados">
                                <i class="fas fa-users"></i>Afiliados</a>
                        </li>
                    </ul>
                </div>
            </nav>

        </header>
        <!-- END HEADER MOBILE-->

        <!-- MENU SIDEBAR-->
        <aside class="menu-sidebar d-none d-lg-block">
            <div class="logo">
                <a href="/SGA-SEBANA/public/">
                    <img src="/SGA-SEBANA/public/assets/img/icon/sebana_logo-removebg.png" alt="SGA-SEBANA" />
                </a>
            </div>
            <div class="menu-sidebar__content js-scrollbar1">
                <nav class="navbar-sidebar">
                    <ul class="list-unstyled navbar__list">
                        <li class="active">
                            <a href="/SGA-SEBANA/public/home">
                                <i class="fas fa-tachometer-alt"></i>Panel</a>
                        </li>
                        <li>
                            <a href="/SGA-SEBANA/public/afiliados">
                                <i class="fas fa-users"></i>Afiliados</a>
                        </li>
                        <li class="has-sub">
                            <a class="js-arrow" href="#">
                                <i class="fas fa-user-shield"></i>Usuarios</a>
                            <ul class="list-unstyled navbar__sub-list js-sub-list">
                                <li>
                                    <a href="/SGA-SEBANA/public/users">Lista de Usuarios</a>
                                </li>
                                <li>
                                    <a href="/SGA-SEBANA/public/users/create">Nuevo Usuario</a>
                                </li>
                                <li>
                                    <a href="/SGA-SEBANA/public/bitacora">Bitácora</a>
                                </li>
                                
                            </ul>
                        </li>
                        <li>
                            <a href="/SGA-SEBANA/public/junta">
                                <i class="fas fa-users"></i>Junta Directiva</a>
                        </li>
                        <li>
                           <a href="/SGA-SEBANA/public/ReporteDeExclusionDeAfiliado">
                           <i class="fas fa-user-times"></i> Exclusiones
                           </a>
                           </li>
                        <li>
                            <a href="/SGA-SEBANA/public/ui/chart">
                                <i class="fas fa-chart-bar"></i>Charts</a>
                        </li>
                        <li>
                            <a href="/SGA-SEBANA/public/ui/table">
                                <i class="fas fa-table"></i>Tables</a>
                        </li>
                        <li>
                            <a href="/SGA-SEBANA/public/ui/form">
                                <i class="far fa-check-square"></i>Forms</a>
                        </li>
                        <li>
                            <a href="/SGA-SEBANA/public/ui/calendar">
                                <i class="fas fa-calendar-alt"></i>Calendar</a>
                        </li>
                        <li>
                            <a href="/SGA-SEBANA/public/ui/map">
                                <i class="fas fa-map-marker-alt"></i>Maps</a>
                        </li>
                        <li class="has-sub">
                            <a class="js-arrow" href="#">
                                <i class="fas fa-desktop"></i>UI Elements</a>
                            <ul class="list-unstyled navbar__sub-list js-sub-list">
                                <li>
                                    <a href="/SGA-SEBANA/public/ui/button">Button</a>
                                </li>
                                <li>
                                    <a href="/SGA-SEBANA/public/ui/badge">Badges</a>
                                </li>
                                <li>
                                    <a href="/SGA-SEBANA/public/ui/tab">Tabs</a>
                                </li>
                                <li>
                                    <a href="/SGA-SEBANA/public/ui/card">Cards</a>
                                </li>
                                <li>
                                    <a href="/SGA-SEBANA/public/ui/alert">Alerts</a>
                                </li>
                                <li>
                                    <a href="/SGA-SEBANA/public/ui/progress-bar">Progress Bars</a>
                                </li>
                                <li>
                                    <a href="/SGA-SEBANA/public/ui/modal">Modals</a>
                                </li>
                                <li>
                                    <a href="/SGA-SEBANA/public/ui/switch">Switchs</a>
                                </li>
                                <li>
                                    <a href="/SGA-SEBANA/public/ui/grid">Grids</a>
                                </li>
                                <li>
                                    <a href="/SGA-SEBANA/public/ui/fontawesome">Fontawesome Icon</a>
                                </li>
                                <li>
                                    <a href="/SGA-SEBANA/public/ui/typo">Typography</a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>
        <!-- END MENU SIDEBAR-->

        <!-- PAGE CONTAINER-->
        <div class="page-container">
            <!-- HEADER DESKTOP-->
            <header class="header-desktop">
                <div class="section__content section__content--p30">
                    <div class="container-fluid">
                        <div class="header-wrap">
                            <div class="header-button">
                                <div class="account-wrap">
                                    <div class="account-item clearfix js-item-menu">
                                        <div class="image"
                                            style="display: flex; align-items: center; justify-content: center; width: 36px; height: 36px; background: #3b5998; border-radius: 50%;">
                                            <i class="fas fa-user" style="color: white; font-size: 18px;"></i>
                                        </div>
                                        <div class="content">
                                            <a class="js-acc-btn"
                                                href="#"><?= htmlspecialchars($authUser['username'] ?? 'Usuario') ?></a>
                                        </div>
                                        <div class="account-dropdown js-dropdown">
                                            <div class="info clearfix">
                                                <div class="image"
                                                    style="display: flex; align-items: center; justify-content: center; width: 50px; height: 50px; background: #3b5998; border-radius: 50%;">
                                                    <i class="fas fa-user" style="color: white; font-size: 24px;"></i>
                                                </div>
                                                <div class="content">
                                                    <h5 class="name">
                                                        <a
                                                            href="#"><?= htmlspecialchars($authUser['nombre_completo'] ?? 'Usuario') ?></a>
                                                    </h5>
                                                    <span
                                                        class="email"><?= htmlspecialchars($authUser['email'] ?? '') ?></span>
                                                </div>
                                            </div>
                                            <div class="account-dropdown__body">
                                                <div class="account-dropdown__item">
                                                    <a href="/SGA-SEBANA/public/users/<?= $authUser['id'] ?? 0 ?>/edit">
                                                        <i class="zmdi zmdi-account"></i>Mi Cuenta</a>
                                                </div>
                                                <div class="account-dropdown__item">
                                                    <span class="text-muted px-3">
                                                        <i
                                                            class="zmdi zmdi-shield-check"></i><?= htmlspecialchars($authUser['rol_nombre'] ?? 'Sin rol') ?></span>
                                                </div>
                                            </div>
                                            <div class="account-dropdown__footer">
                                                <a href="/SGA-SEBANA/public/logout">
                                                    <i class="zmdi zmdi-power"></i>Cerrar Sesión</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            <!-- END HEADER DESKTOP-->

            <!-- MAIN CONTENT-->
            <div class="main-content">
                <div class="section__content section__content--p30">
                    <div class="container-fluid">
                        <?= $content ?? '' ?>
                    </div>
                </div>
            </div>
            <!-- END MAIN CONTENT-->

        </div>
    </div>

    <!-- Jquery JS-->
    <script src="/SGA-SEBANA/public/assets/js/vanilla-utils.js"></script>

    <!-- Bootstrap JS-->
    <script src="/SGA-SEBANA/public/assets/vendor/bootstrap-5.3.8.bundle.min.js"></script>

    <!-- Vendor JS -->
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