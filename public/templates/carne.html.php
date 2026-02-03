<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap');
@page { margin: 0; }
html, body {
    margin: 0;
    padding: 0;
    width: 100%;
    height: 100%;
    background-color: #ffffff;
    overflow: hidden;
    font-family: 'Poppins', 'Helvetica', 'Arial', sans-serif;
}
.card {
    width: 105mm;
    height: 147mm;
    box-sizing: border-box;
    position: relative;
    overflow: hidden;
    border: 1px solid #eeeeee;
}
.header {
    background-color: #007bff;
    color: #ffffff;
    padding: 20px 10px;
    text-align: center;
}
.header img {
    width: 60px;
    margin-bottom: 5px;
}
.header h2 {
    margin: 0;
    font-size: 18px;
    letter-spacing: 1px;
    text-transform: uppercase;
    font-family: 'Poppins', sans-serif;
    font-weight: 700;
}
.header p {
    margin: 0;
    font-size: 10px;
    opacity: 0.8;
}
.content {
    padding: 15px;
    text-align: center;
}
.qr-container {
    margin: 10px 0;
}
.qr-container img {
    width: 150px;
    height: 150px;
    border: 1px solid #ddd;
    padding: 5px;
    border-radius: 5px;
}
.details {
    text-align: left;
    margin-top: 15px;
    font-size: 12px;
}
.details div {
    margin-bottom: 8px;
}
.label {
    color: #888;
    font-size: 9px;
    display: block;
    text-transform: uppercase;
    margin-bottom: 2px;
}
.value {
    font-weight: bold;
    color: #333;
    font-size: 13px;
}
.status-badge {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 10px;
    font-size: 10px;
    font-weight: bold;
    background-color: #d4edda;
    color: #155724;
}
.footer {
    position: absolute;
    bottom: 0;
    width: 100%;
    background-color: #f8f9fa;
    padding: 10px 0;
    text-align: center;
    font-size: 8px;
    color: #999;
    border-top: 1px solid #eee;
}
</style>
</head>
<body>
<div class="card">
    <div class="header">
        <img src="<?= $logo_image ?>" alt="Logo">
        <h2>SGA-SEBANA</h2>
        <p>CARNÉ INSTITUCIONAL DE AFILIACIÓN</p>
    </div>
    <div class="content">
        <div class="qr-container">
            <img src="<?= $qr_image ?>" alt="QR Code">
        </div>
        <div class="details">
            <div>
                <span class="label">Nombre del Afiliado</span>
                <span class="value"><?= htmlspecialchars($afiliado['nombre_completo'] ?? ($afiliado['nombre'] . ' ' . $afiliado['apellido1'])) ?></span>
            </div>
            <table width="100%" cellspacing="0" cellpadding="0">
                <tr>
                    <td width="60%">
                        <span class="label">Identificación</span>
                        <span class="value"><?= htmlspecialchars($afiliado['cedula']) ?></span>
                    </td>
                    <td width="40%">
                        <span class="label">Estado</span>
                        <span class="status-badge">VIGENTE</span>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <div class="footer">
        Documento oficial emitido por SGA-SEBANA<br>
        Fecha de Emisión: <?= date('d/m/Y') ?> • Versión Digital
    </div>
</div>
</body>
</html>