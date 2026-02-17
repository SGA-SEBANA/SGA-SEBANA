<?php ob_start(); ?>

<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header">
                <strong>Desactivar Afiliado</strong>
            </div>
            <div class="card-body">

                <p><strong>Afiliado:</strong> <?= htmlspecialchars($afiliado['nombre']) ?></p>

                <form action="/SGA-SEBANA/public/afiliados/procesar-baja/<?= $afiliado['id'] ?>" method="POST">

                    

                    <div class="form-group mt-3">
                        <label>Tipo de Baja</label>
                        <select name="tipo_baja" class="form-control" required>
                            <option value="voluntaria">Voluntaria</option>
                            <option value="administrativa">Administrativa</option>
                            <option value="disciplinaria">Disciplinaria</option>
                        </select>
                    </div>

                    <div class="form-group mt-3">
                        <label>Motivo</label>
                        <textarea name="motivo_baja" class="form-control" required></textarea>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-danger">
                            Confirmar Desactivación
                        </button>
                        <a href="/SGA-SEBANA/public/afiliados" class="btn btn-secondary">
                            Cancelar
                        </a>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require $_SERVER['DOCUMENT_ROOT'] . '/SGA-SEBANA/public/templates/base.html.php';
?>
