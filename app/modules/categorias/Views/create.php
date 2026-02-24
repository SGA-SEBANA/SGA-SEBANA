<?php
/**
 * Vista de Creación de Categorías - Estilo SGA-SEBANA
 */
ob_start();
?>

<div class="row">
    <div class="col-lg-12">
        <div class="overview-wrap mb-4">
            <h2 class="title-1">Nueva Categoría</h2>
            <a href="/SGA-SEBANA/public/Categorias" class="au-btn au-btn-icon au-btn--blue">
                <i class="zmdi zmdi-arrow-left"></i> Volver a la lista
            </a>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="zmdi zmdi-alert-triangle"></i> 
                <strong>¡Atención!</strong> 
                <?php 
                    if($_GET['error'] === 'duplicado') echo "Ya existe una categoría con ese nombre.";
                    elseif($_GET['error'] === 'vacio') echo "El nombre es un campo obligatorio.";
                    else echo "Error al procesar la solicitud en la base de datos.";
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form action="/SGA-SEBANA/public/Categorias/store" method="post" class="form-horizontal">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <strong><i class="zmdi zmdi-assignment"></i> Formulario de Registro</strong>
                </div>
                <div class="card-body card-block">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nombre" class="form-control-label font-weight-bold">Nombre de Categoría <span class="text-danger">*</span></label>
                                <input type="text" id="nombre" name="nombre" placeholder="Ej: Electrónicos, Repuestos..." 
                                       class="form-control" required autofocus>
                                <small class="form-text text-muted">Este nombre debe ser único para evitar conflictos en el sistema.</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="tipo" class="form-control-label font-weight-bold">Tipo <span class="text-danger">*</span></label>
                                <select id="tipo" name="tipo" class="form-control" required>
                                    <option value="afiliado">Afiliado</option>
                                    <option value="caso_rrll">Caso RRLL</option>
                                    <option value="general">General</option>
                                </select>
                                <small class="form-text text-muted">Selecciona el tipo de categoría.</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-control-label">Estado Inicial</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-success"><i class="zmdi zmdi-check-circle"></i></span>
                                    <input type="text" class="form-control" value="Activo (Predeterminado)" readonly disabled>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="descripcion" class="form-control-label font-weight-bold">Descripción / Notas</label>
                                <textarea name="descripcion" id="descripcion" rows="5" 
                                          placeholder="Escribe aquí una breve descripción de lo que incluye esta categoría..." 
                                          class="form-control"></textarea>
                                <small class="form-text text-muted">Opcional: Detalla el uso de esta categoría para otros usuarios.</small>
                            </div>
                        </div>
                    </div>

                </div>
                
                <div class="card-footer bg-white text-right py-3">
                    <button type="reset" class="btn btn-outline-danger btn-sm px-4 mr-2">
                        <i class="zmdi zmdi-refresh-alt"></i> Limpiar Formulario
                    </button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 shadow-sm">
                        <i class="zmdi zmdi-save"></i> Guardar Categoría
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