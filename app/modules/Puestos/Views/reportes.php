<?php
/**
 * Vista de Reportes de Puestos
 */
$title = "Reportes de Puestos";
ob_start();
?>

<div class="row">
    <div class="col-md-12">
        <!-- HEADER -->
        <div class="overview-wrap mb-4">
            <h2 class="title-1">Reportes de Puestos</h2>
            <div class="d-flex">
                <a href="/SGA-SEBANA/public/puestos" class="au-btn au-btn-icon au-btn--blue au-btn--small">
                    <i class="zmdi zmdi-arrow-left"></i> Volver a la lista
                </a>
                <a href="/SGA-SEBANA/public/puestos/export-csv" class="au-btn au-btn-icon au-btn--green au-btn--small"
                    style="margin-left: 8px;">
                    <i class="zmdi zmdi-download"></i> Exportar CSV
                </a>
                <a href="/SGA-SEBANA/public/puestos/export-pdf" class="au-btn au-btn-icon au-btn--small"
                    style="margin-left: 8px; background: #dc3545; color: #fff;">
                    <i class="zmdi zmdi-collection-pdf"></i> Exportar PDF
                </a>
            </div>
        </div>

        <!-- STATISTICS CARDS -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card" style="border-left: 4px solid #3b5998;">
                    <div class="card-body text-center">
                        <h3 class="mb-1">
                            <?= $estadisticas['total'] ?? 0 ?>
                        </h3>
                        <small class="text-muted">Total Puestos</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card" style="border-left: 4px solid #28a745;">
                    <div class="card-body text-center">
                        <h3 class="mb-1" style="color: #28a745;">
                            <?= $estadisticas['activos'] ?? 0 ?>
                        </h3>
                        <small class="text-muted">Activos</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card" style="border-left: 4px solid #dc3545;">
                    <div class="card-body text-center">
                        <h3 class="mb-1" style="color: #dc3545;">
                            <?= $estadisticas['finalizados'] ?? 0 ?>
                        </h3>
                        <small class="text-muted">Finalizados</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card" style="border-left: 4px solid #ffc107;">
                    <div class="card-body text-center">
                        <h3 class="mb-1" style="color: #e0a800;">
                            <?= $estadisticas['suspendidos'] ?? 0 ?>
                        </h3>
                        <small class="text-muted">Suspendidos</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- FULL TABLE -->
        <div class="card">
            <div class="card-header">
                <strong><i class="zmdi zmdi-format-list-bulleted"></i> Listado Completo de Puestos</strong>
            </div>
            <div class="card-body">
                <div class="table-responsive table-responsive-data2">
                    <table class="table table-data2">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Afiliado</th>
                                <th>Cédula</th>
                                <th>Puesto</th>
                                <th>Departamento</th>
                                <th>Oficina</th>
                                <th>Contrato</th>
                                <th>Jornada</th>
                                <th>Salario</th>
                                <th>Estado</th>
                                <th>Asignación</th>
                                <th>Remoción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($puestos)): ?>
                                <tr>
                                    <td colspan="12" class="text-center p-4">
                                        <p>No hay puestos registrados.</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($puestos as $puesto): ?>
                                    <tr class="tr-shadow">
                                        <td>
                                            <?= $puesto['id'] ?>
                                        </td>
                                        <td>
                                            <?= htmlspecialchars($puesto['afiliado_nombre']) ?>
                                        </td>
                                        <td>
                                            <?= htmlspecialchars($puesto['afiliado_cedula']) ?>
                                        </td>
                                        <td><span class="block-email">
                                                <?= htmlspecialchars($puesto['nombre']) ?>
                                            </span></td>
                                        <td>
                                            <?= htmlspecialchars($puesto['departamento'] ?? '—') ?>
                                        </td>
                                        <td>
                                            <?= htmlspecialchars($puesto['oficina_nombre'] ?? '—') ?>
                                        </td>
                                        <td>
                                            <?php
                                            $contrato_labels = ['indefinido' => 'Indefinido', 'temporal' => 'Temporal', 'proyecto' => 'Proyecto'];
                                            echo $contrato_labels[$puesto['tipo_contrato']] ?? ucfirst($puesto['tipo_contrato']);
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            $jornada_labels = ['completa' => 'Completa', 'media' => 'Media', 'por_horas' => 'Por Horas'];
                                            echo $jornada_labels[$puesto['jornada']] ?? ucfirst($puesto['jornada']);
                                            ?>
                                        </td>
                                        <td>
                                            <?= $puesto['salario_base'] ? '₡' . number_format($puesto['salario_base'], 2) : '—' ?>
                                        </td>
                                        <td>
                                            <?php if ($puesto['estado'] === 'activo'): ?>
                                                <span class="status--process">Activo</span>
                                            <?php elseif ($puesto['estado'] === 'finalizado'): ?>
                                                <span class="status--denied">Finalizado</span>
                                            <?php else: ?>
                                                <span class="status--denied"
                                                    style="background: #ffc107; color: #333;">Suspendido</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?= htmlspecialchars($puesto['fecha_asignacion']) ?>
                                        </td>
                                        <td>
                                            <?= $puesto['fecha_remocion'] ? htmlspecialchars($puesto['fecha_remocion']) : '—' ?>
                                        </td>
                                    </tr>
                                    <tr class="spacer"></tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-3">
            <small class="text-muted">
                <?= count($puestos) ?> puesto(s) en total
            </small>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require BASE_PATH . '/public/templates/base.html.php';
