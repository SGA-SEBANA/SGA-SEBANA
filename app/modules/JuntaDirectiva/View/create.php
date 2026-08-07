<?php
/**
 * Vista de Creación de Miembro Junta
 */
ob_start();
?>

<div class="row">
    <div class="col-lg-12">
        <div class="overview-wrap mb-4">
            <h2 class="title-1">Nuevo Miembro Junta</h2>
            <a href="/SGA-SEBANA/public/junta" class="au-btn au-btn-icon au-btn--blue">
                <i class="zmdi zmdi-arrow-left"></i> Volver a la lista
            </a>
        </div>

        <?php if(!empty($error)): ?>
            <div class="alert alert-danger" role="alert">
                <?= htmlspecialchars((string) $error) ?>
            </div>
        <?php endif; ?>
        
        <form action="/SGA-SEBANA/public/junta/create" method="post" enctype="multipart/form-data"
            class="form-horizontal">

            <div class="card">
                <div class="card-header">
                    <strong>Datos del Miembro</strong>
                </div>
                <div class="card-body card-block">

                    <div class="row mb-3">                      
                        <div class="col-md-6">
                            <label for="afiliado_id" class="form-control-label">Afiliado</label>
                            <select name="afiliado_id" id="afiliado_id" class="form-control" required>
                                <?php foreach ($afiliados as $afiliado): ?>
                                    <option value="<?= $afiliado['id'] ?>">
                                        <?= htmlspecialchars($afiliado['nombre_completo']) ?> -
                                        <?= htmlspecialchars($afiliado['cedula']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="cargo" class="form-control-label">Cargo</label>
                            <select id="cargo" name="cargo" class="form-control" required>
                                <option value="">Seleccione un cargo</option>
                                <?php foreach (($cargosDisponibles ?? []) as $cargo): ?>
                                    <option value="<?= htmlspecialchars((string) $cargo) ?>">
                                        <?= htmlspecialchars((string) $cargo) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="estado" class="form-control-label">Estado</label>
                            <select name="estado" id="estado" class="form-control" required>
                                <option value="vigente">Vigente</option>
                                <option value="suspendido">Suspendido</option>
                                <option value="finalizado">Finalizado</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="fecha_inicio" class="form-control-label">Fecha Inicio</label>
                            <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label for="fecha_fin" class="form-control-label">Fecha Fin (Estimada)</label>
                            <input type="date" id="fecha_fin" name="fecha_fin" class="form-control">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="periodo" class="form-control-label">Periodo</label>
                            <input type="text" id="periodo" name="periodo" placeholder="Ej: 2026-2029"
                                class="form-control" maxlength="9" readonly>
                            <small class="form-text text-muted">Se calcula automaticamente por trienios (2026-2029, 2029-2032, etc.).</small>
                        </div>
                    </div>

                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <strong><i class="zmdi zmdi-assignment"></i> Detalles Adicionales</strong>
                </div>
                <div class="card-body card-block">

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="responsabilidades" class="form-control-label">Responsabilidades</label>
                            <textarea name="responsabilidades" id="responsabilidades" rows="3" class="form-control"
                                maxlength="500" placeholder="Describa las responsabilidades..."></textarea>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="documentos" class="form-control-label">Documentos (Actas, Nombramiento,
                                etc)</label>
                            <input type="file" name="documentos[]" accept=".pdf,.jpg,.jpeg,.png" multiple class="form-control">
                            <small class="form-text text-muted" >Formatos permitidos: PDF, JPG, PNG</small>
                        </div>
                        <div class="col-md-6">
                            <label for="observaciones" class="form-control-label">Observaciones</label>
                            <input type="text" name="observaciones" id="observaciones" class="form-control"
                               maxlength="200" placeholder="Notas adicionales...">
                        </div>
                    </div>

                </div>
                <div class="card-footer text-right">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="zmdi zmdi-save"></i> Registrar Miembro
                    </button>
                    <button type="reset" class="btn btn-danger btn-sm">
                        <i class="zmdi zmdi-refresh-alt"></i> Limpiar
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>

<script>
    (function () {
        const fechaInput = document.getElementById('fecha_inicio');
        const periodoInput = document.getElementById('periodo');

        const calcularPeriodo = (fecha) => {
            if (!fecha) return '';
            const year = parseInt(fecha.substring(0, 4), 10);
            if (Number.isNaN(year)) return '';

            const base = 2026;
            if (year < base) {
                return `${year}-${year + 3}`;
            }

            const inicio = base + Math.floor((year - base) / 3) * 3;
            return `${inicio}-${inicio + 3}`;
        };

        const refrescar = () => {
            periodoInput.value = calcularPeriodo(fechaInput.value);
        };

        fechaInput.addEventListener('change', refrescar);
        fechaInput.addEventListener('input', refrescar);
    })();
</script>

<?php
$content = ob_get_clean();
require $_SERVER['DOCUMENT_ROOT'] . '/SGA-SEBANA/public/templates/base.html.php';
?>
