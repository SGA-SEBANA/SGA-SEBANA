<?php
$title = 'Detalle de bitacora';
ob_start();
?>

<div class="row">
    <div class="col-md-12">
        <div class="overview-wrap mb-4">
            <h2 class="title-1">Detalle de bitacora</h2>
            <a href="/SGA-SEBANA/public/bitacora" class="au-btn au-btn-icon au-btn--blue">
                <i class="zmdi zmdi-arrow-left"></i> Volver
            </a>
        </div>

        <?php if (!$registro): ?>
            <div class="alert alert-warning">No se encontro el registro solicitado.</div>
        <?php else: ?>
            <?php
            $datosAnteriores = (string) ($registro['datos_anteriores'] ?? '');
            $datosNuevos = (string) ($registro['datos_nuevos'] ?? '');

            $prettyAnteriores = $datosAnteriores;
            $prettyNuevos = $datosNuevos;

            if ($datosAnteriores !== '') {
                $decoded = json_decode($datosAnteriores, true);
                if (is_array($decoded)) {
                    $prettyAnteriores = json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                }
            }

            if ($datosNuevos !== '') {
                $decoded = json_decode($datosNuevos, true);
                if (is_array($decoded)) {
                    $prettyNuevos = json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                }
            }
            ?>
            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th>ID</th>
                            <td><?= (int) ($registro['id'] ?? 0) ?></td>
                        </tr>
                        <tr>
                            <th>Fecha</th>
                            <td><?= htmlspecialchars((string) ($registro['fecha_creacion'] ?? '-')) ?></td>
                        </tr>
                        <tr>
                            <th>Accion</th>
                            <td><?= htmlspecialchars((string) ($registro['accion_label'] ?? $registro['accion'] ?? '-')) ?></td>
                        </tr>
                        <tr>
                            <th>Modulo</th>
                            <td><?= htmlspecialchars((string) ($registro['modulo_label'] ?? $registro['modulo'] ?? '-')) ?></td>
                        </tr>
                        <tr>
                            <th>Entidad</th>
                            <td>
                                <?= htmlspecialchars((string) ($registro['entidad_label'] ?? $registro['entidad'] ?? '-')) ?>
                                <?php if (!empty($registro['entidad_id'])): ?>
                                    <small class="text-muted">#<?= (int) $registro['entidad_id'] ?></small>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Descripcion</th>
                            <td><?= htmlspecialchars((string) ($registro['descripcion'] ?? '-')) ?></td>
                        </tr>
                        <tr>
                            <th>Resultado</th>
                            <td><?= htmlspecialchars((string) ($registro['resultado_label'] ?? $registro['resultado'] ?? '-')) ?></td>
                        </tr>
                        <tr>
                            <th>IP</th>
                            <td><?= htmlspecialchars((string) ($registro['ip_address'] ?? '-')) ?></td>
                        </tr>
                        <tr>
                            <th>Metodo HTTP</th>
                            <td><?= htmlspecialchars((string) ($registro['metodo_http'] ?? '-')) ?></td>
                        </tr>
                        <tr>
                            <th>URL</th>
                            <td><?= htmlspecialchars((string) ($registro['url_accedida'] ?? '-')) ?></td>
                        </tr>
                        <tr>
                            <th>Codigo de error</th>
                            <td><?= htmlspecialchars((string) ($registro['codigo_error'] ?? '-')) ?></td>
                        </tr>
                        <tr>
                            <th>Mensaje de error</th>
                            <td><?= htmlspecialchars((string) ($registro['mensaje_error'] ?? '-')) ?></td>
                        </tr>
                        <tr>
                            <th>Datos anteriores</th>
                            <td><pre class="mb-0"><?= htmlspecialchars($prettyAnteriores !== '' ? $prettyAnteriores : '-') ?></pre></td>
                        </tr>
                        <tr>
                            <th>Datos nuevos</th>
                            <td><pre class="mb-0"><?= htmlspecialchars($prettyNuevos !== '' ? $prettyNuevos : '-') ?></pre></td>
                        </tr>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
require BASE_PATH . '/public/templates/base.html.php';
?>
