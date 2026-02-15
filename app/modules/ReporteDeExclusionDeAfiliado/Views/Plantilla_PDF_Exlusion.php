<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
body { font-family: Arial, sans-serif; margin: 20px; }
.header { background-color: #dc3545; color: #fff; padding: 15px; text-align: center; }
.content { margin-top: 20px; }
.footer { margin-top: 30px; font-size: 10px; text-align: center; color: #777; }
</style>
</head>
<body>
  <div class="header">
    <h2>Reporte de Exclusión / Baja</h2>
    <p>Documento oficial emitido por SGA-SEBANA</p>
  </div>
  <div class="content">
    <p><strong>Nombre:</strong> <?= htmlspecialchars($afiliado['nombre_completo']) ?></p>
    <p><strong>Cédula:</strong> <?= htmlspecialchars($afiliado['cedula']) ?></p>
    <p><strong>Fecha Baja:</strong> <?= htmlspecialchars($afiliado['fecha_baja']) ?></p>
    <p><strong>Motivo:</strong> <?= htmlspecialchars($afiliado['motivo_baja']) ?></p>
    <p><strong>Tipo Baja:</strong> <?= htmlspecialchars($afiliado['tipo_baja']) ?></p>
    <p><strong>Estado:</strong> <?= htmlspecialchars($afiliado['estado']) ?></p>
    <p><strong>Observaciones:</strong> <?= htmlspecialchars($afiliado['observaciones'] ?? 'N/A') ?></p>
  </div>
  <div class="footer">
    Fecha de Emisión: <?= date('d/m/Y') ?> • Versión Digital
  </div>
</body>
</html>