<?php

// Force login check - redirect to login if not authenticated
use App\Modules\Usuarios\Helpers\SecurityHelper;
use App\Modules\Usuarios\Helpers\AccessControl;

if (!SecurityHelper::isAuthenticated()) {
    header('Location: /SGA-SEBANA/public/login');
    exit;
}

$authUser = SecurityHelper::getAuthUser();

use App\Modules\Visitas\Models\Notification;

$notiModel = new Notification();

$userId = $authUser['id'] ?? null;

$notificaciones = [];
$totalNoLeidas = 0;

if ($userId) {
    $notificaciones = $notiModel->getUnreadByUser($userId);
    $totalNoLeidas = $notiModel->countUnread($userId);
}

$accessRank = AccessControl::levelRank($authUser['nivel_acceso'] ?? 'basico');
$can = static function (string $required) use ($accessRank): bool {
    return $accessRank >= AccessControl::levelRank($required);
};

$roleKey = AccessControl::currentRoleKey();
$isAffiliateRole = AccessControl::isAffiliateRole();
$isEmployeeRole = AccessControl::isEmployeeRole();
$isAuditorRole = ($roleKey === 'auditor');
$canAdminUsers = $can('total');
$canAdminRrll = in_array($roleKey, ['admin_general', 'admin_rrll'], true);
$canAdminSolicitudes = in_array($roleKey, ['admin_general', 'admin_solicitudes'], true);
$canManageVacaciones = in_array($roleKey, ['admin_general', 'admin_solicitudes'], true);
$canHighAccess = $can('alto') && !$isAuditorRole;
$canOperational = $can('medio') && !$isAuditorRole;
$canReports = $can('medio');
$isAdminPanelRole = in_array($roleKey, ['admin_general', 'admin_rrll', 'admin_solicitudes'], true);

