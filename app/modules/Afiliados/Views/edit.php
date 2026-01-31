<?php
/**
 * Vista de Edición de Afiliados (HU-AF-03)
 * Formulario pre-llenado con datos existentes
 */
ob_start();
?>

<div class="row">
    <div class="col-lg-12">
        <div class="overview-wrap mb-4">
            <h2 class="title-1">Editar Afiliado: <?= htmlspecialchars($afiliado['nombre_completo']) ?></h2>
            <a href="/SGA-SEBANA/public/afiliados" class="au-btn au-btn-icon au-btn--blue">
                <i class="zmdi zmdi-arrow-left"></i> Cancelar y Volver
            </a>
        </div>

        <div class="card">
            <div class="card-header">
                <strong>Actualizar Datos</strong>
            </div>
            <div class="card-body card-block">
                
                <form action="/SGA-SEBANA/public/afiliados/update/<?= $afiliado['id'] ?>" method="post" enctype="multipart/form-data" class="form-horizontal">
                    
                    <input type="hidden" name="_csrf_token" value="<?= $_SESSION['_csrf_token'] ?? '' ?>">

                    <div class="row mb-3">
                        <div class="col col-md-3">
                            <label for="nombre" class="form-control-label">Nombre Completo</label>
                        </div>
                        <div class="col-12 col-md-9">
                            <input type="text" id="nombre" name="nombre_completo" 
                                   value="<?= htmlspecialchars($afiliado['nombre_completo']) ?>" 
                                   class="form-control" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col col-md-3">
                            <label for="cedula" class="form-control-label">Cédula</label>
                        </div>
                        <div class="col-12 col-md-9">
                            <input type="text" id="cedula" name="cedula" 
                                   value="<?= htmlspecialchars($afiliado['cedula']) ?>" 
                                   class="form-control" pattern="\d{9}" title="9 dígitos seguidos" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col col-md-3">
                            <label for="empleado" class="form-control-label">N° de Empleado</label>
                        </div>
                        <div class="col-12 col-md-9">
                            <input type="text" id="empleado" name="numero_empleado" 
                                   value="<?= htmlspecialchars($afiliado['numero_empleado']) ?>" 
                                   class="form-control" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col col-md-3">
                            <label class="form-control-label">Género</label>
                        </div>
                        <div class="col col-md-9">
                            <div class="form-check form-check-inline">
                                <input type="radio" id="g_masc" name="genero" value="Masculino" 
                                       class="form-check-input" <?= $afiliado['genero'] == 'Masculino' ? 'checked' : '' ?> required>
                                <label for="g_masc" class="form-check-label">Masculino</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input type="radio" id="g_fem" name="genero" value="Femenino" 
                                       class="form-check-input" <?= $afiliado['genero'] == 'Femenino' ? 'checked' : '' ?>>
                                <label for="g_fem" class="form-check-label">Femenino</label>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col col-md-3">
                            <label for="fecha_nac" class="form-control-label">Fecha Nacimiento</label>
                        </div>
                        <div class="col-12 col-md-9">
                            <input type="date" id="fecha_nac" name="fecha_nacimiento" 
                                   value="<?= htmlspecialchars($afiliado['fecha_nacimiento']) ?>" 
                                   class="form-control" required>
                        </div>
                    </div>

                    <hr>

                    <div class="row mb-3">
                        <div class="col col-md-3">
                            <label for="oficina" class="form-control-label">Oficina BN</label>
                        </div>
                        <div class="col-12 col-md-9">
                            <input type="text" id="oficina" name="oficina_nombre" 
                                   value="<?= htmlspecialchars($afiliado['oficina_nombre']) ?>" 
                                   class="form-control" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col col-md-3">
                            <label for="num_oficina" class="form-control-label">Código Oficina</label>
                        </div>
                        <div class="col-12 col-md-9">
                            <input type="text" id="num_oficina" name="oficina_numero" 
                                   value="<?= htmlspecialchars($afiliado['oficina_numero']) ?>" 
                                   class="form-control" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col col-md-3">
                            <label for="categoria" class="form-control-label">Categoría RRHH</label>
                        </div>
                        <div class="col-12 col-md-9">
                            <input type="text" id="categoria" name="categoria" 
                                   value="<?= htmlspecialchars($afiliado['categoria']) ?>" 
                                   class="form-control" required>
                        </div>
                    </div>

                    <hr>

                    <div class="row mb-3">
                        <div class="col col-md-3">
                            <label for="email_inst" class="form-control-label">Email Institucional</label>
                        </div>
                        <div class="col-12 col-md-9">
                            <div class="input-group">
                                <span class="input-group-text"><i class="zmdi zmdi-email"></i></span>
                                <input type="email" id="email_inst" name="email_institucional" 
                                       value="<?= htmlspecialchars($afiliado['email_institucional']) ?>" 
                                       class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col col-md-3">
                            <label for="celular" class="form-control-label">Celular Personal</label>
                        </div>
                        <div class="col-12 col-md-9">
                            <div class="input-group">
                                <span class="input-group-text"><i class="zmdi zmdi-phone"></i></span>
                                <input type="text" id="celular" name="celular_personal" 
                                       value="<?= htmlspecialchars($afiliado['celular_personal']) ?>" 
                                       class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="zmdi zmdi-edit"></i> Guardar Cambios
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require $_SERVER['DOCUMENT_ROOT'] . '/SGA-SEBANA/public/templates/base.html.php';
?>