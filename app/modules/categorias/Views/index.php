<?php
/**
 * Vista de Listado de Categorías - Estilo SGA-SEBANA (Data Table 2)
 */
ob_start();
?>

<div class="row">
    <div class="col-md-12">
        <h2 class="title-1 mb-4">Gestión de Categorías</h2>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="zmdi zmdi-check-circle"></i> 
                <strong>¡Éxito!</strong> 
                <?php 
                    if($success === 'creado') echo "Categoría registrada correctamente.";
                    elseif($success === 'actualizado') echo "Categoría actualizada correctamente.";
                    elseif($success === 'estado_cambiado') echo "El estado de la categoría ha sido actualizado.";
                    elseif($success === 'eliminado_fisico') echo "Registro eliminado permanentemente del sistema.";
                    elseif($success === 'inactivado_por_asociacion') echo "La categoría tiene registros asociados; se ha marcado como Inactiva por seguridad.";
                    else echo "Operación realizada con éxito.";
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card mb-4 shadow-sm">
            <div class="card-header">
                <strong><i class="zmdi zmdi-filter-list"></i> Búsqueda y Filtros</strong>
            </div>
            <div class="card-body">
                <form action="/SGA-SEBANA/public/Categorias" method="GET">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="q" class="control-label mb-1">Buscar Categoría</label>
                                <input id="q" name="q" type="text" class="form-control"
                                    placeholder="Nombre o descripción..."
                                    value="<?= htmlspecialchars($filtros['q'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="estado" class="control-label mb-1">Estado</label>
                                <select name="estado" id="estado" class="form-control">
                                    <option value="">Todos los estados</option>
                                    <option value="activo" <?= ($filtros['estado'] ?? '') === 'activo' ? 'selected' : '' ?>>Activos</option>
                                    <option value="inactivo" <?= ($filtros['estado'] ?? '') === 'inactivo' ? 'selected' : '' ?>>Inactivos</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="tipo" class="control-label mb-1">Tipo</label>
                                <select name="tipo" id="tipo" class="form-control">
                                    <option value="">Todos los tipos</option>
                                    <option value="afiliado" <?= ($filtros['tipo'] ?? '') === 'afiliado' ? 'selected' : '' ?>>Afiliado</option>
                                    <option value="caso_rrll" <?= ($filtros['tipo'] ?? '') === 'caso_rrll' ? 'selected' : '' ?>>Caso RRLL</option>
                                    <option value="general" <?= ($filtros['tipo'] ?? '') === 'general' ? 'selected' : '' ?>>General</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary btn-block mb-1">
                                <i class="zmdi zmdi-search"></i> Filtrar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="table-data__tool">
            <div class="table-data__tool-left"></div>
            <div class="table-data__tool-right">
                <a href="/SGA-SEBANA/public/Categorias/create"
                    class="au-btn au-btn-icon au-btn--green au-btn--small">
                    <i class="zmdi zmdi-plus"></i> Nueva Categoría
                </a>
            </div>
        </div>

        <div class="table-responsive table-responsive-data2">
            <table class="table table-data2">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre de Categoría</th>
                        <th>Descripción</th>
                        <th>Tipo</th>
                        <th>Estado</th>
                        <th>Fecha Registro</th>
                        <th>Fecha Actualización</th>
                        <th class="text-right">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (empty($categorias)): ?>
                        <tr>
                            <td colspan="6" class="text-center p-5 bg-white">
                                <i class="zmdi zmdi-info-outline zmdi-hc-3x text-muted mb-3"></i>
                                <p>No se encontraron categorías con los filtros aplicados.</p>
                                <a href="/SGA-SEBANA/public/Categorias" class="btn btn-link btn-sm">Limpiar filtros</a>
                            </td>
                        </tr>
                    <?php else: ?>

                        <?php foreach ($categorias as $cat): ?>
                            <tr class="tr-shadow">
                                <td><?= $cat['id'] ?></td>
                                <td>
                                    <span class="block-email text-dark font-weight-bold">
                                        <?= htmlspecialchars($cat['nombre']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="desc"><?= htmlspecialchars($cat['descripcion'] ?: 'Sin descripción') ?></span>
                                </td>
                                <td>
                                    <span class="au-badge au-badge--blue px-2 py-1">
                                        <?= htmlspecialchars($cat['tipo'] ?? 'Sin tipo') ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($cat['estado'] === 'activo'): ?>
                                        <span class="status--process">Activo</span>
                                    <?php else: ?>
                                        <span class="status--denied">Inactivo</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $fecha = $cat['fecha_creacion'] ?? null;
                                    if ($fecha && strtotime($fecha)) {
                                        echo date('d/m/Y', strtotime($fecha));
                                    } else {
                                        echo 'Sin fecha';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    $fechaAct = $cat['fecha_actualizacion'] ?? null;
                                    if ($fechaAct && strtotime($fechaAct)) {
                                        echo date('d/m/Y H:i', strtotime($fechaAct));
                                    } else {
                                        echo 'Sin actualización';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <div class="table-data-feature">
                                        <a href="/SGA-SEBANA/public/Categorias/<?= $cat['id'] ?>/edit" 
                                           class="item" data-toggle="tooltip" title="Editar">
                                            <i class="zmdi zmdi-edit"></i>
                                        </a>
                                        <form action="/SGA-SEBANA/public/Categorias/<?= $cat['id'] ?>/toggle" method="POST" style="display:inline;">
                                            <?php if ($cat['estado'] === 'activo'): ?>
                                                <button type="submit" class="item" data-toggle="tooltip" title="Desactivar" onclick="return confirm('¿Inactivar categoría?');">
                                                    <i class="zmdi zmdi-block" style="color: #fa4251;"></i>
                                                </button>
                                            <?php else: ?>
                                                <button type="submit" class="item" data-toggle="tooltip" title="Activar">
                                                    <i class="zmdi zmdi-check" style="color: #00b5e9;"></i>
                                                </button>
                                            <?php endif; ?>
                                        </form>
                                        <a href="/SGA-SEBANA/public/Categorias/<?= $cat['id'] ?>/show" 
                                           class="item" data-toggle="tooltip" title="Ver Detalles">
                                            <i class="zmdi zmdi-more"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <tr class="spacer"></tr>
                        <?php endforeach; ?>

                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require $_SERVER['DOCUMENT_ROOT'] . '/SGA-SEBANA/public/templates/base.html.php';
?>