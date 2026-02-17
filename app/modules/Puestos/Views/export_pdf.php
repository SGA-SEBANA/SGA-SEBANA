<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Puestos - <?= date('Y-m-d') ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 10px; color: #333; padding: 15px; }
        h1 { text-align: center; font-size: 16px; margin-bottom: 4px; }
        .subtitle { text-align: center; color: #666; font-size: 11px; margin-bottom: 15px; }
        .stats { width: 100%; margin-bottom: 15px; }
        .stats td { text-align: center; padding: 6px 10px; border: 1px solid #ddd; }
        .stats td strong { display: block; font-size: 14px; }
        .stats td small { color: #666; }
        table.data { width: 100%; border-collapse: collapse; margin-top: 8px; }
        table.data th { background-color: #3b5998; color: white; padding: 5px 6px; text-align: left; font-size: 9px; }
        table.data td { padding: 4px 6px; border-bottom: 1px solid #ddd; font-size: 9px; }
        table.data tr:nth-child(even) { background-color: #f9f9f9; }
        .status-activo { color: #28a745; font-weight: bold; }
        .status-finalizado { color: #dc3545; font-weight: bold; }
        .status-suspendido { color: #e0a800; font-weight: bold; }
        .footer { margin-top: 15px; text-align: center; font-size: 9px; color: #999; border-top: 1px solid #ddd; padding-top: 5px; }
    </style>
</head>
<body>
    <h1>Reporte de Puestos de Afiliados</h1>
    <p class="subtitle">SGA-SEBANA — Generado el <?= date('d/m/Y H:i') ?></p>

    <!-- Statistics -->
    <table class="stats" cellspacing="0">
        <tr>
            <td>
                <strong><?= $estadisticas['total'] ?? 0 ?></strong>
                <small>Total</small>
            </td>
            <td>
                <strong style="color: #28a745;"><?= $estadisticas['activos'] ?? 0 ?></strong>
                <small>Activos</small>
            </td>
            <td>
                <strong style="color: #dc3545;"><?= $estadisticas['finalizados'] ?? 0 ?></strong>
                <small>Finalizados</small>
            </td>
            <td>
                <strong style="color: #e0a800;"><?= $estadisticas['suspendidos'] ?? 0 ?></strong>
                <small>Suspendidos</small>
            </td>
        </tr>
    </table>

    <!-- Table -->
    <table class="data" cellspacing="0">
        <thead>
            <tr>
                <th>#</th>
                <th>Afiliado</th>
                <th>Cédula</th>
                <th>Puesto</th>
                <th>Departamento</th>
                <th>Oficina</th>
                <th>Contrato</th>
                <th>Jornada</th>
                <th>Salario</th>
                <th>Estado</th>
                <th>F. Asignación</th>
                <th>F. Remoción</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($puestos)): ?>
                <tr><td colspan="12" style="text-align:center;">No hay puestos registrados.</td></tr>
            <?php else: ?>
                <?php $contrato_labels = ['indefinido' => 'Indef.', 'temporal' => 'Temp.', 'proyecto' => 'Proy.']; ?>
                <?php $jornada_labels = ['completa' => 'Completa', 'media' => 'Media', 'por_horas' => 'Por Horas']; ?>
                <?php foreach ($puestos as $i => $p): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= htmlspecialchars($p['afiliado_nombre']) ?></td>
                        <td><?= htmlspecialchars($p['afiliado_cedula']) ?></td>
                        <td><?= htmlspecialchars($p['nombre']) ?></td>
                        <td><?= htmlspecialchars($p['departamento'] ?? '') ?></td>
                        <td><?= htmlspecialchars($p['oficina_nombre'] ?? '') ?></td>
                        <td><?= $contrato_labels[$p['tipo_contrato']] ?? ucfirst($p['tipo_contrato']) ?></td>
                        <td><?= $jornada_labels[$p['jornada']] ?? ucfirst($p['jornada']) ?></td>
                        <td><?= $p['salario_base'] ? number_format($p['salario_base'], 2) : '' ?></td>
                        <td class="status-<?= $p['estado'] ?>"><?= ucfirst($p['estado']) ?></td>
                        <td><?= !empty($p['fecha_asignacion']) ? date('d/m/Y', strtotime($p['fecha_asignacion'])) : '' ?></td>
                        <td><?= !empty($p['fecha_remocion']) ? date('d/m/Y', strtotime($p['fecha_remocion'])) : '' ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer">
        Reporte generado automáticamente por SGA-SEBANA — <?= count($puestos) ?> registro(s)
    </div>
</body>
</html>