$panelUrl = AccessControl::defaultPanelPath();
$displayName = trim((string) ($authUser['nombre_completo'] ?? ''));
if ($displayName === '') {
    $displayName = (string) ($authUser['username'] ?? 'Usuario');
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="SGA-SEBANA">
    <meta name="author" content="SGA-SEBANA Team">
    <meta name="keywords" content="sga sebana">

    <title><?= $title ?? 'SGA-SEBANA' ?></title>

    <link href="/SGA-SEBANA/public/assets/css/font-face.css" rel="stylesheet" media="all">
    <link href="/SGA-SEBANA/public/assets/vendor/fontawesome-7.1.0/css/all.min.css" rel="stylesheet" media="all">
    <link href="/SGA-SEBANA/public/assets/vendor/mdi-font/css/material-design-iconic-font.min.css" rel="stylesheet"
        media="all">

    <link href="/SGA-SEBANA/public/assets/vendor/bootstrap-5.3.8.min.css" rel="stylesheet" media="all">

    <link href="/SGA-SEBANA/public/assets/css/aos.css" rel="stylesheet" media="all">
    <link href="/SGA-SEBANA/public/assets/vendor/css-hamburgers/hamburgers.min.css" rel="stylesheet" media="all">
    <link href="/SGA-SEBANA/public/assets/css/swiper-bundle-12.0.3.min.css" rel="stylesheet" media="all">
    <link href="/SGA-SEBANA/public/assets/vendor/perfect-scrollbar/perfect-scrollbar-1.5.6.css" rel="stylesheet"
        media="all">

    <link href="/SGA-SEBANA/public/assets/css/theme.css" rel="stylesheet" media="all">

    <link rel="shortcut icon" href="/SGA-SEBANA/public/assets/img/favicon/icono-SEBANA.png" type="image/x-icon">

    <link rel="stylesheet" href="/SGA-SEBANA/public/assets/css/calendar.css">

    <style>
        .sga-pagination-wrapper .pagination {
            gap: 0.4rem;
            flex-wrap: wrap;
        }

        .sga-pagination-wrapper .page-link {
            min-width: 38px;
            text-align: center;
            border-radius: 10px;
            border: 1px solid #d9e3ef;
            background: #fff;
            color: #20437c;
            font-weight: 600;
            padding: 0.45rem 0.7rem;
        }

        .sga-pagination-wrapper .page-link:hover {
            background: #eef4ff;
            color: #173560;
            border-color: #b8cdec;
        }

        .sga-pagination-wrapper .page-item.active .page-link {
            background: #1c4388;
            border-color: #1c4388;
            color: #fff;
            box-shadow: 0 8px 16px rgba(28, 67, 136, 0.2);
        }

        .sga-pagination-wrapper .page-item.disabled .page-link {
            color: #8f9ba8;
            background: #f6f8fb;
            border-color: #e6ebf2;
            pointer-events: none;
        }

        .sga-pagination-meta {
            font-size: 0.8rem;
            color: #6d7d8f;
        }
    </style>

</head>

<body>
    <div class="page-wrapper">
        <header class="header-mobile d-block d-lg-none">
            <div class="header-mobile__bar">
                <div class="container-fluid">
                    <div class="header-mobile-inner">
                        <a class="logo" href="<?= $panelUrl ?>">
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
                            <a href="<?= $panelUrl ?>">
                                <i class="fas fa-tachometer-alt"></i><?= $isAdminPanelRole ? 'Panel' : ($isAffiliateRole ? 'Mis Solicitudes' : ($isEmployeeRole ? 'Mis Vacaciones' : 'Inicio')) ?></a>
                        </li>
                        <?php if ($canOperational): ?>
                            <li>
                                <a href="/SGA-SEBANA/public/afiliados">
                                    <i class="fas fa-users"></i>Afiliados</a>
                            </li>
                            <li>
                                <a href="/SGA-SEBANA/public/puestos">
                                    <i class="fas fa-briefcase"></i>Puestos</a>
                            </li>
                        <?php endif; ?>
                        <?php if ($isAffiliateRole): ?>
                            <li>
                                <a href="/SGA-SEBANA/public/ayudas">
                                    <i class="fa-solid fa-hand-holding-dollar"></i>Ayudas</a>
                            </li>
                            <li>
                                <a href="/SGA-SEBANA/public/viaticos">
                                    <i class="fa-solid fa-receipt"></i>Viaticos</a>
                            </li>
                            <li>
                                <a href="/SGA-SEBANA/public/vacaciones">
                                    <i class="fa-solid fa-umbrella-beach"></i>Vacaciones</a>
                            </li>
                            <li>
                                <a href="/SGA-SEBANA/public/visit-requests">
                                    <i class="fa-solid fa-building-user"></i>Visitas</a>
                            </li>
                            <li>
                                <a href="/SGA-SEBANA/public/users/<?= (int) ($authUser['id'] ?? 0) ?>/edit">
                                    <i class="fas fa-user-cog"></i>Mi Usuario</a>
                            </li>
                        <?php endif; ?>
                        <?php if ($isEmployeeRole): ?>
                            <li>
                                <a href="/SGA-SEBANA/public/vacaciones">
                                    <i class="fa-solid fa-umbrella-beach"></i>Vacaciones</a>
                            </li>
                            <li>
                                <a href="/SGA-SEBANA/public/users/<?= (int) ($authUser['id'] ?? 0) ?>/edit">
                                    <i class="fas fa-user-cog"></i>Mi Usuario</a>
                            </li>
                        <?php endif; ?>
                        <?php if ($canHighAccess): ?>
                            <li>
                                <a href="/SGA-SEBANA/public/ayudas">
                                    <i class="fa-solid fa-hand-holding-dollar"></i>Solicitudes Ayudas</a>
                            </li>
                            <li>
                                <a href="/SGA-SEBANA/public/viaticos">
                                    <i class="fa-solid fa-receipt"></i>Solicitudes Viaticos</a>
                            </li>
                            <li>
                                <a href="/SGA-SEBANA/public/visit-requests">
                                    <i class="fa-solid fa-building-user"></i>Solicitudes Visitas</a>
                            </li>
                        <?php endif; ?>
                        <?php if ($canManageVacaciones): ?>
                            <li>
                                <a href="/SGA-SEBANA/public/vacaciones">
                                    <i class="fa-solid fa-umbrella-beach"></i>Solicitudes Vacaciones</a>
                            </li>
                        <?php endif; ?>
                        <?php if ($canAdminSolicitudes): ?>
                            <li>
                                <a href="/SGA-SEBANA/public/admin/visit-requests">
                                    <i class="fa-solid fa-envelopes-bulk"></i>Admin Visitas</a>
                            </li>
                            <li>
                                <a href="/SGA-SEBANA/public/asistente-afiliacion/solicitudes">
                                    <i class="fa-solid fa-file-signature"></i>Afiliacion</a>
                            </li>
                        <?php endif; ?>
                        <?php if ($canAdminRrll): ?>
                            <li>
                                <a href="/SGA-SEBANA/public/casos-rrll">
                                    <i class="fa-solid fa-people-group"></i>Casos RRLL</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </nav>

        </header>
        <aside class="menu-sidebar d-none d-lg-block">
            <div class="logo">
                <a href="<?= $panelUrl ?>">
                    <img src="/SGA-SEBANA/public/assets/img/icon/sebana_logo-removebg.png" alt="SGA-SEBANA" />
                </a>
            </div>
            <div class="menu-sidebar__content js-scrollbar1">
                <nav class="navbar-sidebar">
                    <ul class="list-unstyled navbar__list">
                        <li class="active">
                            <a href="<?= $panelUrl ?>">
                                <i class="fas fa-tachometer-alt"></i><?= $isAdminPanelRole ? 'Panel de Control' : ($isAffiliateRole ? 'Mis Solicitudes' : ($isEmployeeRole ? 'Mis Vacaciones' : 'Inicio')) ?></a>
                        </li>

                        <?php if ($canOperational): ?>
                            <li class="has-sub">
                                <a class="js-arrow" href="#">
                                    <i class="fas fa-users"></i>Gestion de Afiliados</a>
                                <ul class="list-unstyled navbar__sub-list js-sub-list">
                                    <li><a href="/SGA-SEBANA/public/afiliados"><i class="fas fa-user-friends"></i> Lista de Afiliados</a></li>
                                    <li><a href="/SGA-SEBANA/public/ReporteDeExclusionDeAfiliado"><i class="fas fa-user-times"></i> Exclusiones</a></li>
                                    <li><a href="/SGA-SEBANA/public/Categorias"><i class="fas fa-tags"></i> Categorias</a></li>
                                    <li><a href="/SGA-SEBANA/public/puestos"><i class="fas fa-briefcase"></i> Puestos</a></li>
                                </ul>
                            </li>

                            <li class="has-sub">
                                <a class="js-arrow" href="#">
                                    <i class="fas fa-sitemap"></i>Estructura Organica</a>
                                <ul class="list-unstyled navbar__sub-list js-sub-list">
                                    <li><a href="/SGA-SEBANA/public/junta"><i class="fas fa-user-tie"></i> Junta Directiva</a></li>
                                </ul>
                            </li>
                        <?php endif; ?>

                        <?php if ($isAffiliateRole || $isEmployeeRole || $canAdminSolicitudes || $canAdminRrll || $canOperational || $canHighAccess): ?>
                            <li class="has-sub">
                                <a class="js-arrow" href="#">
                                    <i class="fas fa-file-invoice-dollar"></i>Tramites y Solicitudes</a>
                                <ul class="list-unstyled navbar__sub-list js-sub-list">
                                    <?php if ($isAffiliateRole): ?>
                                        <li><a href="/SGA-SEBANA/public/visit-requests"><i class="fa-solid fa-building-user"></i> Solicitar Visita</a></li>
                                        <li><a href="/SGA-SEBANA/public/ayudas"><i class="fa-solid fa-hand-holding-dollar"></i> Ayudas</a></li>
                                        <li><a href="/SGA-SEBANA/public/viaticos"><i class="fa-solid fa-receipt"></i> Viaticos</a></li>
                                        <li><a href="/SGA-SEBANA/public/vacaciones"><i class="fa-solid fa-umbrella-beach"></i> Vacaciones</a></li>
                                    <?php endif; ?>

                                    <?php if ($isEmployeeRole): ?>
                                        <li><a href="/SGA-SEBANA/public/vacaciones"><i class="fa-solid fa-umbrella-beach"></i> Vacaciones</a></li>
                                    <?php endif; ?>

                                    <?php if ($canHighAccess): ?>
                                        <li><a href="/SGA-SEBANA/public/visit-requests"><i class="fa-solid fa-building-user"></i> Visitas</a></li>
                                        <li><a href="/SGA-SEBANA/public/ayudas"><i class="fa-solid fa-hand-holding-dollar"></i> Ayudas</a></li>
                                        <li><a href="/SGA-SEBANA/public/viaticos"><i class="fa-solid fa-receipt"></i> Viaticos</a></li>
                                    <?php endif; ?>

                                    <?php if ($canManageVacaciones): ?>
                                        <li><a href="/SGA-SEBANA/public/vacaciones"><i class="fa-solid fa-umbrella-beach"></i> Vacaciones</a></li>
                                    <?php endif; ?>

                                    <?php if ($canAdminSolicitudes): ?>
                                        <li><a href="/SGA-SEBANA/public/admin/visit-requests"><i class="fa-solid fa-envelopes-bulk"></i> Admin. Visitas</a></li>
                                        <li><a href="/SGA-SEBANA/public/asistente-afiliacion/solicitudes"><i class="fa-solid fa-file-signature"></i> Afiliacion SEBANA</a></li>
                                    <?php endif; ?>

                                    <?php if ($canAdminRrll): ?>
                                        <li><a href="/SGA-SEBANA/public/casos-rrll"><i class="fa-solid fa-people-group"></i> Casos RRLL</a></li>
                                    <?php endif; ?>

                                    <?php if ($canOperational): ?>
                                        <li><a href="/SGA-SEBANA/public/oficinas"><i class="fa-solid fa-building"></i> Oficinas</a></li>
                                    <?php endif; ?>
                                </ul>
                            </li>
                        <?php endif; ?>

                        <?php if ($canReports || $canAdminUsers || $isAffiliateRole || $isEmployeeRole): ?>
                            <li class="has-sub">
                                <a class="js-arrow" href="#">
                                    <i class="fas fa-cogs"></i>Configuracion</a>
                                <ul class="list-unstyled navbar__sub-list js-sub-list">
                                    <?php if ($isAffiliateRole || $isEmployeeRole): ?>
                                        <li><a href="/SGA-SEBANA/public/users/<?= (int) ($authUser['id'] ?? 0) ?>/edit"><i class="fas fa-user-cog"></i> Mi Usuario</a></li>
                                    <?php endif; ?>
                                    <?php if ($canAdminUsers): ?>
                                        <li><a href="/SGA-SEBANA/public/users"><i class="fas fa-user-shield"></i> Usuarios</a></li>
                                    <?php endif; ?>
                                    <?php if ($canReports): ?>
                                        <li><a href="/SGA-SEBANA/public/bitacora"><i class="fas fa-history"></i> Bitacora de Sistema</a></li>
                                    <?php endif; ?>
                                </ul>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </aside>
        <div class="page-container">
            <header class="header-desktop">
                <div class="section__content section__content--p30">
                    <div class="container-fluid">
                        <div class="header-wrap">

                            <div class="header-noti js-item-menu me-4 position-relative">
                                <i class="fa fa-bell fs-5"></i>

                                <?php if ($totalNoLeidas > 0): ?>
                                <span class="quantity"><?= $totalNoLeidas ?></span>
                                <?php endif; ?>

                                <div class="notifi-dropdown js-dropdown">
                                    <?php if (empty($notificaciones)): ?>
                                    <div class="notifi__title p-3">
                                        <p>No hay notificaciones</p>
                                    </div>
                                    <?php else: ?>
                                    <div class="notifi__title p-3" style="display: flex; justify-content: space-between; align-items: center;">
                                        <p style="margin: 0;">Tienes <?= $totalNoLeidas ?> notificaciones</p>
                                        <form action="/SGA-SEBANA/public/notificaciones/read-all" method="post" style="margin: 0;">
                                            <?= \App\Modules\Usuarios\Helpers\SecurityHelper::csrfField() ?>
                                            <button type="submit" style="font-size: 12px; color: #007bff; border:none; background:none; padding:0;">
                                                Marcar todas
                                            </button>
                                        </form>
                                    </div>

                                    <?php foreach ($notificaciones as $n): ?>
                                    <div class="notifi__item" style="display: flex; justify-content: space-between; align-items: center;">
                                        <div class="content" style="width: 100%;">
                                            <form action="/SGA-SEBANA/public/notificaciones/read/<?= (int) ($n['id'] ?? 0) ?>" method="post" style="margin:0;">
                                                <?= \App\Modules\Usuarios\Helpers\SecurityHelper::csrfField() ?>
                                                <button type="submit" style="border:none;background:none;padding:0;text-align:left;width:100%;">
                                                    <p><strong><?= htmlspecialchars($n['titulo']) ?></strong></p>
                                                    <span><?= htmlspecialchars($n['mensaje']) ?></span>
                                                </button>
                                            </form>
                                        </div>
                                        <div style="margin-left: 10px;">
                                            <form action="/SGA-SEBANA/public/notificaciones/archive/<?= (int) ($n['id'] ?? 0) ?>" method="post" style="margin:0;">
                                                <?= \App\Modules\Usuarios\Helpers\SecurityHelper::csrfField() ?>
                                                <button type="submit" title="Eliminar" style="color: #dc3545; padding: 5px; border:none; background:none;">
                                                    <i class="zmdi zmdi-close"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="header-button">
                                <div class="account-wrap">
                                    <div class="account-item clearfix js-item-menu">
                                        <div class="image"
                                            style="display: flex; align-items: center; justify-content: center; width: 36px; height: 36px; background: #3b5998; border-radius: 50%;">
                                            <i class="fas fa-user" style="color: white; font-size: 18px;"></i>
                                        </div>
                                        <div class="content">
                                            <a class="js-acc-btn"
                                                href="#"><?= htmlspecialchars($displayName) ?></a>
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
                                                    <span class="text-muted px-4">
                                                        <i
                                                            class="zmdi zmdi-shield-check me-3"></i><?= htmlspecialchars($authUser['rol_nombre'] ?? 'Sin rol') ?></span>
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
            <div class="main-content">
                <div class="section__content section__content--p30">
                    <div class="container-fluid">
                        <?php if (isset($_GET['error']) && $_GET['error'] === 'no_autorizado'): ?>
                            <div class="alert alert-danger mt-3">
                                No tiene permisos para acceder a esta seccion segun su rol.
                            </div>
                        <?php endif; ?>
                        <?= $content ?? '' ?>
                    </div>
                </div>
            </div>
            </div>
    </div>

    <script src="/SGA-SEBANA/public/assets/js/vanilla-utils.js"></script>

    <script src="/SGA-SEBANA/public/assets/vendor/bootstrap-5.3.8.bundle.min.js"></script>

    <script src="/SGA-SEBANA/public/assets/vendor/perfect-scrollbar/perfect-scrollbar-1.5.6.min.js"></script>
    <script src="/SGA-SEBANA/public/assets/vendor/chartjs/chart.umd.js-4.5.1.min.js"></script>

    <script src="/SGA-SEBANA/public/assets/js/bootstrap5-init.js"></script>
    <script src="/SGA-SEBANA/public/assets/js/main.js"></script>
    <script src="/SGA-SEBANA/public/assets/js/swiper-bundle-12.0.3.min.js"></script>
    <script src="/SGA-SEBANA/public/assets/js/aos.js"></script>
    <script src="/SGA-SEBANA/public/assets/js/modern-plugins.js"></script>

</body>

</html>
