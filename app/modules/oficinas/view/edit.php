<?php
ob_start();
?>

<div class="row">
    <div class="col-lg-12">
        <div class="overview-wrap mb-4">
            <h2 class="title-1">Editar Oficina</h2>
            <a href="/SGA-SEBANA/public/oficinas" class="au-btn au-btn-icon au-btn--blue">
                <i class="zmdi zmdi-arrow-left"></i> Volver a la lista
            </a>
        </div>

       <form method="post" action="/SGA-SEBANA/public/oficinas/edit/<?= $office['id'] ?>">
            <?= \App\Modules\Usuarios\Helpers\SecurityHelper::csrfField() ?>

  
            <div class="card">
                <div class="card-header">
                    <strong><?= htmlspecialchars($office['nombre'] ?? 'Oficina') ?></strong>
                </div>
                <div class="card-body card-block">

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label>Código</label>
                            <input type="text" name="codigo"
                                value="<?= htmlspecialchars($office['codigo'] ?? '') ?>"
                                class="form-control" required>
                        </div>

                        <div class="col-md-8">
                            <label>Nombre</label>
                            <input type="text" name="nombre"
                                value="<?= htmlspecialchars($office['nombre'] ?? '') ?>"
                                class="form-control" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label>Dirección</label>
                            <input type="text" name="direccion"
                                value="<?= htmlspecialchars($office['direccion'] ?? '') ?>"
                                class="form-control">
                        </div>
                    </div>

                </div>
            </div>

        
            <div class="card">
                <div class="card-header">
                    <strong>Ubicación</strong>
                </div>
                <div class="card-body card-block">

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label>Provincia</label>
                            <input type="text" name="provincia"
                                value="<?= htmlspecialchars($office['provincia'] ?? '') ?>"
                                class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label>Cantón</label>
                            <input type="text" name="canton"
                                value="<?= htmlspecialchars($office['canton'] ?? '') ?>"
                                class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label>Distrito</label>
                            <input type="text" name="distrito"
                                value="<?= htmlspecialchars($office['distrito'] ?? '') ?>"
                                class="form-control">
                        </div>
                    </div>

                </div>
            </div>

 
            <div class="card">
                <div class="card-header">
                    <strong>Contacto</strong>
                </div>
                <div class="card-body card-block">

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label>Teléfono</label>
                            <input type="text" name="telefono"
                                value="<?= htmlspecialchars($office['telefono'] ?? '') ?>"
                                class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label>Correo</label>
                            <input type="email" name="correo"
                                value="<?= htmlspecialchars($office['correo'] ?? '') ?>"
                                class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label>Horario</label>
                            <input type="text" name="horario_atencion"
                                value="<?= htmlspecialchars($office['horario_atencion'] ?? '') ?>"
                                class="form-control">
                        </div>
                    </div>

                </div>
            </div>


            <div class="card">
                <div class="card-header">
                    <strong>Responsable</strong>
                </div>
                <div class="card-body card-block">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>Responsable</label>
                            <input type="text" name="responsable"
                                value="<?= htmlspecialchars($office['responsable'] ?? '') ?>"
                                class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label>Coordenadas GPS</label>
                            <input type="text" name="coordenadas_gps"
                                value="<?= htmlspecialchars($office['coordenadas_gps'] ?? '') ?>"
                                class="form-control">
                        </div>
                    </div>

                </div>
            </div>

    
            <div class="card">
                <div class="card-header">
                    <strong>Información adicional</strong>
                </div>
                <div class="card-body card-block">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>Estado</label>
                            <select name="activo" class="form-control">
                                <option value="1" <?= ($office['activo'] ?? 1) == 1 ? 'selected' : '' ?>>Activo</option>
                                <option value="0" <?= ($office['activo'] ?? 1) == 0 ? 'selected' : '' ?>>Inactivo</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label>Observaciones</label>
                            <input type="text" name="observaciones"
                                value="<?= htmlspecialchars($office['observaciones'] ?? '') ?>"
                                class="form-control">
                        </div>
                    </div>

                </div>

                <div class="card-footer text-right">
                    <button type="submit" class="btn btn-primary btn-sm">
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
