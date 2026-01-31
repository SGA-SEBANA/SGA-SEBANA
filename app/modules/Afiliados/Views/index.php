<?php
/**
 * Vista de Listado de Afiliados
 * Completada: Listar, Editar enlace y Botón de Estado (HU-AF-04)
 */
ob_start();
?>

<div class="row">
    <div class="col-md-12">
        <div class="overview-wrap mb-4">
            <h2 class="title-1">Gestión de Afiliados</h2>
            <a href="/SGA-SEBANA/public/afiliados/create" class="au-btn au-btn-icon au-btn--green">
                <i class="zmdi zmdi-plus"></i> Nuevo Afiliado
            </a>
        </div>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="zmdi zmdi-check-circle"></i> <?= $success ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="table-responsive table-responsive-data2">
            <table class="table table-data2">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Cédula</th>
                        <th>Empleado #</th>
                        <th>Oficina</th>
                        <th>Celular</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($afiliados)): ?>
                        <tr>
                            <td colspan="7" class="text-center">No hay afiliados registrados aún.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($afiliados as $afiliado): ?>
                            <tr class="tr-shadow">
                                <td><?= htmlspecialchars($afiliado['nombre_completo']) ?></td>
                                <td>
                                    <span class="block-email"><?= htmlspecialchars($afiliado['cedula']) ?></span>
                                </td>
                                <td><?= htmlspecialchars($afiliado['numero_empleado']) ?></td>
                                <td class="desc"><?= htmlspecialchars($afiliado['oficina_nombre']) ?></td>
                                <td><?= htmlspecialchars($afiliado['celular_personal']) ?></td>
                                <td>
                                    <?php if ($afiliado['estado'] === 'activo'): ?>
                                        <span class="status--process">Activo</span>
                                    <?php else: ?>
                                        <span class="status--denied">Inactivo</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="table-data-feature">
                                        <a href="/SGA-SEBANA/public/afiliados/edit/<?= $afiliado['id'] ?>" class="item" data-toggle="tooltip" data-placement="top" title="Editar">
                                            <i class="zmdi zmdi-edit"></i>
                                        </a>
                                        
                                        <form action="/SGA-SEBANA/public/afiliados/toggle/<?= $afiliado['id'] ?>" method="post" style="display:inline;">
                                            <button type="submit" class="item" data-toggle="tooltip" data-placement="top" 
                                                    title="<?= $afiliado['estado'] === 'activo' ? 'Desactivar' : 'Activar' ?>"
                                                    onclick="return confirm('¿Estás seguro de cambiar el estado de este afiliado?')">
                                                
                                                <?php if ($afiliado['estado'] === 'activo'): ?>
                                                    <i class="zmdi zmdi-block" style="color: #fa4251;"></i> <?php else: ?>
                                                    <i class="zmdi zmdi-check" style="color: #00b5e9;"></i> <?php endif; ?>
                                            
                                            </button>
                                        </form>

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