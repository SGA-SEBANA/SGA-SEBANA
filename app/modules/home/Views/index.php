<?php
ob_start();
?>

<div class="row">
    <div class="col-md-12">
        <div class="overview-wrap">
            <h2 class="title-1">Resumen del Sistema</h2>
        </div>
    </div>
</div>
<div class="row m-t-25">
    <div class="col-sm-6 col-lg-3">
        <div class="overview-item overview-item--c1">
            <div class="overview__inner">
                <div class="overview-box clearfix">
                    <div class="icon">
                        <i class="zmdi zmdi-account-o"></i>
                    </div>
                    <div class="text">
                        <h2><?= $stats['afiliados']['total'] ?></h2>
                        <span>Total Afiliados</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="overview-item overview-item--c2">
        <div class="overview__inner">
            <div class="overview-box clearfix">
                <div class="icon">
                    <i class="zmdi zmdi-accounts-alt"></i>
                </div>
                <div class="text">
                    <h2><?= $stats['junta']['total'] ?></h2>
                    <span>Miembros Junta</span>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-sm-6 col-lg-3">
    <div class="overview-item overview-item--c3">
        <div class="overview__inner">
            <div class="overview-box clearfix">
                <div class="icon">
                    <i class="zmdi zmdi-account-box-mail"></i>
                </div>
                <div class="text">
                    <h2><?= $stats['usuarios']['total'] ?></h2>
                    <span>Usuarios Sistema</span>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-sm-6 col-lg-3">
    <div class="overview-item overview-item--c4">
        <div class="overview__inner">
            <div class="overview-box clearfix">
                <div class="icon">
                    <i class="zmdi zmdi-receipt"></i>
                </div>
                <div class="text">
                    <h2><?= $stats['logs_hoy']['total'] ?></h2>
                    <span>Acciones Hoy</span>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="copyright">
            <p>Copyright © <?= date('Y') ?> SGA-SEBANA. All rights reserved.</p>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require BASE_PATH . '/public/templates/base.html.php';
?>