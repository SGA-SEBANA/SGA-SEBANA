<?php
/**
 * Vista de Historial de Puestos de un Afiliado
 */
$title = "Historial de Puestos - " . htmlspecialchars($afiliado['nombre_completo']);
ob_start();
?>

<div class="row">
    <div class="col-md-12">
        <!-- HEADER -->
        <div class="overview-wrap mb-4">
            <h2 class="title-1">Historial de Puestos</h2>
            <a href="/SGA-SEBANA/public/puestos" class="au-btn au-btn-icon au-btn--blue">
                <i class="zmdi zmdi-arrow-left"></i> Volver a la lista
            </a>
        </div>

        <!-- AFILIADO INFO -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <strong><i class="zmdi zmdi-account"></i> Afiliado:</strong>
                        <?= htmlspecialchars($afiliado['nombre_completo']) ?>
                    </div>
                    <div class="col-md-6">
                        <strong><i class="zmdi zmdi-assignment-account"></i> Cédula:</strong>
                        <?= htmlspecialchars($afiliado['cedula']) ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- TABLE -->
        <div class="table-responsive table-responsive-data2">
            <table class="table table-data2">
                <thead>
                    <tr>
                        <th>Puesto</th>
                        <th>Departamento</th>
                        <th>Oficina</th>
                        <th>Contrato</th>
                        <th>Jornada</th>
                        <th>Salario</th>
                        <th>Estado</th>
                        <th>Asignación</th>
                        <th>Remoción</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($puestos)): ?>
                        <tr>
                            <td colspan="10" class="text-center p-4">
                                <p>Este afiliado no tiene puestos registrados.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($puestos as $puesto): ?>
                            <tr class="tr-shadow">
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
                                        <span class="status--denied" style="background: #ffc107; color: #333;">Suspendido</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= htmlspecialchars($puesto['fecha_asignacion']) ?>
                                </td>
                                <td>
                                    <?= $puesto['fecha_remocion'] ? htmlspecialchars($puesto['fecha_remocion']) : '—' ?>
                                </td>
                                <td>
                                    <div class="table-data-feature">
                                        <a href="/SGA-SEBANA/public/puestos/edit/<?= $puesto['id'] ?>" class="item"
                                            data-toggle="tooltip" title="Editar">
                                            <i class="zmdi zmdi-edit"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <tr class="spacer"></tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            <small class="text-muted">
                <?= count($puestos) ?> puesto(s) en el historial
            </small>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require BASE_PATH . '/public/templates/base.html.php';
