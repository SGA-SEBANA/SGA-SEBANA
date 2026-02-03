<?php
/**
 * Users List View - SGA-SEBANA
 * Uses base.html.php template
 */

use App\Modules\Usuarios\Helpers\SecurityHelper;

ob_start();
?>

<div class="row">
    <div class="col-md-12">
        <!-- Page Header -->
        <div class="overview-wrap mb-4">
            <h2 class="title-1">Gestión de Usuarios</h2>
            <a href="/SGA-SEBANA/public/users/create" class="au-btn au-btn-icon au-btn--green">
                <i class="zmdi zmdi-plus"></i> Nuevo Usuario
            </a>
        </div>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= SecurityHelper::e($success) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= SecurityHelper::e($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Users Table -->
        <div class="table-responsive table-responsive-data2">
            <table class="table table-data2">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Email</th>
                        <th>Nombre</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Último Acceso</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="7" class="text-center">No hay usuarios registrados.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $user): ?>
                            <tr class="tr-shadow">
                                <td>
                                    <span class="block-email">
                                        <?= SecurityHelper::e($user['username']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="block-email">
                                        <?= SecurityHelper::e($user['correo']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?= SecurityHelper::e($user['nombre_completo'] ?? '-') ?>
                                </td>
                                <td>
                                    <span class="badge bg-primary">
                                        <?= SecurityHelper::e($user['rol_nombre'] ?? 'Sin rol') ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($user['estado'] === 'activo'): ?>
                                        <span class="badge bg-success">Activo</span>
                                    <?php elseif ($user['estado'] === 'inactivo'): ?>
                                        <span class="badge bg-secondary">Inactivo</span>
                                    <?php elseif ($user['estado'] === 'bloqueado'): ?>
                                        <span class="badge bg-danger">Bloqueado</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($user['ultimo_acceso']): ?>
                                        <?= date('d/m/Y H:i', strtotime($user['ultimo_acceso'])) ?>
                                    <?php else: ?>
                                        <span class="text-muted">Nunca</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="table-data-feature text-center">
                                        <a href="/SGA-SEBANA/public/users/<?= $user['id'] ?>" class="item"
                                            data-bs-toggle="tooltip" title="Ver">
                                            <i class="zmdi zmdi-eye"></i>
                                        </a>
                                        <a href="/SGA-SEBANA/public/users/<?= $user['id'] ?>/edit" class="item"
                                            data-bs-toggle="tooltip" title="Editar">
                                            <i class="zmdi zmdi-edit"></i>
                                        </a>
                                        <?php if ($user['id'] != ($authUser['id'] ?? 0)): ?>
                                            <form action="/SGA-SEBANA/public/users/<?= $user['id'] ?>/toggle" method="post"
                                                style="display: inline;">
                                                <input type="hidden" name="_csrf_token"
                                                    value="<?= SecurityHelper::getCsrfToken() ?>">
                                                <button type="submit" class="item" data-bs-toggle="tooltip"
                                                    title="<?= $user['estado'] === 'activo' ? 'Desactivar' : 'Activar' ?>"
                                                    onclick="return confirm('¿Está seguro de <?= $user['estado'] === 'activo' ? 'desactivar' : 'activar' ?> este usuario?')">
                                                    <i
                                                        class="zmdi zmdi-<?= $user['estado'] === 'activo' ? 'block' : 'check' ?>"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
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

<?php
$content = ob_get_clean();
require $_SERVER['DOCUMENT_ROOT'] . '/SGA-SEBANA/public/templates/base.html.php';
?>