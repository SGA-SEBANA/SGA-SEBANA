<?php
$title = "Nueva Categoría RRLL";
ob_start();
?>

<div class="row">
    <div class="col-lg-12">
        <div class="overview-wrap mb-4">
            <h2 class="title-1">Nueva Categoría RRLL</h2>
            <a href="/SGA-SEBANA/public/CategoriasRRLL" class="au-btn au-btn-icon au-btn--blue">
                <i class="zmdi zmdi-arrow-left"></i> Volver al listado
            </a>
        </div>

        <!-- ALERTAS -->
        <?php if (!empty($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="zmdi zmdi-check-circle"></i> <?= htmlspecialchars($_GET['success']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="zmdi zmdi-alert-triangle"></i> <?= htmlspecialchars($_GET['error']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- FORMULARIO -->
        <form action="/SGA-SEBANA/public/CategoriasRRLL/store" method="post" class="form-horizontal">
            <input type="hidden" name="_csrf_token" value="<?= $_SESSION['_csrf_token'] ?? '' ?>">

            <div class="card">
                <div class="card-header">
                    <strong>Formulario de Registro</strong>
                </div>
                <div class="card-body card-block">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="nombre" class="form-control-label">Nombre</label>
                            <input type="text" id="nombre" name="nombre" placeholder="Ej: Conflictos Colectivos"
                                   class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="descripcion" class="form-control-label">Descripción</label>
                            <textarea id="descripcion" name="descripcion" rows="2"
                                      placeholder="Breve descripción de la categoría"
                                      class="form-control"></textarea>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-right">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="zmdi zmdi-save"></i> Guardar Categoría
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