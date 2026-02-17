<?php
$title = "Gestión de Puestos";
ob_start();
?>

<div class="row">
    <div class="col-md-12">
        <!-- HEADER -->
        <div class="overview-wrap mb-4">
            <h2 class="title-1">Gestión de Puestos</h2>
            <div class="d-flex">
                <a href="/SGA-SEBANA/public/puestos/create" class="au-btn au-btn-icon au-btn--green au-btn--small">
                    <i class="zmdi zmdi-plus"></i> Asignar Puesto
                </a>
                <a href="/SGA-SEBANA/public/puestos/reportes" class="au-btn au-btn-icon au-btn--blue au-btn--small ml-2"
                    style="margin-left: 8px;">
                    <i class="zmdi zmdi-chart"></i> Reportes
                </a>
            </div>
        </div>

        <!-- SUCCESS MESSAGE -->
        <?php if (!empty($success)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="zmdi zmdi-check-circle"></i>
                <?= htmlspecialchars($success) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- FILTERS -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="/SGA-SEBANA/public/puestos" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-control-label">Búsqueda</label>
                        <input type="text" name="q" class="form-control" placeholder="Nombre, puesto, cédula..."
                            value="<?= htmlspecialchars($filtros['busqueda'] ?? '') ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-control-label">Estado</label>
                        <select name="estado" class="form-control">
                            <option value="">Todos</option>
                            <option value="activo" <?= ($filtros['estado'] ?? '') === 'activo' ? 'selected' : '' ?>>Activo
                            </option>
                            <option value="finalizado" <?= ($filtros['estado'] ?? '') === 'finalizado' ? 'selected' : '' ?>>Finalizado</option>
                            <option value="suspendido" <?= ($filtros['estado'] ?? '') === 'suspendido' ? 'selected' : '' ?>>Suspendido</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-control-label">Afiliado</label>
                        <select name="afiliado_id" class="form-control">
                            <option value="">Todos</option>
                            <?php foreach ($afiliados as $af): ?>
                                <option value="<?= $af['id'] ?>" <?= ($filtros['afiliado_id'] ?? '') == $af['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($af['nombre_completo']) ?> -
                                    <?= htmlspecialchars($af['cedula']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="au-btn au-btn-icon au-btn--blue au-btn--small">
                            <i class="zmdi zmdi-search"></i> Filtrar
                        </button>
                        <a href="/SGA-SEBANA/public/puestos" class="au-btn au-btn-icon au-btn--small"
                            style="background: #6c757d; color: #fff; margin-left: 5px;" title="Limpiar filtros">
                            <i class="zmdi zmdi-refresh"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- TABLE -->
        <div class="table-responsive table-responsive-data2">
            <table class="table table-data2">
                <thead>
                    <tr>
                        <th>Afiliado</th>
                        <th>Puesto</th>
                        <th>Departamento</th>
                        <th>Oficina</th>
                        <th>Contrato</th>
                        <th>Jornada</th>
                        <th>Estado</th>
                        <th>Fecha Asignación</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($puestos)): ?>
                        <tr>
                            <td colspan="9" class="text-center p-4">
                                <p>No se encontraron puestos registrados.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($puestos as $puesto): ?>
                            <tr class="tr-shadow">
                                <td>
                                    <a href="/SGA-SEBANA/public/puestos/historial/<?= $puesto['afiliado_id'] ?>"
                                        title="Ver historial de <?= htmlspecialchars($puesto['afiliado_nombre']) ?>">
                                        <?= htmlspecialchars($puesto['afiliado_nombre']) ?>
                                    </a>
                                    <br><small class="text-muted">
                                        <?= htmlspecialchars($puesto['afiliado_cedula']) ?>
                                    </small>
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
                                    $contrato_labels = [
                                        'indefinido' => 'Indefinido',
                                        'temporal' => 'Temporal',
                                        'proyecto' => 'Proyecto',
                                    ];
                                    echo $contrato_labels[$puesto['tipo_contrato']] ?? ucfirst($puesto['tipo_contrato']);
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    $jornada_labels = [
                                        'completa' => 'Completa',
                                        'media' => 'Media',
                                        'por_horas' => 'Por Horas',
                                    ];
                                    echo $jornada_labels[$puesto['jornada']] ?? ucfirst($puesto['jornada']);
                                    ?>
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
                                    <div class="table-data-feature">
                                        <a href="/SGA-SEBANA/public/puestos/edit/<?= $puesto['id'] ?>" class="item"
                                            data-toggle="tooltip" data-placement="top" title="Editar">
                                            <i class="zmdi zmdi-edit"></i>
                                        </a>

                                        <form action="/SGA-SEBANA/public/puestos/toggle/<?= $puesto['id'] ?>" method="post"
                                            style="display:inline;">
                                            <button type="submit" class="item" data-toggle="tooltip" data-placement="top"
                                                title="<?= $puesto['estado'] === 'activo' ? 'Finalizar' : 'Activar' ?>"
                                                onclick="return confirm('¿Está seguro de cambiar el estado de este puesto?')">

                                                <?php if ($puesto['estado'] === 'activo'): ?>
                                                    <i class="zmdi zmdi-block" style="color: #fa4251;"></i>
                                                <?php else: ?>
                                                    <i class="zmdi zmdi-check" style="color: #00b5e9;"></i>
                                                <?php endif; ?>

                                            </button>
                                        </form>

                                        <a href="/SGA-SEBANA/public/puestos/delete/<?= $puesto['id'] ?>" class="item"
                                            data-toggle="tooltip" data-placement="top" title="Eliminar"
                                            onclick="return confirm('¿Está seguro de eliminar este puesto?')">
                                            <i class="zmdi zmdi-delete"></i>
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

        <!-- RESULTS COUNT -->
        <div class="mt-3">
            <small class="text-muted">
                <?= count($puestos) ?> puesto(s) encontrado(s)
            </small>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require BASE_PATH . '/public/templates/base.html.php';
