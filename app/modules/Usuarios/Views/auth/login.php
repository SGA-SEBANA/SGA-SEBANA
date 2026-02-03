<?php
/**
 * Login View - SGA-SEBANA
 * Uses auth.html.php template
 */

use App\Modules\Usuarios\Helpers\SecurityHelper;

ob_start();
?>

<div class="login-logo mb-4 text-center">
    <div
        style="background-color: #1c4388; background-image: radial-gradient(ellipse at center, #112955 0%, #1c4388 70%); padding: 15px; border-radius: 10px; display: inline-block;">
        <a href="#">
            <img src="/SGA-SEBANA/public/assets/img/icon/sebana_logo-removebg.png" alt="SGA-SEBANA"
                style="max-height: 80px; filter: drop-shadow(0 0 10px rgba(0,0,0,0.2));">
        </a>
    </div>
    <h3 class="text-center mt-3" style="color: #333; font-weight: 700;">SGA-SEBANA</h3>
    <p class="text-center text-muted small">Sistema de Gestión de Afiliados</p>
</div>

<div class="login-form">
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="zmdi zmdi-alert-circle"></i> <?= SecurityHelper::e($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <form action="/SGA-SEBANA/public/login" method="post">
        <input type="hidden" name="_csrf_token" value="<?= SecurityHelper::e($csrf_token ?? '') ?>">

        <div class="form-group mb-3">
            <label class="form-label">Usuario</label>
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0">
                    <i class="zmdi zmdi-account text-primary"></i>
                </span>
                <input class="form-control border-start-0 ps-0" type="text" name="username"
                    placeholder="Ingrese su usuario" required autofocus>
            </div>
        </div>

        <div class="form-group mb-4">
            <label class="form-label">Contraseña</label>
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0">
                    <i class="zmdi zmdi-lock text-primary"></i>
                </span>
                <input class="form-control border-start-0 ps-0" type="password" name="password"
                    placeholder="Ingrese su contraseña" required>
            </div>
        </div>

        <div class="d-grid gap-2 mt-4">
            <button class="au-btn au-btn--block au-btn--blue" type="submit">
                Iniciar Sesión <i class="zmdi zmdi-long-arrow-right ms-2"></i>
            </button>
        </div>
    </form>

    <div class="text-center mt-4 pt-2 border-top">
        <p class="text-muted small">© <?= date('Y') ?> SEBANA. Todos los derechos reservados.</p>
    </div>
</div>

<?php
$content = ob_get_clean();
require $_SERVER['DOCUMENT_ROOT'] . '/SGA-SEBANA/public/templates/auth.html.php';
?>