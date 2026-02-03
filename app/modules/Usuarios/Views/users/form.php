<?php
/**
 * User Form View (Create/Edit) - SGA-SEBANA
 * Uses base.html.php template
 */

use App\Modules\Usuarios\Helpers\SecurityHelper;

$isEdit = $action === 'edit';
$formAction = $isEdit ? "/SGA-SEBANA/public/users/{$user['id']}" : '/SGA-SEBANA/public/users';

ob_start();
?>

<div class="row">
    <div class="col-lg-12">
        <!-- Page Header -->
        <div class="overview-wrap mb-4">
            <h2 class="title-1">
                <?= $isEdit ? 'Editar Usuario' : 'Nuevo Usuario' ?>
            </h2>
            <a href="/SGA-SEBANA/public/users" class="au-btn au-btn-icon au-btn--blue">
                <i class="zmdi zmdi-arrow-left"></i> Volver a la lista
            </a>
        </div>

        <?php if (!empty($mustChangePassword)): ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="zmdi zmdi-alert-triangle"></i> <strong>¡Atención!</strong> Debe cambiar su contraseña antes de
                continuar.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Por favor corrija los siguientes errores:</strong>
                <ul class="mb-0 mt-2">
                    <?php foreach ($errors as $error): ?>
                        <li><?= SecurityHelper::e($error) ?></li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form action="<?= $formAction ?>" method="post" class="form-horizontal">
            <input type="hidden" name="_csrf_token" value="<?= SecurityHelper::e($csrf_token ?? '') ?>">

            <div class="card">
                <div class="card-header">
                    <strong>Datos de la Cuenta</strong>
                </div>
                <div class="card-body card-block">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="username" class="form-control-label">Usuario <span
                                    class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="zmdi zmdi-account"></i></span>
                                <input type="text" id="username" name="username"
                                    class="form-control <?= isset($errors['username']) ? 'is-invalid' : '' ?>"
                                    placeholder="Nombre de usuario"
                                    value="<?= SecurityHelper::e($old['username'] ?? $user['username'] ?? '') ?>"
                                    required minlength="3">
                            </div>
                            <small class="form-text text-muted">Mínimo 3 caracteres, sin espacios.</small>
                        </div>
                        <div class="col-md-6">
                            <label for="correo" class="form-control-label">Email <span
                                    class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="zmdi zmdi-email"></i></span>
                                <input type="email" id="correo" name="correo"
                                    class="form-control <?= isset($errors['correo']) ? 'is-invalid' : '' ?>"
                                    placeholder="correo@ejemplo.com"
                                    value="<?= SecurityHelper::e($old['correo'] ?? $user['correo'] ?? '') ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="nombre_completo" class="form-control-label">Nombre Completo <span
                                    class="text-danger">*</span></label>
                            <input type="text" id="nombre_completo" name="nombre_completo"
                                class="form-control <?= isset($errors['nombre_completo']) ? 'is-invalid' : '' ?>"
                                placeholder="Nombre y apellidos"
                                value="<?= SecurityHelper::e($old['nombre_completo'] ?? $user['nombre_completo'] ?? '') ?>"
                                required>
                        </div>
                        <div class="col-md-6">
                            <label for="telefono" class="form-control-label">Teléfono</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="zmdi zmdi-phone"></i></span>
                                <input type="tel" id="telefono" name="telefono" class="form-control"
                                    placeholder="8888-8888"
                                    value="<?= SecurityHelper::e($old['telefono'] ?? $user['telefono'] ?? '') ?>">
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="rol_id" class="form-control-label">Rol de Usuario <span
                                    class="text-danger">*</span></label>
                            <select name="rol_id" id="rol_id"
                                class="form-control <?= isset($errors['rol_id']) ? 'is-invalid' : '' ?>" required>
                                <option value="">-- Seleccione un rol --</option>
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?= $role['id'] ?>" <?= ($old['rol_id'] ?? $user['rol_id'] ?? '') == $role['id'] ? 'selected' : '' ?>>
                                        <?= SecurityHelper::e($role['nombre']) ?> (Acceso Nivel
                                        <?= SecurityHelper::e($role['nivel_acceso']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <strong><i class="zmdi zmdi-lock"></i> Seguridad</strong>
                </div>
                <div class="card-body card-block">
                    <h5 class="mb-3 text-muted">
                        <?= $isEdit ? 'Cambiar Contraseña (opcional)' : 'Definir Contraseña' ?>
                    </h5>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="password" class="form-control-label">
                                Contraseña <?= !$isEdit ? '<span class="text-danger">*</span>' : '' ?>
                            </label>
                            <input type="password" id="password" name="password"
                                class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
                                placeholder="<?= $isEdit ? 'Dejar vacío para mantener actual' : 'Contraseña segura' ?>"
                                <?= !$isEdit ? 'required' : '' ?> minlength="8">
                            <small class="form-text text-muted">
                                Mínimo 8 caracteres (A-z, 0-9, !@#).
                            </small>
                        </div>
                        <div class="col-md-6">
                            <label for="password_confirm" class="form-control-label">
                                Confirmar Contraseña <?= !$isEdit ? '<span class="text-danger">*</span>' : '' ?>
                            </label>
                            <input type="password" id="password_confirm" name="password_confirm"
                                class="form-control <?= isset($errors['password_confirm']) ? 'is-invalid' : '' ?>"
                                placeholder="Repita la contraseña" <?= !$isEdit ? 'required' : '' ?>>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-right">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="zmdi zmdi-save"></i> <?= $isEdit ? 'Actualizar Usuario' : 'Crear Usuario' ?>
                    </button>
                    <a href="/SGA-SEBANA/public/users" class="btn btn-danger btn-sm ml-2">
                        <i class="zmdi zmdi-close"></i> Cancelar
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    // Client-side password validation
    document.getElementById('password').addEventListener('input', function () {
        const password = this.value;
        const confirmField = document.getElementById('password_confirm');

        if (password.length > 0) {
            confirmField.setAttribute('required', 'required');
        } else if (<?= $isEdit ? 'true' : 'false' ?>) {
            confirmField.removeAttribute('required');
        }
    });

    document.querySelector('form').addEventListener('submit', function (e) {
        const password = document.getElementById('password').value;
        const confirm = document.getElementById('password_confirm').value;

        if (password && password !== confirm) {
            e.preventDefault();
            alert('Las contraseñas no coinciden.');
        }
    });
</script>

<?php
$content = ob_get_clean();
require $_SERVER['DOCUMENT_ROOT'] . '/SGA-SEBANA/public/templates/base.html.php';
?>