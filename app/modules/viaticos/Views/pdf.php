<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Boleta de Viáticos - <?= htmlspecialchars($viatico['consecutivo']) ?></title>
    <style>
        body { font-family: 'DejaVu Sans', 'Helvetica', 'Arial', sans-serif; font-size: 14px; color: #333; }
        .header { border-bottom: 2px solid #001B71; padding-bottom: 10px; margin-bottom: 20px; }
        .logo-text { color: #001B71; font-size: 24px; font-weight: bold; margin: 0; }
        .sub-text { font-size: 12px; color: #777; margin: 0; }
        .boleta-title { text-align: center; font-size: 18px; font-weight: bold; margin: 20px 0; background-color: #f4f4f4; padding: 10px; }
        .row { width: 100%; clear: both; margin-bottom: 15px; }
        .col-half { width: 48%; float: left; }
        .col-half.right { float: right; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #001B71; color: white; }
        .text-right { text-align: right; }
        .total-box { background-color: #e6f7ff; border: 1px solid #91d5ff; padding: 15px; text-align: right; font-size: 18px; font-weight: bold; }
        .footer { margin-top: 50px; font-size: 12px; text-align: center; color: #777; border-top: 1px solid #ddd; padding-top: 10px; }
        .signature-box { margin-top: 60px; text-align: center; }
        .signature-line { width: 250px; border-bottom: 1px solid #000; margin: 0 auto 5px auto; }
    </style>
</head>
<body>

    <div class="header">
        <h1 class="logo-text">SGA-SEBANA</h1>
        <p class="sub-text">Sistema de Gestión Administrativa | Módulo de Viáticos</p>
    </div>

    <div class="boleta-title">
        BOLETA OFICIAL DE VIÁTICOS Y TRANSPORTE
    </div>

    <div class="row">
        <div class="col-half">
            <strong>Consecutivo:</strong> <?= htmlspecialchars($viatico['consecutivo']) ?><br><br>
            <strong>Estado:</strong> <?= htmlspecialchars($viatico['estado']) ?>
            <?php if (!empty($viatico['fecha_inicio']) || !empty($viatico['fecha_fin'])): ?>
                <br><br><strong>Rango:</strong>
                <?= !empty($viatico['fecha_inicio']) ? date('d/m/Y', strtotime($viatico['fecha_inicio'])) : 'N/D' ?>
                -
                <?= !empty($viatico['fecha_fin']) ? date('d/m/Y', strtotime($viatico['fecha_fin'])) : 'N/D' ?>
            <?php endif; ?>
            <?php if (!empty($viatico['cantidad_dias'])): ?>
                <br><br><strong>Cantidad de Días:</strong> <?= (int)$viatico['cantidad_dias'] ?>
            <?php endif; ?>
            <?php if (!empty($viatico['empleados'])): ?>
                <br><br><strong>Empleado(s):</strong><br>
                <?= nl2br(htmlspecialchars($viatico['empleados'])) ?>
            <?php endif; ?>
        </div>
        <div class="col-half right text-right">
            <strong>Fecha de Emisión:</strong> <?= date('d/m/Y h:i A') ?><br><br>
            <strong>Fecha de Registro:</strong> <?= date('d/m/Y', strtotime($viatico['creado_en'])) ?>
        </div>
    </div>
    <div style="clear: both;"></div>

    <?php
        $desayunoCount = (int)($viatico['cantidad_desayuno'] ?? 0);
        $almuerzoCount = (int)($viatico['cantidad_almuerzo'] ?? 0);
        $cenaCount = (int)($viatico['cantidad_cena'] ?? 0);
        $desayunoTotal = $desayunoCount * 4200;
        $almuerzoTotal = $almuerzoCount * 5600;
        $cenaTotal = $cenaCount * 5600;
    ?>
    <h3>1. Resumen de Alimentación</h3>
    <table>
        <thead>
            <tr>
                <th>Concepto</th>
                <th class="text-right">Cantidad</th>
                <th class="text-right">Monto (₡)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Desayuno</td>
                <td class="text-right"><?= $desayunoCount ?></td>
                <td class="text-right"><?= number_format($desayunoTotal, 2) ?></td>
            </tr>
            <tr>
                <td>Almuerzo</td>
                <td class="text-right"><?= $almuerzoCount ?></td>
                <td class="text-right"><?= number_format($almuerzoTotal, 2) ?></td>
            </tr>
            <tr>
                <td>Cena</td>
                <td class="text-right"><?= $cenaCount ?></td>
                <td class="text-right"><?= number_format($cenaTotal, 2) ?></td>
            </tr>
            <tr>
                <td class="text-right" colspan="2"><strong>Subtotal Alimentación:</strong></td>
                <td class="text-right"><strong><?= number_format($viatico['monto_alimentacion'], 2) ?></strong></td>
            </tr>
        </tbody>
    </table>

    <h3>2. Resumen de Transporte y Kilometraje</h3>
    <?php if ($viatico['aplica_transporte']): ?>
        <table>
            <thead>
                <tr>
                    <th>Tipo de Vehículo</th>
                    <th class="text-right">Transportes</th>
                    <th class="text-right">Kilometraje</th>
                    <th class="text-right">Tarifa CGR (₡)</th>
                    <th class="text-right">Monto (₡)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?= ucfirst(htmlspecialchars($viatico['tipo_vehiculo'])) ?></td>
                    <td class="text-right"><?= (int)($viatico['cantidad_transportes'] ?? 0) ?></td>
                    <td class="text-right"><?= number_format($viatico['kilometraje'], 2) ?> km</td>
                    <td class="text-right"><?= number_format($viatico['tarifa_km'], 2) ?></td>
                    <td class="text-right"><?= number_format($viatico['monto_transporte'], 2) ?></td>
                </tr>
            </tbody>
        </table>
    <?php else: ?>
        <p><i>No se solicitó cobro por concepto de transporte o kilometraje en esta boleta.</i></p><br>
    <?php endif; ?>

    <h3>3. Otros Gastos</h3>
    <table>
        <thead>
            <tr>
                <th>Concepto</th>
                <th class="text-right">Monto (₡)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Hospedaje</td>
                <td class="text-right"><?= number_format($viatico['monto_hospedaje'] ?? 0, 2) ?></td>
            </tr>
            <tr>
                <td>Gastos Menores</td>
                <td class="text-right"><?= number_format($viatico['monto_gastos_menores'] ?? 0, 2) ?></td>
            </tr>
        </tbody>
    </table>

    <div class="total-box">
        TOTAL A PAGAR: ₡ <?= number_format($viatico['total_pagar'], 2) ?>
    </div>

    <div class="row" style="margin-top: 80px;">
        <div class="col-half">
            <div class="signature-box">
                <div class="signature-line"></div>
                Firma del Solicitante
            </div>
        </div>
        <div class="col-half right">
            <div class="signature-box">
                <div class="signature-line"></div>
                Autorizado por (Jefatura)
            </div>
        </div>
    </div>
    <div style="clear: both;"></div>

    <div class="footer">
        Documento generado automáticamente por SGA-SEBANA. 
        <br>Ref: <?= md5($viatico['consecutivo'] . $viatico['creado_en']) ?>
    </div>

</body>
</html>
