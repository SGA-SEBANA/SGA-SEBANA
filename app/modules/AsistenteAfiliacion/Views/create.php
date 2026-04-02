<?php
ob_start();

$form = $form ?? [];
$get = static function ($key, $default = '') use ($form) {
    return htmlspecialchars((string) ($form[$key] ?? $default), ENT_QUOTES, 'UTF-8');
};

$status = $status ?? null;
?>

<div class="login-logo mb-3 text-center">
    <div style="background-color: #1c4388; padding: 12px; border-radius: 8px; display: inline-block;">
        <img src="/SGA-SEBANA/public/assets/img/icon/sebana_logo-removebg.png" alt="SEBANA" style="max-height: 70px;">
    </div>
    <h3 class="text-center mt-3 mb-1" style="color:#1c4388; font-weight: 700;">Asistente de Afiliacion a SEBANA</h3>
    <p class="text-muted small mb-0">Formulario publico para solicitud de afiliacion</p>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <strong>Revise la solicitud:</strong>
        <ul class="mb-0 mt-2 ps-3">
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php if (($success ?? null) === 'enviado'): ?>
    <div class="alert alert-success">
        Solicitud enviada correctamente. SEBANA fue notificado para revision.
    </div>
<?php endif; ?>

<?php if (!empty($status)): ?>
    <div class="alert alert-info">
        Estado actual del borrador: <strong><?= htmlspecialchars((string) $status) ?></strong>
    </div>
<?php endif; ?>

