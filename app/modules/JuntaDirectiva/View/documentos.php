<?php
/**
 * Vista de Lista de Documentos
 */
ob_start();
?>

<div class="row">
    <div class="col-lg-12">
        <!-- HEADER -->
        <div class="overview-wrap mb-4">
            <h2 class="title-1">Documentos: <?= htmlspecialchars($miembro['nombre'] ?? 'Miembro') ?></h2>
            <a href="/SGA-SEBANA/public/junta" class="au-btn au-btn-icon au-btn--blue">
                <i class="zmdi zmdi-arrow-left"></i> Volver a la lista
            </a>
        </div>

        <div class="card">
            <div class="card-header">
                <strong>Archivos Adjuntos</strong>
            </div>
            <div class="card-body">
                <?php if (empty($documentos)): ?>
                    <div class="alert alert-info">
                        No hay documentos adjuntos para este miembro.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-borderless table-striped table-earning">
                            <thead>
                                <tr>
                                    <th>Nombre del Archivo</th>
                                    <th class="text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($documentos as $doc): ?>
                                    <tr>
                                        <td>
                                            <i class="zmdi zmdi-file-text mr-2"></i>
                                            <?= htmlspecialchars($doc['nombre_original']) ?>
                                        </td>
                                        <td class="text-right">
                                            <!-- Ver / Descargar -->
                                            <a href="/SGA-SEBANA/public/junta/ver-documento/<?= $doc['id'] ?>" target="_blank"
                                                class="btn btn-secondary btn-sm" title="Ver archivo">
                                                <i class="zmdi zmdi-eye"></i>
                                            </a>

                                            <!-- Eliminar -->
                                            <a href="/SGA-SEBANA/public/junta/eliminar-documento/<?= $doc['id'] ?>"
                                                class="btn btn-danger btn-sm"
                                                onclick="return confirm('¿Está seguro de eliminar este documento? Esta acción no se puede deshacer.')"
                                                title="Eliminar">
                                                <i class="zmdi zmdi-delete"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
            <div class="card-footer">
                <a href="/SGA-SEBANA/public/junta/edit/<?= $miembro['id'] ?>" class="btn btn-primary btn-sm">
                    <i class="zmdi zmdi-plus"></i> Agregar más documentos
                </a>
            </div>
        </div>

    </div>
</div>

<?php
$content = ob_get_clean();
require BASE_PATH . '/public/templates/base.html.php';
