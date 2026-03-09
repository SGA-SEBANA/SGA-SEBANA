<?php
/**
 * Vista: Detalles de la Solicitud (Espaciado Profesional Aplicado y PDF Conectado)
 */
ob_start();
?>

<div class="row">
    <div class="col-lg-12">
        <div class="overview-wrap mb-4">
            <h2 class="title-1">Detalles de Solicitud: <?= htmlspecialchars($viatico['consecutivo']) ?></h2>
            <div>
                <a href="/SGA-SEBANA/public/viaticos/pdf?id=<?= htmlspecialchars($viatico['id']) ?>" target="_blank" class="btn btn-danger mr-2">
                    <i class="zmdi zmdi-collection-pdf me-2"></i>Generar PDF
                </a>
                <a href="/SGA-SEBANA/public/viaticos" class="btn btn-secondary">
                    <i class="zmdi zmdi-arrow-left me-2"></i>Volver
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <strong><i class="zmdi zmdi-info me-2"></i>Información General</strong>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-2">
                                <small class="text-muted d-block">Estado</small>
                                <span class="badge badge-<?= $viatico['estado'] === 'Borrador' ? 'secondary' : 'success' ?> px-3 py-2" style="font-size: 0.9rem;">
                                    <?= htmlspecialchars($viatico['estado']) ?>
                                </span>
                            </div>
                            <div class="col-md-4 mb-2">
                                <small class="text-muted d-block">Fecha de Creación</small>
                                <strong><?= date('d/m/Y h:i A', strtotime($viatico['creado_en'])) ?></strong>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <strong><i class="zmdi zmdi-car me-2"></i>Detalles de Transporte</strong>
                    </div>
                    <div class="card-body">
                        <?php if ($viatico['aplica_transporte']): ?>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <small class="text-muted d-block">Tipo de Vehículo</small>
                                    <strong><?= ucfirst(htmlspecialchars($viatico['tipo_vehiculo'])) ?></strong>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <small class="text-muted d-block">Kilometraje Recorrido</small>
                                    <strong><?= number_format($viatico['kilometraje'], 2) ?> km</strong>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <small class="text-muted d-block">Tarifa por km (CGR)</small>
                                    <strong>₡<?= number_format($viatico['tarifa_km'], 2) ?></strong>
                                </div>
                                <?php if (!empty($viatico['enlace_maps'])): ?>
                                    <div class="col-md-12 mt-2">
                                        <small class="text-muted d-block">Ruta (Google Maps)</small>
                                        <a href="<?= htmlspecialchars($viatico['enlace_maps']) ?>" target="_blank" class="text-primary">
                                            <i class="zmdi zmdi-map me-2"></i>Ver ruta en el mapa
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted mb-0">No se solicitó cobro por kilometraje.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <strong><i class="zmdi zmdi-attachment-alt me-2"></i>Archivo de Respaldo</strong>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($viatico['archivo_comprobante'])): ?>
                            <div class="d-flex align-items-center">
                                <i class="zmdi zmdi-file-text zmdi-hc-3x text-primary mr-3"></i>
                                <div>
                                    <p class="mb-1">El usuario adjuntó un comprobante para esta solicitud.</p>
                                    <a href="/SGA-SEBANA/public/<?= htmlspecialchars($viatico['archivo_comprobante']) ?>" target="_blank" class="btn btn-outline-primary btn-sm">
                                        <i class="zmdi zmdi-download me-2"></i>Ver / Descargar Archivo
                                    </a>
                                </div>
                            </div>
                        <?php else: ?>
                            <p class="text-muted mb-0">No se adjuntaron comprobantes.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm mb-4 border-info">
                    <div class="card-header bg-info text-white">
                        <strong><i class="zmdi zmdi-receipt me-2"></i>Resumen de Cobro</strong>
                    </div>
                    <div class="card-body">
                        <h6 class="text-muted mb-3 border-bottom pb-2">Alimentación</h6>
                        <ul class="list-unstyled mb-4">
                            <li class="d-flex justify-content-between mb-2">
                                <span><i class="zmdi <?= $viatico['aplica_desayuno'] ? 'zmdi-check text-success' : 'zmdi-close text-danger' ?>"></i> Desayuno</span>
                                <strong><?= $viatico['aplica_desayuno'] ? '₡4,200.00' : '₡0.00' ?></strong>
                            </li>
                            <li class="d-flex justify-content-between mb-2">
                                <span><i class="zmdi <?= $viatico['aplica_almuerzo'] ? 'zmdi-check text-success' : 'zmdi-close text-danger' ?>"></i> Almuerzo</span>
                                <strong><?= $viatico['aplica_almuerzo'] ? '₡5,600.00' : '₡0.00' ?></strong>
                            </li>
                            <li class="d-flex justify-content-between mb-2">
                                <span><i class="zmdi <?= $viatico['aplica_cena'] ? 'zmdi-check text-success' : 'zmdi-close text-danger' ?>"></i> Cena</span>
                                <strong><?= $viatico['aplica_cena'] ? '₡5,600.00' : '₡0.00' ?></strong>
                            </li>
                            <li class="d-flex justify-content-between border-top pt-2 mt-2">
                                <span class="text-muted">Subtotal Alimentación</span>
                                <strong>₡<?= number_format($viatico['monto_alimentacion'], 2) ?></strong>
                            </li>
                        </ul>

                        <h6 class="text-muted mb-3 border-bottom pb-2">Transporte</h6>
                        <div class="d-flex justify-content-between mb-4">
                            <span class="text-muted">Subtotal Transporte</span>
                            <strong>₡<?= number_format($viatico['monto_transporte'], 2) ?></strong>
                        </div>

                        <div class="bg-light p-3 text-center rounded border border-info">
                            <h5 class="text-info font-weight-bold mb-1">GRAN TOTAL</h5>
                            <h2 class="text-info font-weight-bold mb-0">₡<?= number_format($viatico['total_pagar'], 2) ?></h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require $_SERVER['DOCUMENT_ROOT'] . '/SGA-SEBANA/public/templates/base.html.php';
?>