<?php
/**
 * Vista de Emisión de Carné
 */
ob_start();
?>

<div class="row">
    <div class="col-lg-12">
        <div class="overview-wrap mb-4">
            <h2 class="title-1">Emisión de Carné</h2>
            <a href="/SGA-SEBANA/public/afiliados" class="au-btn au-btn-icon au-btn--blue">
                <i class="zmdi zmdi-arrow-left"></i> Volver a Afiliados
            </a>
        </div>

        <?php if (!empty($mensaje)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="zmdi zmdi-check-circle"></i> <?= htmlspecialchars($mensaje) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                    <div class="card-header bg-primary text-white p-4 text-center border-0 position-relative">
                        <!-- Background Decorative Element -->
                        <div
                            style="position:absolute; top:-20px; right:-20px; width:100px; height:100px; background:rgba(255,255,255,0.1); border-radius:50%;">
                        </div>

                        <img src="/SGA-SEBANA/public/assets/img/icon/sebana_logo-removebg.png" alt="Logo Sebana"
                            style="width: 80px; filter: brightness(0) invert(1);" class="mb-2">
                        <h4 class="text-white mb-0" style="letter-spacing: 1px; font-weight: 700;">SGA-SEBANA</h4>
                        <p class="text-white-50 mb-0 small">CARNÉ DE AFILIACIÓN</p>
                    </div>

                    <div class="card-body p-0 text-center">
                        <!-- QR Section -->
                        <div class="py-4 bg-light">
                            <div class="qr-wrapper d-inline-block p-3 bg-white shadow-sm rounded-3 border">
                                <img src="<?= $qr_image ?>" alt="QR Code" style="width: 200px; height: 200px;">
                            </div>
                        </div>

                        <!-- Data Section -->
                        <div class="px-4 py-4 text-left">
                            <div class="mb-3">
                                <label class="text-muted small mb-0 d-block">NOMBRE COMPLETO</label>
                                <span
                                    class="h5 font-weight-bold text-dark"><?= htmlspecialchars($afiliado['nombre_completo'] ?? ($afiliado['nombre'] . ' ' . $afiliado['apellido1'])) ?></span>
                            </div>

                            <div class="row">
                                <div class="col-6">
                                    <label class="text-muted small mb-0 d-block">CÉDULA</label>
                                    <span class="font-weight-bold"><?= htmlspecialchars($afiliado['cedula']) ?></span>
                                </div>
                                <div class="col-6">
                                    <label class="text-muted small mb-0 d-block">ESTADO</label>
                                    <?php if (strtolower($afiliado['estado']) === 'activo'): ?>
                                        <span class="badge badge-success px-3 py-1 rounded-pill" style="color:#fff !important; background-color: #28a745;">VIGENTE</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger px-3 py-1 rounded-pill" style="color:#fff !important; background-color: #dc3545;"><?= strtoupper($afiliado['estado']) ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-white border-top p-4 text-center">
                        <a href="/SGA-SEBANA/public/carnets/descargar/<?= $afiliado['id'] ?>"
                            class="au-btn au-btn-icon au-btn--green w-100">
                            <i class="zmdi zmdi-download"></i> Descargar Carné (PDF)
                        </a>
                        <p class="mt-3 text-muted small mb-0">
                            <i class="zmdi zmdi-info-outline"></i> Presente este QR para validar su afiliación.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Custom animations or refinements */
    .card {
        transition: transform 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
    }

    .qr-wrapper img {
        transition: all 0.3s ease;
    }

    .qr-wrapper:hover img {
        transform: scale(1.05);
    }
</style>

<?php
$content = ob_get_clean();
require $_SERVER['DOCUMENT_ROOT'] . '/SGA-SEBANA/public/templates/base.html.php';
?>