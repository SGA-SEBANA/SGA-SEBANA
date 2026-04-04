<?php
$title = 'Bitacora';
ob_start();
?>

<div class="row">
    <div class="col-md-12">
        <div class="overview-wrap mb-4">
            <h2 class="title-1">Bitacora del sistema</h2>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <strong><i class="zmdi zmdi-filter-list"></i> Busqueda y filtros</strong>
            </div>
            <div class="card-body">
                <form method="GET">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label mb-1">Busqueda</label>
                                <input type="text" class="form-control" name="q"
                                    value="<?= htmlspecialchars((string) ($_GET['q'] ?? '')) ?>"
                                    placeholder="Accion, modulo, entidad, descripcion">
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="control-label mb-1">Modulo</label>
                                <select name="modulo" class="form-control">
                                    <option value="">Todos</option>
                                    <?php foreach ($modulos as $m): ?>
                                        <?php $moduloRaw = (string) ($m['modulo'] ?? ''); ?>
                                        <option value="<?= htmlspecialchars($moduloRaw) ?>"
                                            <?= (($_GET['modulo'] ?? '') === $moduloRaw) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars((string) ($m['modulo_label'] ?? $moduloRaw)) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="control-label mb-1">Accion</label>
                                <select name="accion" class="form-control">
                                    <option value="">Todas</option>
                                    <?php foreach ($acciones as $a): ?>
                                        <?php $accionRaw = (string) ($a['accion'] ?? ''); ?>
                                        <option value="<?= htmlspecialchars($accionRaw) ?>"
                                            <?= (($_GET['accion'] ?? '') === $accionRaw) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars((string) ($a['accion_label'] ?? $accionRaw)) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="control-label mb-1">Resultado</label>
                                <select name="resultado" class="form-control">
                                    <option value="">Todos</option>
                                    <?php foreach ($resultados as $r): ?>
                                        <?php $resultadoRaw = (string) ($r['resultado'] ?? ''); ?>
                                        <option value="<?= htmlspecialchars($resultadoRaw) ?>"
                                            <?= (($_GET['resultado'] ?? '') === $resultadoRaw) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars((string) ($r['resultado_label'] ?? $resultadoRaw)) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="control-label mb-1">Fecha</label>
                                <input type="date" name="fecha" class="form-control"
                                    value="<?= htmlspecialchars((string) ($_GET['fecha'] ?? '')) ?>">
                            </div>
                        </div>

                        <div class="col-md-1 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary btn-block">Filtrar</button>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-12 d-flex gap-2">
                            <a href="/SGA-SEBANA/public/bitacora" class="btn btn-secondary">Limpiar filtros</a>
                            <a href="/SGA-SEBANA/public/bitacora/exportarExcel?<?= http_build_query($_GET) ?>"
                                class="btn btn-success">Exportar Excel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="table-responsive table-responsive-data2">
            <table class="table table-data2">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Accion</th>
                        <th>Modulo</th>
                        <th>Entidad</th>
                        <th>Descripcion</th>
                        <th>Resultado</th>
                        <th>Opciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($bitacora)): ?>
                        <tr>
                            <td colspan="7" class="text-center p-4">No hay registros para los filtros aplicados.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($bitacora as $registro): ?>
                            <tr class="tr-shadow">
                                <td>
                                    <?php $fecha = (string) ($registro['fecha_creacion'] ?? ''); ?>
                                    <?= $fecha !== '' ? htmlspecialchars(date('d/m/Y H:i:s', strtotime($fecha))) : '-' ?>
                                </td>
                                <td><?= htmlspecialchars((string) ($registro['accion_label'] ?? $registro['accion'] ?? '-')) ?></td>
                                <td><?= htmlspecialchars((string) ($registro['modulo_label'] ?? $registro['modulo'] ?? '-')) ?></td>
                                <td>
                                    <?= htmlspecialchars((string) ($registro['entidad_label'] ?? $registro['entidad'] ?? '-')) ?>
                                    <?php if (!empty($registro['entidad_id'])): ?>
                                        <small class="text-muted">#<?= (int) $registro['entidad_id'] ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars((string) ($registro['descripcion'] ?? '-')) ?></td>
                                <td><?= htmlspecialchars((string) ($registro['resultado_label'] ?? $registro['resultado'] ?? '-')) ?></td>
                                <td>
                                    <a href="/SGA-SEBANA/public/bitacora/detalles/<?= (int) ($registro['id'] ?? 0) ?>"
                                        class="au-btn au-btn-icon au-btn--blue au-btn--small text-nowrap">
                                        Ver detalle
                                    </a>
                                </td>
                            </tr>
                            <tr class="spacer"></tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

            <?php if ($totalPaginas > 1): ?>
                <div class="mt-3 d-flex justify-content-center gap-2">
                    <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                        <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"
                            class="btn btn-sm <?= $i === (int) $page ? 'btn-primary' : 'btn-secondary' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require BASE_PATH . '/public/templates/base.html.php';
?>
