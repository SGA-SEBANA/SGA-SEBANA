<?php
/**
 * Vista de Creación de Afiliados
 */
ob_start();
?>

<div class="row">
    <div class="col-lg-12">
        <div class="overview-wrap mb-4">
            <h2 class="title-1">Nuevo Afiliado</h2>
            <a href="/SGA-SEBANA/public/afiliados" class="au-btn au-btn-icon au-btn--blue">
                <i class="zmdi zmdi-arrow-left"></i> Volver a la lista
            </a>
        </div>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="zmdi zmdi-check-circle"></i> <?= $success ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="zmdi zmdi-alert-triangle"></i> <?= $error ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form action="/SGA-SEBANA/public/afiliados/store" method="post" enctype="multipart/form-data"
            class="form-horizontal">
            <input type="hidden" name="_csrf_token" value="<?= $_SESSION['_csrf_token'] ?? '' ?>">

            <div class="card">
                <div class="card-header">
                    <strong>Formulario de Registro</strong>
                </div>
                <div class="card-body card-block">
                    <!-- Datos Básicos -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="nombre" class="form-control-label">Nombre</label>
                            <input type="text" id="nombre" name="nombre" placeholder="Nombre" class="form-control"
                                required>
                        </div>
                        <div class="col-md-4">
                            <label for="apellido1" class="form-control-label">Primer Apellido</label>
                            <input type="text" id="apellido1" name="apellido1" placeholder="Primer Apellido"
                                class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label for="apellido2" class="form-control-label">Segundo Apellido</label>
                            <input type="text" id="apellido2" name="apellido2" placeholder="Segundo Apellido"
                                class="form-control">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="cedula" class="form-control-label">Cédula</label>
                            <input type="text" id="cedula" name="cedula" placeholder="000000000" class="form-control"
                                pattern="\d{9,12}" title="9 a 12 dígitos" required>
                            <small class="form-text text-muted">Sin guiones ni espacios</small>
                        </div>
                        <div class="col-md-4">
                            <label for="fecha_nac" class="form-control-label">Fecha Nacimiento</label>
                            <input type="date" id="fecha_nac" name="fecha_nacimiento" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-control-label">Género</label>
                            <select name="genero" class="form-control">
                                <option value="Masculino">Masculino</option>
                                <option value="Femenino">Femenino</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="correo" class="form-control-label">Correo</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="zmdi zmdi-email"></i></span>
                                <input type="email" id="correo" name="correo" placeholder="email@ejemplo.com"
                                    class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="telefono" class="form-control-label">Teléfono</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="zmdi zmdi-phone"></i></span>
                                <input type="text" id="telefono" name="telefono" placeholder="8888-8888"
                                    class="form-control">
                            </div>
                        </div>
                    </div>

                    <hr>
                    <!-- Campos Adicionales -->

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="oficina_id" class="form-control-label">Oficina BN</label>
                            <select name="oficina_id" id="oficina_id" class="form-control">
                                <option value="">Seleccione Oficina...</option>
                                <?php if (!empty($oficinas)): ?>
                                    <?php foreach ($oficinas as $of): ?>
                                        <option value="<?= $of['id'] ?>">
                                            <?= htmlspecialchars($of['nombre']) ?> - <?= $of['codigo'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="categoria_id" class="form-control-label">Categoría</label>
                            <select name="categoria_id" id="categoria_id" class="form-control">
                                <option value="">Seleccione Categoría...</option>
                                <?php if (!empty($categorias)): ?>
                                    <?php foreach ($categorias as $cat): ?>
                                        <option value="<?= $cat['id'] ?>">
                                            <?= htmlspecialchars($cat['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="direccion" class="form-control-label">Domicilio</label>
                            <textarea name="direccion" id="direccion" rows="2" placeholder="Dirección exacta..."
                                class="form-control"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label for="puesto_actual" class="form-control-label">Puesto Actual</label>
                            <input type="text" name="puesto_actual" id="puesto_actual" placeholder="Ej: Cajero"
                                class="form-control">
                        </div>
                    </div>

                </div>
                <div class="card-footer text-right">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="zmdi zmdi-save"></i> Registrar Afiliado
                    </button>
                    <button type="reset" class="btn btn-danger btn-sm">
                        <i class="zmdi zmdi-refresh-alt"></i> Limpiar
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
require $_SERVER['DOCUMENT_ROOT'] . '/SGA-SEBANA/public/templates/base.html.php';
?>