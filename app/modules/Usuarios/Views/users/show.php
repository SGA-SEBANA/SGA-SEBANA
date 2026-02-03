<?php
/**
 * User Show View (Detail) - SGA-SEBANA
 * Uses base.html.php template
 */

use App\Modules\Usuarios\Helpers\SecurityHelper;

ob_start();
?>

<div class="row">
    <div class="col-lg-8 offset-lg-2">
        <!-- Page Header -->
        <div class="overview-wrap mb-4">
            <h2 class="title-1">Detalle de Usuario</h2>
            <a href="/SGA-SEBANA/public/users" class="au-btn au-btn-icon au-btn--blue">
                <i class="zmdi zmdi-arrow-left"></i> Volver
            </a>
        </div>

        <!-- User Details Card -->
        <div class="card">
            <div class="card-header">
                <strong>Información del Usuario</strong>
                <a href="/SGA-SEBANA/public/users/<?= $user['id'] ?>/edit" class="btn btn-primary btn-sm float-end">
                    <i class="zmdi zmdi-edit"></i> Editar
                </a>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4"><strong>ID:</strong></div>
                    <div class="col-md-8">
                        <?= SecurityHelper::e($user['id']) ?>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4"><strong>Usuario:</strong></div>
                    <div class="col-md-8">
                        <?= SecurityHelper::e($user['username']) ?>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4"><strong>Email:</strong></div>
                    <div class="col-md-8">
                        <?= SecurityHelper::e($user['correo']) ?>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4"><strong>Nombre Completo:</strong></div>
                    <div class="col-md-8">
                        <?= SecurityHelper::e($user['nombre_completo'] ?? '-') ?>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4"><strong>Teléfono:</strong></div>
                    <div class="col-md-8">
                        <?= SecurityHelper::e($user['telefono'] ?? '-') ?>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4"><strong>Rol:</strong></div>
                    <div class="col-md-8">
                        <span class="badge bg-primary">
                            <?= SecurityHelper::e($user['rol_nombre'] ?? 'Sin rol') ?>
                        </span>
                        <small class="text-muted">(
                            <?= SecurityHelper::e($user['nivel_acceso'] ?? '-') ?>)
                        </small>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4"><strong>Estado:</strong></div>
                    <div class="col-md-8">
                        <?php if ($user['estado'] === 'activo'): ?>
                            <span class="badge bg-success">Activo</span>
                        <?php elseif ($user['estado'] === 'inactivo'): ?>
                            <span class="badge bg-secondary">Inactivo</span>
                        <?php elseif ($user['estado'] === 'bloqueado'): ?>
                            <span class="badge bg-danger">Bloqueado</span>
                        <?php endif; ?>

                        <?php if ($user['bloqueado']): ?>
                            <span class="badge bg-warning text-dark">Cuenta Bloqueada</span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4"><strong>Último Acceso:</strong></div>
                    <div class="col-md-8">
                        <?php if ($user['ultimo_acceso']): ?>
                            <?= date('d/m/Y H:i:s', strtotime($user['ultimo_acceso'])) ?>
                        <?php else: ?>
                            <span class="text-muted">Nunca</span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4"><strong>Intentos Fallidos:</strong></div>
                    <div class="col-md-8">
                        <?= (int) $user['intentos_fallidos'] ?>
                        <?php if ($user['intentos_fallidos'] >= 3): ?>
                            <span class="badge bg-warning text-dark">Advertencia</span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4"><strong>Debe Cambiar Contraseña:</strong></div>
                    <div class="col-md-8">
                        <?= $user['debe_cambiar_contrasena'] ? '<span class="badge bg-info">Sí</span>' : 'No' ?>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4"><strong>Fecha de Creación:</strong></div>
                    <div class="col-md-8">
                        <?= date('d/m/Y H:i:s', strtotime($user['fecha_creacion'])) ?>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4"><strong>Última Actualización:</strong></div>
                    <div class="col-md-8">
                        <?= date('d/m/Y H:i:s', strtotime($user['fecha_actualizacion'])) ?>
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