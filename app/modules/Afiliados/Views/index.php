<?php
/**
 * Vista de Listado de Afiliados
 */
ob_start();
?>

<div class="row">
    <div class="col-md-12">
        <h2 class="title-1 mb-4">Gestión de Afiliados</h2>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="zmdi zmdi-check-circle"></i> <?= $success ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- FILTROS -->
        <div class="card">
            <div class="card-header">
                <strong><i class="zmdi zmdi-filter-list"></i> Búsqueda y Filtros</strong>
            </div>
            <div class="card-body">
                <form action="/SGA-SEBANA/public/afiliados" method="GET">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="q" class="control-label mb-1">Buscar</label>
                                <input id="q" name="q" type="text" class="form-control"
                                    placeholder="Nombre o Cédula..."
                                    value="<?= htmlspecialchars($filtros['busqueda']) ?>">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="oficina" class="control-label mb-1">Oficina</label>
                                <select name="oficina_id" id="oficina" class="form-control">
                                    <option value="">Todas las Oficinas</option>
                                    <?php foreach ($oficinas as $of): ?>
                                        <option value="<?= $of['id'] ?>"
                                            <?= $filtros['oficina_id'] == $of['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($of['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="estado" class="control-label mb-1">Estado</label>
                                <select name="estado" id="estado" class="form-control">
                                    <option value="">Todos</option>
                                    <option value="activo"
                                        <?= $filtros['estado'] === 'activo' ? 'selected' : '' ?>>
                                        Activos
                                    </option>
                                    <option value="inactivo"
                                        <?= $filtros['estado'] === 'inactivo' ? 'selected' : '' ?>>
                                        Inactivos
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="zmdi zmdi-search"></i> Filtrar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- ACCIONES -->
        <div class="table-data__tool">
            <div class="table-data__tool-left"></div>
            <div class="table-data__tool-right">
                <a href="/SGA-SEBANA/public/afiliados/create"
                    class="au-btn au-btn-icon au-btn--green au-btn--small">
                    <i class="zmdi zmdi-plus"></i> Nuevo Afiliado
                </a>
            </div>
        </div>

        <!-- TABLA -->
        <div class="table-responsive table-responsive-data2">
            <table class="table table-data2">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Cédula</th>
                        <th>Oficina</th>
                        <th>Categoría</th>
                        <th>Teléfono</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (empty($afiliados)): ?>
                        <tr>
                            <td colspan="7" class="text-center p-4">
                                <p>No hay afiliados registrados aún.</p>
                                <a href="/SGA-SEBANA/public/afiliados/create"
                                    class="btn btn-primary btn-sm mt-2">
                                    Crear el primero
                                </a>
                            </td>
                        </tr>
                    <?php else: ?>

                        <?php foreach ($afiliados as $afiliado): ?>
                            <tr class="tr-shadow">
                                <td>
                                    <?= htmlspecialchars($afiliado['nombre_completo']) ?>
                                    <div class="small text-muted">
                                        <?= htmlspecialchars($afiliado['correo']) ?>
                                    </div>
                                </td>

                                <td>
                                    <span class="block-email">
                                        <?= htmlspecialchars($afiliado['cedula']) ?>
                                    </span>
                                </td>

                                <td><?= htmlspecialchars($afiliado['oficina_nombre'] ?? '-') ?></td>

                                <td>
                                    <?= !empty($afiliado['categoria_nombre'])
                                        ? htmlspecialchars($afiliado['categoria_nombre'])
                                        : '-' ?>
                                </td>

                                <td><?= htmlspecialchars($afiliado['telefono']) ?></td>

                                <td>
                                    <?php if ($afiliado['estado'] === 'activo'): ?>
                                        <span class="status--process">Activo</span>
                                    <?php else: ?>
                                        <span class="status--denied">Inactivo</span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <div class="table-data-feature">

                                        <!-- EDITAR -->
                                        <a href="/SGA-SEBANA/public/afiliados/edit/<?= $afiliado['id'] ?>"
                                            class="item"
                                            data-toggle="tooltip"
                                            title="Editar">
                                            <i class="zmdi zmdi-edit"></i>
                                        </a>

                                        <!-- DESACTIVAR / ACTIVAR -->
                                        <?php if ($afiliado['estado'] === 'activo'): ?>

                                            <!-- Ahora abre vista de baja -->
                                            <a href="/SGA-SEBANA/public/afiliados/desactivar/<?= $afiliado['id'] ?>"
                                                class="item"
                                                data-toggle="tooltip"
                                                title="Desactivar">
                                                <i class="zmdi zmdi-block"
                                                    style="color: #fa4251;"></i>
                                            </a>

                                        <?php else: ?>

                                            <!-- Activar normal -->
                                            <form action="/SGA-SEBANA/public/afiliados/toggle/<?= $afiliado['id'] ?>"
                                                method="post"
                                                style="display:inline;">
                                                <button type="submit"
                                                    class="item"
                                                    data-toggle="tooltip"
                                                    title="Activar">
                                                    <i class="zmdi zmdi-check"
                                                        style="color: #00b5e9;"></i>
                                                </button>
                                            </form>

                                        <?php endif; ?>

                                        <!-- GENERAR CARNÉ -->
                                        <?php if ($afiliado['estado'] === 'activo'): ?>
                                            <a href="/SGA-SEBANA/public/carnets/emitir/<?= $afiliado['id'] ?>"
                                                class="item"
                                                data-toggle="tooltip"
                                                title="Generar Carné">
                                                <i class="zmdi zmdi-card"></i>
                                            </a>
                                        <?php endif; ?>

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
