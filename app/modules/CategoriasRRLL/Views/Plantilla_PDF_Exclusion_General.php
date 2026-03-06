<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
body { font-family: Arial, sans-serif; margin: 20px; }
.header { background-color: #dc3545; color: #fff; padding: 15px; text-align: center; }
.table { width: 100%; border-collapse: collapse; margin-top: 20px; }
.table th, .table td { border: 1px solid #ccc; padding: 8px; font-size: 12px; }
.footer { margin-top: 30px; font-size: 10px; text-align: center; color: #777; }
</style>
</head>
<body>
  <div class="header">
    <h2>Reporte de Exclusiones / Bajas</h2>
    <p>Documento oficial emitido por SGA-SEBANA</p>
  </div>
  <table class="table">
    <thead>
      <tr>
        <th>Nombre</th>
        <th>Cédula</th>
        <th>Fecha Baja</th>
        <th>Motivo</th>
        <th>Tipo Baja</th>
        <th>Estado</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($exclusiones as $exc): ?>
        <tr>
          <td><?= htmlspecialchars($exc['nombre_completo']) ?></td>
          <td><?= htmlspecialchars($exc['cedula']) ?></td>
          <td><?= htmlspecialchars($exc['fecha_baja'] ?? '—') ?></td>
          <td><?= htmlspecialchars($exc['motivo_baja'] ?? '—') ?></td>
          <td><?= htmlspecialchars($exc['tipo_baja'] ?? '—') ?></td>
          <td><?= htmlspecialchars($exc['estado']) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <div class="footer">
    Fecha de Emisión: <?= date('d/m/Y') ?> • Versión Digital
  </div>
</body>
</html>