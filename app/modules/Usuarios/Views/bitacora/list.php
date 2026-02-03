<?php
/**
 * Bitacora List View (Audit Log) - SGA-SEBANA
 * Uses base.html.php template
 * READ-ONLY view
 */

use App\Modules\Usuarios\Helpers\SecurityHelper;

ob_start();
?>

<div class="row">
    <div class="col-md-12">
        <!-- Page Header -->
        <div class="overview-wrap mb-4">
            <h2 class="title-1">Bitácora de Auditoría</h2>
            <span class="badge bg-secondary">Solo Lectura</span>
        </div>

        <!-- Info Box -->
        <div class="alert alert-info" role="alert">
            <i class="fas fa-info-circle"></i>
            Esta vista muestra un registro de todas las acciones realizadas en el sistema.
            Los registros no pueden ser editados ni eliminados.
        </div>

        <!-- Filters (optional, can be enhanced) -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <strong>Total de registros mostrados:</strong>
                        <?= count($logs) ?>
                    </div>
                    <div class="col-md-3">
                        <strong>Últimos:</strong> 200 registros
                    </div>
                </div>
            </div>
        </div>

        <!-- Bitacora Table -->
        <div class="table-responsive table-responsive-data2">
            <table class="table table-data2 table-striped">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Usuario</th>
                        <th>Acción</th>
                        <th>Módulo</th>
                        <th>Entidad</th>
                        <th>Descripción</th>
                        <th>Resultado</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($logs)): ?>
                        <tr>
                            <td colspan="8" class="text-center">No hay registros en la bitácora.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($logs as $log): ?>
                            <tr class="tr-shadow">
                                <td>
                                    <small>
                                        <?= date('d/m/Y', strtotime($log['fecha_creacion'])) ?>
                                    </small><br>
                                    <small class="text-muted">
                                        <?= date('H:i:s', strtotime($log['fecha_creacion'])) ?>
                                    </small>
                                </td>
                                <td>
                                    <?php if ($log['username']): ?>
                                        <span title="<?= SecurityHelper::e($log['usuario_nombre'] ?? '') ?>">
                                            <?= SecurityHelper::e($log['username']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">Sistema</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $actionBadge = 'bg-secondary';
                                    switch ($log['accion']) {
                                        case 'LOGIN':
                                            $actionBadge = 'bg-success';
                                            break;
                                        case 'LOGIN_FAILED':
                                            $actionBadge = 'bg-danger';
                                            break;
                                        case 'LOGOUT':
                                            $actionBadge = 'bg-info';
                                            break;
                                        case 'CREATE':
                                            $actionBadge = 'bg-primary';
                                            break;
                                        case 'UPDATE':
                                            $actionBadge = 'bg-warning text-dark';
                                            break;
                                        case 'DELETE':
                                            $actionBadge = 'bg-danger';
                                            break;
                                        case 'STATUS_CHANGE':
                                            $actionBadge = 'bg-purple';
                                            break;
                                    }
                                    ?>
                                    <span class="badge <?= $actionBadge ?>">
                                        <?= SecurityHelper::e($log['accion']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?= SecurityHelper::e($log['modulo']) ?>
                                </td>
                                <td>
                                    <?= SecurityHelper::e($log['entidad']) ?>
                                    <?php if ($log['entidad_id']): ?>
                                        <small class="text-muted">#
                                            <?= $log['entidad_id'] ?>
                                        </small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span title="<?= SecurityHelper::e($log['descripcion'] ?? '') ?>">
                                        <?= SecurityHelper::e(substr($log['descripcion'] ?? '-', 0, 50)) ?>
                                        <?= strlen($log['descripcion'] ?? '') > 50 ? '...' : '' ?>
                                    </span>
                                </td>
                                <td>
                                    <?php
                                    $resultBadge = 'bg-success';
                                    if ($log['resultado'] === 'fallido') {
                                        $resultBadge = 'bg-danger';
                                    } elseif ($log['resultado'] === 'bloqueado') {
                                        $resultBadge = 'bg-warning text-dark';
                                    }
                                    ?>
                                    <span class="badge <?= $resultBadge ?>">
                                        <?= SecurityHelper::e($log['resultado']) ?>
                                    </span>
                                </td>
                                <td>
                                    <small title="<?= SecurityHelper::e($log['user_agent'] ?? '') ?>">
                                        <?= SecurityHelper::e($log['ip_address']) ?>
                                    </small>
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

<style>
    .bg-purple {
        background-color: #6f42c1 !important;
        color: white;
    }
</style>

<?php
$content = ob_get_clean();
require $_SERVER['DOCUMENT_ROOT'] . '/SGA-SEBANA/public/templates/base.html.php';
?>