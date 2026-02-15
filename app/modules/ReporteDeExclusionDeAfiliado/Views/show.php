<?php
ob_start();
?>

<div class="row">
  <div class="col-md-8 offset-md-2">
    <div class="card shadow-lg border-0 rounded-4">
      <div class="card-header bg-danger text-white text-center">
        <h4>Detalle de Exclusión / Baja</h4>
      </div>
      <div class="card-body">
        <p><strong>Nombre:</strong> <?= htmlspecialchars($afiliado['nombre_completo']) ?></p>
        <p><strong>Cédula:</strong> <?= htmlspecialchars($afiliado['cedula']) ?></p>
        <p><strong>Fecha Baja:</strong> <?= htmlspecialchars($afiliado['fecha_baja']) ?></p>
        <p><strong>Motivo:</strong> <?= htmlspecialchars($afiliado['motivo_baja']) ?></p>
        <p><strong>Tipo Baja:</strong> <?= htmlspecialchars($afiliado['tipo_baja']) ?></p>
        <p><strong>Estado:</strong> <?= htmlspecialchars($afiliado['estado']) ?></p>
        <p><strong>Observaciones:</strong> <?= htmlspecialchars($afiliado['observaciones'] ?? 'N/A') ?></p>
      </div>
      <div class="card-footer text-center">
        <a href="/SGA-SEBANA/public/ReporteDeExclusionDeAfiliado/pdf/<?= $afiliado['id'] ?>" class="btn btn-danger">
          <i class="zmdi zmdi-download"></i> Descargar PDF
        </a>
        <a href="/SGA-SEBANA/public/ReporteDeExclusionDeAfiliado" class="btn btn-secondary">
          <i class="zmdi zmdi-arrow-left"></i> Volver al Reporte
        </a>
      </div>
    </div>
  </div>
</div>

<?php
$content = ob_get_clean();
require $_SERVER['DOCUMENT_ROOT'] . '/SGA-SEBANA/public/templates/base.html.php';
?>