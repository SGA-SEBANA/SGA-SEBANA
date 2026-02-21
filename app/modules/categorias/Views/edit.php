<?php
/**
 * Vista de Edición de Categorías - Estilo SGA-SEBANA (Data Table 2 Refined)
 */
ob_start();
?>

<div class="row">
    <div class="col-lg-12">
        <div class="overview-wrap mb-4">
            <h2 class="title-1">Editar Categoría</h2>
            <a href="/SGA-SEBANA/public/categorias" class="au-btn au-btn-icon au-btn--blue">
                <i class="zmdi zmdi-arrow-left"></i> Volver a la lista
            </a>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="zmdi zmdi-alert-triangle"></i> 
                <strong>¡Error!</strong> 
                <?php 
                    if($_GET['error'] === 'duplicado') echo "Ya existe otra categoría con ese nombre.";
                    elseif($_GET['error'] === 'vacio') echo "El nombre no puede quedar vacío.";
                    else echo "No se pudo actualizar la categoría en la base de datos.";
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form action="/SGA-SEBANA/public/categorias/<?= $categoria['id'] ?>/update" method="post" class="form-horizontal">
            <input type="hidden" name="_csrf_token" value="<?= $_SESSION['_csrf_token'] ?? '' ?>">

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <strong><i class="zmdi zmdi-edit"></i> Modificar Información del Registro #<?= $categoria['id'] ?></strong>
                </div>
                
                <div class="card-body card-block">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nombre" class="form-control-label font-weight-bold">Nombre de Categoría <span class="text-danger">*</span></label>
                                <input type="text" id="nombre" name="nombre" 
                                       value="<?= htmlspecialchars($categoria['nombre']) ?>" 
                                       class="form-control" required autofocus>
                                <small class="form-text text-muted">Asegúrate de que el nombre siga siendo descriptivo y único.</small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-control-label">Estado Actual</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light <?= $categoria['estado'] === 'activo' ? 'text-success' : 'text-danger' ?>">
                                        <i class="zmdi <?= $categoria['estado'] === 'activo' ? 'zmdi-check-circle' : 'zmdi-block' ?>"></i>
                                    </span>
                                    <input type="text" class="form-control" 
                                           value="<?= ucfirst($categoria['estado']) ?>" readonly disabled>
                                </div>
                                <small class="form-text text-muted">El estado se gestiona desde el listado principal.</small>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="descripcion" class="form-control-label font-weight-bold">Descripción / Notas</label>
                                <textarea name="descripcion" id="descripcion" rows="5" 
                                          class="form-control"><?= htmlspecialchars($categoria['descripcion']) ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer bg-white text-right py-3">
                    <a href="/SGA-SEBANA/public/categorias" class="btn btn-outline-danger btn-sm px-4 mr-2">
                        <i class="zmdi zmdi-close"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary btn-sm px-4 shadow-sm">
                        <i class="zmdi zmdi-save"></i> Guardar Cambios
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
// Integración con la plantilla base oficial
require $_SERVER['DOCUMENT_ROOT'] . '/SGA-SEBANA/public/templates/base.html.php';
?>