<div class="login-form">
    <form action="/SGA-SEBANA/public/afiliarse/enviar" method="post" enctype="multipart/form-data" id="afiliacion-form">
        <input type="hidden" name="draft_id" value="<?= htmlspecialchars((string) ($draft_id ?? '')) ?>">

        <h5 class="mb-3 text-primary">1. Elegibilidad BNCR</h5>
        <div class="form-group mb-3">
            <label class="form-label">Tipo de usuario BNCR *</label>
            <select name="tipo_usuario" id="tipo_usuario" class="form-control" required>
                <option value="">Seleccione una opcion</option>
                <option value="activo" <?= ($get('tipo_usuario') === 'activo') ? 'selected' : '' ?>>Activo BNCR</option>
                <option value="jubilado" <?= ($get('tipo_usuario') === 'jubilado') ? 'selected' : '' ?>>Jubilado BNCR</option>
                <option value="otro" <?= ($get('tipo_usuario') === 'otro') ? 'selected' : '' ?>>Otro (No elegible)</option>
            </select>
            <small class="text-muted">Solo aplica para personal BNCR activo o jubilado.</small>
        </div>

        <div class="alert alert-warning d-none" id="eligibility-warning">
            Este tipo de usuario no es elegible para afiliacion a SEBANA.
        </div>

        <h5 class="mt-4 mb-3 text-primary">2. Datos personales</h5>
        <div class="row">
            <div class="col-md-6 form-group mb-3">
                <label>Cedula *</label>
                <input type="text" class="form-control" name="cedula" value="<?= $get('cedula') ?>" required>
            </div>
            <div class="col-md-6 form-group mb-3">
                <label>Correo electronico *</label>
                <input type="email" class="form-control" name="correo" value="<?= $get('correo') ?>" required>
            </div>
            <div class="col-md-4 form-group mb-3">
                <label>Nombre *</label>
                <input type="text" class="form-control" name="nombre" value="<?= $get('nombre') ?>" required>
            </div>
            <div class="col-md-4 form-group mb-3">
                <label>Primer apellido *</label>
                <input type="text" class="form-control" name="apellido1" value="<?= $get('apellido1') ?>" required>
            </div>
            <div class="col-md-4 form-group mb-3">
                <label>Segundo apellido</label>
                <input type="text" class="form-control" name="apellido2" value="<?= $get('apellido2') ?>">
            </div>
            <div class="col-md-6 form-group mb-3">
                <label>Fecha de nacimiento *</label>
                <input type="date" class="form-control" name="fecha_nacimiento" value="<?= $get('fecha_nacimiento') ?>" required>
            </div>
            <div class="col-md-6 form-group mb-3">
                <label>Celular *</label>
                <input type="text" class="form-control" name="celular" value="<?= $get('celular') ?>" placeholder="Ejemplo: 88887777" required>
            </div>
        </div>

        <h5 class="mt-4 mb-3 text-primary">3. Informacion laboral BNCR</h5>
        <div class="row">
            <div class="col-md-6 form-group mb-3">
                <label>Numero de empleado *</label>
                <input type="text" class="form-control" name="numero_empleado" value="<?= $get('numero_empleado') ?>" required>
            </div>
            <div class="col-md-6 form-group mb-3">
                <label>Oficina BNCR *</label>
                <select class="form-control" name="oficina_id" required>
                    <option value="">Seleccione una oficina</option>
                    <?php foreach (($oficinas ?? []) as $oficina): ?>
                        <?php
                        $oficinaId = (string) ($oficina['id'] ?? '');
                        $selectedId = (string) ($form['oficina_id'] ?? '');
                        $selected = ($selectedId !== '' && $selectedId === $oficinaId)
                            || ($selectedId === '' && $get('oficina_bncr') === (string) ($oficina['nombre'] ?? ''));
                        ?>
                        <option value="<?= htmlspecialchars($oficinaId) ?>" <?= $selected ? 'selected' : '' ?>>
                            <?= htmlspecialchars((string) ($oficina['nombre'] ?? '')) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6 form-group mb-3">
                <label>Departamento *</label>
                <input type="text" class="form-control" name="departamento" value="<?= $get('departamento') ?>" required>
            </div>
            <div class="col-md-6 form-group mb-3">
                <label>Puesto *</label>
                <input type="text" class="form-control" name="puesto" value="<?= $get('puesto') ?>" required>
            </div>
            <div class="col-md-6 form-group mb-3">
                <label>Categoria de afiliacion</label>
                <select class="form-control" name="categoria_id">
                    <option value="">Seleccione una categoria</option>
                    <?php foreach (($categorias ?? []) as $categoria): ?>
                        <?php
                        $categoriaId = (string) ($categoria['id'] ?? '');
                        $selectedCategoria = (string) ($form['categoria_id'] ?? '') === $categoriaId;
                        ?>
                        <option value="<?= htmlspecialchars($categoriaId) ?>" <?= $selectedCategoria ? 'selected' : '' ?>>
                            <?= htmlspecialchars((string) ($categoria['nombre'] ?? '')) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6 form-group mb-3">
                <label>Fecha ingreso BNCR *</label>
                <input type="date" class="form-control" name="fecha_ingreso_bncr" value="<?= $get('fecha_ingreso_bncr') ?>" required>
            </div>
            <div class="col-md-6 form-group mb-3">
                <label>Fecha jubilacion (si aplica)</label>
                <input type="date" class="form-control" name="fecha_jubilacion" value="<?= $get('fecha_jubilacion') ?>">
            </div>
        </div>

        <div class="form-group mb-3">
            <label>Observaciones adicionales</label>
            <textarea class="form-control" name="observaciones" rows="3"><?= $get('observaciones') ?></textarea>
        </div>

        <h5 class="mt-4 mb-3 text-primary">4. Aceptaciones obligatorias</h5>
        <div class="form-check mb-2">
            <input type="checkbox" class="form-check-input" id="acepta_deduccion" name="acepta_deduccion" <?= ((int) ($form['acepta_deduccion'] ?? 0) === 1) ? 'checked' : '' ?>>
            <label class="form-check-label" for="acepta_deduccion">
                Autorizo la deduccion salarial del 1% para cuota sindical. *
            </label>
        </div>
        <div class="form-check mb-3">
            <input type="checkbox" class="form-check-input" id="acepta_estatuto" name="acepta_estatuto" <?= ((int) ($form['acepta_estatuto'] ?? 0) === 1) ? 'checked' : '' ?>>
            <label class="form-check-label" for="acepta_estatuto">
                Declaro haber leido y aceptado el estatuto de SEBANA. *
            </label>
        </div>

        <h5 class="mt-4 mb-3 text-primary">5. Documento de afiliacion</h5>
        <p class="text-muted small mb-2">
            Primero genere o descargue el PDF, firmelo manual o digitalmente fuera del sistema y luego adjunte el PDF firmado.
        </p>
        <div class="form-group mb-3">
            <label>Adjuntar PDF firmado *</label>
            <input type="file" class="form-control" name="pdf_firmado" accept=".pdf,application/pdf">
        </div>

        <div class="row g-2 mt-3">
            <div class="col-md-4 d-grid">
                <button type="submit" class="btn btn-outline-primary"
                    formaction="/SGA-SEBANA/public/afiliarse/pdf/generar" formmethod="post">
                    Generar PDF
                </button>
            </div>
            <div class="col-md-4 d-grid">
                <button type="submit" class="btn btn-outline-secondary"
                    formaction="/SGA-SEBANA/public/afiliarse/pdf/descargar" formmethod="post">
                    Descargar PDF
                </button>
            </div>
            <div class="col-md-4 d-grid">
                <button type="submit" class="btn btn-success" id="btn-enviar">
                    Enviar Solicitud
                </button>
            </div>
        </div>
    </form>

    <div class="text-center mt-4 pt-2 border-top">
        <a href="/SGA-SEBANA/public/login" class="small">Volver a inicio de sesion</a>
    </div>
</div>

<script>
    (function() {
        const tipo = document.getElementById('tipo_usuario');
        const warning = document.getElementById('eligibility-warning');
        const buttons = document.querySelectorAll('#afiliacion-form button');

        const updateEligibility = () => {
            const value = (tipo.value || '').toLowerCase();
            const allow = (value === 'activo' || value === 'jubilado');

            if (allow || value === '') {
                warning.classList.add('d-none');
            } else {
                warning.classList.remove('d-none');
            }

            buttons.forEach((btn) => {
                btn.disabled = (!allow && value !== '');
            });
        };

        tipo.addEventListener('change', updateEligibility);
        updateEligibility();
    })();
</script>

<?php
$content = ob_get_clean();
require BASE_PATH . '/public/templates/auth.html.php';
?>
