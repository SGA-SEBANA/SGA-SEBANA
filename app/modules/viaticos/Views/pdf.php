<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Boleta de Viáticos - <?= htmlspecialchars($viatico['consecutivo']) ?></title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 14px; color: #333; }
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
        </div>
        <div class="col-half right text-right">
            <strong>Fecha de Emisión:</strong> <?= date('d/m/Y h:i A') ?><br><br>
            <strong>Fecha de Registro:</strong> <?= date('d/m/Y', strtotime($viatico['creado_en'])) ?>
        </div>
    </div>
    <div style="clear: both;"></div>

    <h3>1. Resumen de Alimentación</h3>
    <table>
        <thead>
            <tr>
                <th>Concepto</th>
                <th class="text-right">Monto (₡)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Desayuno (Aplica: <?= $viatico['aplica_desayuno'] ? 'Sí' : 'No' ?>)</td>
                <td class="text-right"><?= $viatico['aplica_desayuno'] ? '4,200.00' : '0.00' ?></td>
            </tr>
            <tr>
                <td>Almuerzo (Aplica: <?= $viatico['aplica_almuerzo'] ? 'Sí' : 'No' ?>)</td>
                <td class="text-right"><?= $viatico['aplica_almuerzo'] ? '5,600.00' : '0.00' ?></td>
            </tr>
            <tr>
                <td>Cena (Aplica: <?= $viatico['aplica_cena'] ? 'Sí' : 'No' ?>)</td>
                <td class="text-right"><?= $viatico['aplica_cena'] ? '5,600.00' : '0.00' ?></td>
            </tr>
            <tr>
                <td class="text-right"><strong>Subtotal Alimentación:</strong></td>
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
                    <th class="text-right">Kilometraje</th>
                    <th class="text-right">Tarifa CGR (₡)</th>
                    <th class="text-right">Monto (₡)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?= ucfirst(htmlspecialchars($viatico['tipo_vehiculo'])) ?></td>
                    <td class="text-right"><?= number_format($viatico['kilometraje'], 2) ?> km</td>
                    <td class="text-right"><?= number_format($viatico['tarifa_km'], 2) ?></td>
                    <td class="text-right"><?= number_format($viatico['monto_transporte'], 2) ?></td>
                </tr>
            </tbody>
        </table>
    <?php else: ?>
        <p><i>No se solicitó cobro por concepto de transporte o kilometraje en esta boleta.</i></p><br>
    <?php endif; ?>

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