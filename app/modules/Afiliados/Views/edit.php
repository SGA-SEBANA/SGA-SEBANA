<?php
/**
 * Vista de Edición de Afiliados
 */
ob_start();

// Decodificar contacto de emergencia si existe
$contactoEmergencia = json_decode($afiliado['datos_contacto_emergencia'] ?? '{}', true);
?>

<div class="row">
    <div class="col-lg-12">
        <div class="overview-wrap mb-4">
            <h2 class="title-1">Editar Afiliado: <?= htmlspecialchars($afiliado['nombre']) ?></h2>
            <a href="/SGA-SEBANA/public/afiliados" class="au-btn au-btn-icon au-btn--blue">
                <i class="zmdi zmdi-arrow-left"></i> Volver a la lista
            </a>
        </div>

        <form action="/SGA-SEBANA/public/afiliados/update/<?= $afiliado['id'] ?>" method="post"
            enctype="multipart/form-data" class="form-horizontal">
            <input type="hidden" name="_csrf_token" value="<?= $_SESSION['_csrf_token'] ?? '' ?>">

            <!-- SECCIÓN 1: DATOS PERSONALES -->
            <div class="card">
                <div class="card-header">
                    <strong><i class="zmdi zmdi-account"></i> Datos Personales</strong>
                </div>
                <div class="card-body card-block">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="nombre" class="form-control-label">Nombre</label>
                            <input type="text" id="nombre" name="nombre" class="form-control"
                                value="<?= htmlspecialchars($afiliado['nombre']) ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label for="apellido1" class="form-control-label">Primer Apellido</label>
                            <input type="text" id="apellido1" name="apellido1" class="form-control"
                                value="<?= htmlspecialchars($afiliado['apellido1']) ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label for="apellido2" class="form-control-label">Segundo Apellido</label>
                            <input type="text" id="apellido2" name="apellido2" class="form-control"
                                value="<?= htmlspecialchars($afiliado['apellido2']) ?>">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="cedula" class="form-control-label">Cédula</label>
                            <input type="text" id="cedula" name="cedula" class="form-control" pattern="\d{9,12}"
                                value="<?= htmlspecialchars($afiliado['cedula']) ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label for="fecha_nac" class="form-control-label">Fecha Nacimiento</label>
                            <input type="date" id="fecha_nac" name="fecha_nacimiento" class="form-control"
                                value="<?= htmlspecialchars($afiliado['fecha_nacimiento']) ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-control-label">Género</label>
                            <select name="genero" class="form-control">
                                <option value="Masculino" <?= strtolower($afiliado['genero']) == 'masculino' ? 'selected' : '' ?>>Masculino</option>
                                <option value="Femenino" <?= strtolower($afiliado['genero']) == 'femenino' ? 'selected' : '' ?>>Femenino</option>
                                <option value="Otro" <?= strtolower($afiliado['genero']) == 'otro' ? 'selected' : '' ?>>
                                    Otro</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECCIÓN 2: CONTACTO Y UBICACIÓN -->
            <div class="card">
                <div class="card-header">
                    <strong><i class="zmdi zmdi-pin"></i> Contacto y Ubicación</strong>
                </div>
                <div class="card-body card-block">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="correo" class="form-control-label">Correo Institucional/Personal</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="zmdi zmdi-email"></i></span>
                                <input type="email" id="correo" name="correo" class="form-control"
                                    value="<?= htmlspecialchars($afiliado['correo']) ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="telefono" class="form-control-label">Teléfono Principal</label>
                            <input type="text" id="telefono" name="telefono" class="form-control"
                                value="<?= htmlspecialchars($afiliado['telefono']) ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="telefono_secundario" class="form-control-label">Teléfono Secundario</label>
                            <input type="text" id="telefono_secundario" name="telefono_secundario" class="form-control"
                                value="<?= htmlspecialchars($afiliado['telefono_secundario'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12">
                            <label for="direccion" class="form-control-label">Dirección Domicilio</label>
                            <textarea name="direccion" id="direccion" rows="2"
                                class="form-control"><?= htmlspecialchars($afiliado['direccion'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECCIÓN 3: INFORMACIÓN LABORAL -->
            <div class="card">
                <div class="card-header">
                    <strong><i class="zmdi zmdi-case"></i> Información Laboral</strong>
                </div>
                <div class="card-body card-block">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="oficina_id" class="form-control-label">Oficina BN</label>
                            <select name="oficina_id" id="oficina_id" class="form-control">
                                <option value="">Seleccione Oficina...</option>
                                <?php foreach ($oficinas as $of): ?>
                                    <option value="<?= $of['id'] ?>" <?= ($afiliado['oficina_id'] == $of['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($of['nombre']) ?> - <?= $of['codigo'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="puesto_actual" class="form-control-label">Puesto Actual</label>
                            <input type="text" name="puesto_actual" id="puesto_actual" class="form-control"
                                value="<?= htmlspecialchars($afiliado['puesto_actual'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="categoria_id" class="form-control-label">Categoría Afiliación</label>
                            <select name="categoria_id" id="categoria_id" class="form-control">
                                <option value="">Seleccione Categoría...</option>
                                <?php foreach ($categorias as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" <?= ($afiliado['categoria_id'] == $cat['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECCIÓN 4: EMERGENCIA Y OBSERVACIONES -->
            <div class="card">
                <div class="card-header">
                    <strong><i class="zmdi zmdi-alert-circle"></i> En Caso de Emergencia</strong>
                </div>
                <div class="card-body card-block">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="emergencia_nombre" class="form-control-label">Nombre Contacto</label>
                            <input type="text" name="emergencia_nombre" id="emergencia_nombre" class="form-control"
                                value="<?= htmlspecialchars($contactoEmergencia['nombre'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="emergencia_telefono" class="form-control-label">Teléfono Contacto</label>
                            <input type="text" name="emergencia_telefono" id="emergencia_telefono" class="form-control"
                                value="<?= htmlspecialchars($contactoEmergencia['telefono'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="emergencia_relacion" class="form-control-label">Relación (Parentesco)</label>
                            <input type="text" name="emergencia_relacion" id="emergencia_relacion" class="form-control"
                                value="<?= htmlspecialchars($contactoEmergencia['relacion'] ?? '') ?>">
                        </div>
                    </div>
                    <hr>
                    <div class="row mb-3">
                        <div class="col-12">
                            <label for="observaciones" class="form-control-label">Observaciones Generales</label>
                            <textarea name="observaciones" id="observaciones" rows="3"
                                class="form-control"><?= htmlspecialchars($afiliado['observaciones'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-right">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="zmdi zmdi-save"></i> Guardar Cambios
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