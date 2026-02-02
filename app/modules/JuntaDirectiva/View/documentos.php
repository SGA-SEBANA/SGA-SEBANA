<?php
$title = "Documentos - " . htmlspecialchars($miembro['nombre']);
ob_start();
?>

<h2>Documentos de <?= htmlspecialchars($miembro['nombre']) ?></h2>

<?php if(empty($documentos)): ?>
    <p>No hay documentos.</p>
<?php else: ?>
    <ul>
        <?php foreach($documentos as $doc): ?>
            <li>
                <a href="/SGA-SEBANA/public/junta/ver-documento/<?= $doc['id'] ?>" target="_blank">
                    <?= htmlspecialchars($doc['nombre_original']) ?>
                </a>
                <small>(<?= $doc['fecha_subida'] ?>)</small>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<a href="/SGA-SEBANA/public/junta" class="btn btn-secondary">Volver</a>

<?php
$content = ob_get_clean();
require BASE_PATH . '/public/templates/base.html.php';
