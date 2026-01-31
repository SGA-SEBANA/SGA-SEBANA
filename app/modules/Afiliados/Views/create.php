<?php
/**
 * Vista de Creación de Afiliados (HU-AF-01)
 * Usa la plantilla base del sistema
 */
ob_start(); // 1. INICIO DE LA GRABACIÓN
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
        <div class="card">
            <div class="card-header">
                <strong>Formulario de Registro</strong>
            </div>
            <div class="card-body card-block">
                
                <form action="/SGA-SEBANA/public/afiliados/store" method="post" enctype="multipart/form-data" class="form-horizontal">
                    
                    <input type="hidden" name="_csrf_token" value="<?= $_SESSION['_csrf_token'] ?? '' ?>">

                    <div class="row mb-3">
                        <div class="col col-md-3">
                            <label for="nombre" class="form-control-label">Nombre Completo</label>
                        </div>
                        <div class="col-12 col-md-9">
                            <input type="text" id="nombre" name="nombre_completo" placeholder="Ingrese nombre y apellidos" class="form-control" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col col-md-3">
                            <label for="cedula" class="form-control-label">Cédula</label>
                        </div>
                        <div class="col-12 col-md-9">
                            <input type="text" id="cedula" name="cedula" placeholder="000000000 (9 dígitos)" class="form-control" pattern="\d{9}" title="9 dígitos seguidos" required>
                            <small class="form-text text-muted">Sin guiones ni espacios</small>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col col-md-3">
                            <label for="empleado" class="form-control-label">N° de Empleado</label>
                        </div>
                        <div class="col-12 col-md-9">
                            <input type="text" id="empleado" name="numero_empleado" placeholder="Ej: 12345" class="form-control" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col col-md-3">
                            <label class="form-control-label">Género</label>
                        </div>
                        <div class="col col-md-9">
                            <div class="form-check form-check-inline">
                                <input type="radio" id="g_masc" name="genero" value="Masculino" class="form-check-input" required>
                                <label for="g_masc" class="form-check-label">Masculino</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input type="radio" id="g_fem" name="genero" value="Femenino" class="form-check-input">
                                <label for="g_fem" class="form-check-label">Femenino</label>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col col-md-3">
                            <label for="fecha_nac" class="form-control-label">Fecha Nacimiento</label>
                        </div>
                        <div class="col-12 col-md-9">
                            <input type="date" id="fecha_nac" name="fecha_nacimiento" class="form-control" required>
                        </div>
                    </div>

                    <hr>

                    <div class="row mb-3">
                        <div class="col col-md-3">
                            <label for="oficina" class="form-control-label">Oficina BN</label>
                        </div>
                        <div class="col-12 col-md-9">
                            <input type="text" id="oficina" name="oficina_nombre" placeholder="Nombre de la oficina" class="form-control" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col col-md-3">
                            <label for="num_oficina" class="form-control-label">Código Oficina</label>
                        </div>
                        <div class="col-12 col-md-9">
                            <input type="text" id="num_oficina" name="oficina_numero" placeholder="Ej: 050" class="form-control" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col col-md-3">
                            <label for="categoria" class="form-control-label">Categoría RRHH</label>
                        </div>
                        <div class="col-12 col-md-9">
                            <input type="text" id="categoria" name="categoria" placeholder="Ej: Profesional 1" class="form-control" required>
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
                                <input type="email" id="email_inst" name="email_institucional" placeholder="usuario@bncr.fi.cr" class="form-control">
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
                                <input type="text" id="celular" name="celular_personal" placeholder="8888-8888" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="zmdi zmdi-save"></i> Registrar Afiliado
                        </button>
                        <button type="reset" class="btn btn-danger btn-sm">
                            <i class="zmdi zmdi-refresh-alt"></i> Limpiar
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<?php
// 2. FIN DE GRABACIÓN Y CARGA DE LA PLANTILLA MAESTRA
$content = ob_get_clean();
require $_SERVER['DOCUMENT_ROOT'] . '/SGA-SEBANA/public/templates/base.html.php';
?>