<?php
/**
 * Vista de Historial de Etapas
 */
ob_start();
?>

<div class="row">
    <div class="col-md-12">
        <div class="overview-wrap mb-4">
            <h2 class="title-1"><i class="zmdi zmdi-time"></i> Historial de Etapas: <?= htmlspecialchars($caso['numero_expediente']) ?></h2>
            <a href="/SGA-SEBANA/public/casos-rrll/<?= $caso['id'] ?>/etapas" class="au-btn au-btn-icon au-btn--blue">
                <i class="zmdi zmdi-arrow-left"></i> Volver
            </a>
        </div>

        <!-- Información del Caso -->
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <strong>Caso:</strong> <?= htmlspecialchars($caso['titulo']) ?><br>
                        <strong>Expediente:</strong> <?= htmlspecialchars($caso['numero_expediente']) ?>
                    </div>
                    <div class="col-md-4 text-right">
                        <small class="text-muted">Abierto: <?= date('d/m/Y', strtotime($caso['fecha_apertura'])) ?></small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Timeline de Etapas -->
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="zmdi zmdi-layers"></i> Evolución de Etapas</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($historial)): ?>
                    <div class="timeline timeline-simple">
                        <div class="timeline-row">
                            <div class="timeline-time"></div>
                            <div class="timeline-content"></div>
                        </div>

                        <?php foreach($historial as $index => $etapa): ?>
                            <div class="timeline-row">
                                <div class="timeline-time">
                                    <span class="date"><?= date('d/m', strtotime($etapa['fecha_creacion'])) ?></span>
                                    <span class="time"><?= date('H:i', strtotime($etapa['fecha_creacion'])) ?></span>
                                </div>
                                <div class="timeline-content">
                                    <h6 class="mb-0">
                                        <span class="badge bg-primary">Etapa <?= $etapa['orden'] ?></span>
                                        <?= htmlspecialchars($etapa['nombre']) ?>
                                    </h6>
                                    <p class="mb-2"><?= htmlspecialchars($etapa['descripcion'] ?? 'Sin descripción') ?></p>
                                    
                                    <small>
                                        <strong>Estado:</strong>
                                        <span class="badge bg-<?= $etapa['estado'] === 'finalizado' ? 'success' : ($etapa['estado'] === 'en_progreso' ? 'primary' : 'secondary') ?>">
                                            <?= ucfirst(str_replace('_', ' ', $etapa['estado'])) ?>
                                        </span>
                                        <?php if ($etapa['responsable_id']): ?>
                                            | <strong>Responsable:</strong> <?= htmlspecialchars($etapa['responsable_nombre'] ?? 'N/A') ?>
                                        <?php endif; ?>
                                    </small>

                                    <div class="mt-2">
                                        <small class="text-muted">
                                            <?php if ($etapa['fecha_inicio']): ?>
                                                Inicio: <?= date('d/m/Y', strtotime($etapa['fecha_inicio'])) ?>
                                            <?php endif; ?>
                                            <?php if ($etapa['fecha_estimada_fin']): ?>
                                                | Est. Fin: <?= date('d/m/Y', strtotime($etapa['fecha_estimada_fin'])) ?>
                                            <?php endif; ?>
                                            <?php if ($etapa['fecha_fin']): ?>
                                                | Fin Real: <?= date('d/m/Y', strtotime($etapa['fecha_fin'])) ?>
                                            <?php endif; ?>
                                        </small>
                                    </div>

                                    <?php if (!empty($etapa['resultado'])): ?>
                                        <div class="mt-2">
                                            <small><strong>Resultado:</strong> <?= htmlspecialchars($etapa['resultado']) ?></small>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($etapa['observaciones'])): ?>
                                        <div class="mt-2">
                                            <small><strong>Observaciones:</strong> <?= htmlspecialchars($etapa['observaciones']) ?></small>
                                        </div>
                                    <?php endif; ?>

                                    <div class="mt-3">
                                        <small class="text-muted">
                                            Registro: <?= date('d/m/Y H:i', strtotime($etapa['fecha_creacion'])) ?>
                                            | Actualización: <?= date('d/m/Y H:i', strtotime($etapa['fecha_actualizacion'])) ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <style>
                        .timeline-simple {
                            position: relative;
                            padding: 20px 0;
                        }

                        .timeline-simple .timeline-row {
                            display: flex;
                            margin-bottom: 40px;
                            position: relative;
                        }

                        .timeline-simple .timeline-row:not(:last-child)::after {
                            content: '';
                            position: absolute;
                            left: 50px;
                            top: 50px;
                            width: 2px;
                            height: calc(100% + 20px);
                            background-color: #e0e0e0;
                        }

                        .timeline-simple .timeline-time {
                            width: 100px;
                            flex-shrink: 0;
                            text-align: right;
                            padding-right: 20px;
                        }

                        .timeline-simple .timeline-time .date {
                            display: block;
                            font-weight: bold;
                            color: #333;
                        }

                        .timeline-simple .timeline-time .time {
                            display: block;
                            font-size: 12px;
                            color: #999;
                        }

                        .timeline-simple .timeline-content {
                            flex: 1;
                            padding-left: 40px;
                            position: relative;
                        }

                        .timeline-simple .timeline-content::before {
                            content: '';
                            position: absolute;
                            left: 0;
                            top: 3px;
                            width: 12px;
                            height: 12px;
                            background-color: #007bff;
                            border: 3px solid white;
                            border-radius: 50%;
                        }

                        .timeline-simple .timeline-row:last-child .timeline-content::after {
                            content: '';
                            position: absolute;
                            left: -8px;
                            top: 15px;
                            width: 2px;
                            height: 0;
                            background-color: transparent;
                        }
                    </style>

                <?php else: ?>
                    <div class="text-center text-muted py-5">
                        <i class="zmdi zmdi-info" style="font-size: 48px;"></i>
                        <p class="mt-3">No hay etapas registradas para este caso.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Resumen estadístico -->
        <?php if (!empty($historial)): ?>
            <div class="row mt-4">
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5><?= count($historial) ?></h5>
                            <small class="text-muted">Total de Etapas</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5><?= count(array_filter($historial, fn($e) => $e['estado'] === 'finalizado')) ?></h5>
                            <small class="text-muted">Completadas</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5><?= count(array_filter($historial, fn($e) => $e['estado'] === 'en_progreso')) ?></h5>
                            <small class="text-muted">En Progreso</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5><?= count(array_filter($historial, fn($e) => $e['estado'] === 'pendiente')) ?></h5>
                            <small class="text-muted">Pendientes</small>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
require $_SERVER['DOCUMENT_ROOT'] . '/SGA-SEBANA/public/templates/base.html.php';
?>
