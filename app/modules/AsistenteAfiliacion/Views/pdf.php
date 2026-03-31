<?php
$solicitud = $solicitud ?? [];
$fullName = trim((string) (($solicitud['nombre'] ?? '') . ' ' . ($solicitud['apellido1'] ?? '') . ' ' . ($solicitud['apellido2'] ?? '')));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Formulario de Afiliacion SEBANA</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1f2937; }
        .header { text-align: center; margin-bottom: 18px; }
        .title { font-size: 18px; font-weight: bold; color: #1c4388; margin-bottom: 4px; }
        .subtitle { font-size: 11px; color: #4b5563; }
        .box { border: 1px solid #d1d5db; border-radius: 6px; padding: 10px; margin-bottom: 10px; }
        .section-title { font-weight: bold; font-size: 13px; color: #1c4388; margin-bottom: 8px; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 4px 2px; vertical-align: top; }
        .label { font-weight: bold; width: 38%; }
        .value { border-bottom: 1px solid #e5e7eb; }
        .check { font-weight: bold; color: #065f46; }
        .footer { margin-top: 20px; font-size: 11px; color: #374151; line-height: 1.4; }
        .signature { margin-top: 32px; }
        .line { margin-top: 40px; border-top: 1px solid #111827; width: 70%; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">SEBANA - Solicitud de Afiliacion</div>
        <div class="subtitle">Documento generado por el Asistente de Afiliacion</div>
    </div>

    <div class="box">
        <div class="section-title">1. Elegibilidad BNCR</div>
        <table>
            <tr>
                <td class="label">Tipo de usuario</td>
                <td class="value"><?= htmlspecialchars((string) ($solicitud['tipo_usuario'] ?? '')) ?></td>
            </tr>
            <tr>
                <td class="label">Cedula</td>
                <td class="value"><?= htmlspecialchars((string) ($solicitud['cedula'] ?? '')) ?></td>
            </tr>
        </table>
    </div>

    <div class="box">
        <div class="section-title">2. Datos personales</div>
        <table>
            <tr>
                <td class="label">Nombre completo</td>
                <td class="value"><?= htmlspecialchars($fullName) ?></td>
            </tr>
            <tr>
                <td class="label">Correo electronico</td>
                <td class="value"><?= htmlspecialchars((string) ($solicitud['correo'] ?? '')) ?></td>
            </tr>
            <tr>
                <td class="label">Fecha de nacimiento</td>
                <td class="value"><?= htmlspecialchars((string) ($solicitud['fecha_nacimiento'] ?? '')) ?></td>
            </tr>
            <tr>
                <td class="label">Celular</td>
                <td class="value"><?= htmlspecialchars((string) ($solicitud['celular'] ?? '')) ?></td>
            </tr>
        </table>
    </div>

    <div class="box">
        <div class="section-title">3. Informacion laboral BNCR</div>
        <table>
            <tr>
                <td class="label">Numero de empleado</td>
                <td class="value"><?= htmlspecialchars((string) ($solicitud['numero_empleado'] ?? '')) ?></td>
            </tr>
            <tr>
                <td class="label">Oficina BNCR</td>
                <td class="value"><?= htmlspecialchars((string) ($solicitud['oficina_bncr'] ?? '')) ?></td>
            </tr>
            <tr>
                <td class="label">Departamento</td>
                <td class="value"><?= htmlspecialchars((string) ($solicitud['departamento'] ?? '')) ?></td>
            </tr>
            <tr>
                <td class="label">Puesto</td>
                <td class="value"><?= htmlspecialchars((string) ($solicitud['puesto'] ?? '')) ?></td>
            </tr>
            <tr>
                <td class="label">Fecha ingreso BNCR</td>
                <td class="value"><?= htmlspecialchars((string) ($solicitud['fecha_ingreso_bncr'] ?? '')) ?></td>
            </tr>
            <tr>
                <td class="label">Fecha jubilacion</td>
                <td class="value"><?= htmlspecialchars((string) ($solicitud['fecha_jubilacion'] ?? '-')) ?></td>
            </tr>
        </table>
    </div>

    <div class="box">
        <div class="section-title">4. Aceptaciones</div>
        <p class="check">Deduccion salarial 1%: <?= ((int) ($solicitud['acepta_deduccion'] ?? 0) === 1) ? 'ACEPTADA' : 'NO ACEPTADA' ?></p>
        <p class="check">Aceptacion del estatuto: <?= ((int) ($solicitud['acepta_estatuto'] ?? 0) === 1) ? 'ACEPTADA' : 'NO ACEPTADA' ?></p>
        <?php if (!empty($solicitud['observaciones'])): ?>
            <p><strong>Observaciones:</strong> <?= nl2br(htmlspecialchars((string) $solicitud['observaciones'])) ?></p>
        <?php endif; ?>
    </div>

    <div class="footer">
        Declaro que la informacion brindada es veridica y autorizo a SEBANA para gestionar esta solicitud de afiliacion.
        <div class="signature">
            <div class="line"></div>
            <div>Firma del solicitante</div>
            <div>Nombre: <?= htmlspecialchars($fullName) ?></div>
            <div>Cedula: <?= htmlspecialchars((string) ($solicitud['cedula'] ?? '')) ?></div>
            <div>Fecha: <?= date('Y-m-d') ?></div>
        </div>
    </div>
</body>
</html>
