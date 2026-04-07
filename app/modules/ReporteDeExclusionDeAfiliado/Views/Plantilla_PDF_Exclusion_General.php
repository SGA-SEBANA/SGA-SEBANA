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
        <th>Cedula</th>
        <th>Fecha Baja</th>
        <th>Motivo</th>
        <th>Tipo Baja</th>
        <th>Estado</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach (($exclusiones ?? []) as $exc): ?>
        <?php
          $nombre = (string) ($exc['nombre_completo'] ?? '');
          $cedula = (string) ($exc['cedula'] ?? '');
          $fechaBaja = (string) ($exc['fecha_baja'] ?? '---');
          $motivoBaja = (string) ($exc['motivo_baja'] ?? '---');
          $tipoBaja = (string) ($exc['tipo_baja'] ?? '---');
          $estado = (string) ($exc['estado'] ?? '');
        ?>
        <tr>
          <td><?= htmlspecialchars($nombre) ?></td>
          <td><?= htmlspecialchars($cedula) ?></td>
          <td><?= htmlspecialchars($fechaBaja) ?></td>
          <td><?= htmlspecialchars($motivoBaja) ?></td>
          <td><?= htmlspecialchars($tipoBaja) ?></td>
          <td><?= htmlspecialchars($estado) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <div class="footer">
    Fecha de Emision: <?= date('d/m/Y') ?> - Version Digital
  </div>
</body>
</html>
