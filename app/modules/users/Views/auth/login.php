<?php
/**
 * Login View - SGA-SEBANA
 * Uses auth.html.php template
 */

use App\Modules\Users\Helpers\SecurityHelper;

ob_start();
?>

<div class="login-logo">
    <a href="#">
        <img src="/SGA-SEBANA/public/assets/img/icon/logo.png" alt="SGA-SEBANA">
    </a>
</div>
<div class="login-form">
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger" role="alert">
            <?= SecurityHelper::e($error) ?>
        </div>
    <?php endif; ?>

    <form action="/SGA-SEBANA/public/login" method="post">
        <input type="hidden" name="_csrf_token" value="<?= SecurityHelper::e($csrf_token ?? '') ?>">

        <div class="form-group">
            <label>Usuario</label>
            <input class="au-input au-input--full" type="text" name="username" placeholder="Nombre de usuario" required
                autofocus>
        </div>
        <div class="form-group">
            <label>Contraseña</label>
            <input class="au-input au-input--full" type="password" name="password" placeholder="Contraseña" required>
        </div>
        <!-- <div class="login-checkbox">
            <label>
                <input type="checkbox" name="remember">Recordarme
            </label>
            <label>
                <a href="#">¿Olvidó su contraseña?</a>
            </label>
        </div> -->
        <button class="au-btn au-btn--block au-btn--green m-b-20" type="submit">Iniciar Sesión</button>
    </form>
</div>

<?php
$content = ob_get_clean();
require $_SERVER['DOCUMENT_ROOT'] . '/SGA-SEBANA/public/templates/auth.html.php';
?>