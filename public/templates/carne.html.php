<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">

<style>
body {
    font-family: Arial, Helvetica, sans-serif;
    margin: 0;
    padding: 0;
}

.card {
    width: 100%;
    height: 100%;
    border: 2px solid #0b4f6c;
    border-radius: 10px;
    padding: 10px;
    box-sizing: border-box;
    position: relative;
}

/* HEADER */
.header {
    display: flex;
    align-items: center;
    border-bottom: 2px solid #0b4f6c;
    padding-bottom: 6px;
    margin-bottom: 8px;
}

.logo {
    width: 45px;
    height: 45px;
    margin-right: 10px;
}

.header-text {
    font-size: 12px;
    line-height: 1.2;
}

.header-text strong {
    font-size: 14px;
    color: #0b4f6c;
}

/* BODY */
.content {
    text-align: center;
}

.qr {
    margin: 8px 0;
}

.qr img {
    width: 120px;
    height: 120px;
}

.info {
    font-size: 11px;
    text-align: left;
    margin-top: 6px;
}

.info p {
    margin: 3px 0;
}

.label {
    font-weight: bold;
    color: #0b4f6c;
}

/* FOOTER */
.footer {
    position: absolute;
    bottom: 8px;
    left: 10px;
    right: 10px;
    font-size: 9px;
    text-align: center;
    border-top: 1px solid #ccc;
    padding-top: 4px;
    color: #555;
}
</style>
</head>

<body>

<div class="card">

    <!-- HEADER -->
    <div class="header">
        <img src="<?= $logo_image ?>" class="logo">
        <div class="header-text">
            <strong>Sindicato SGA-SEBANA</strong><br>
            Carné Institucional
        </div>
    </div>

    <!-- BODY -->
    <div class="content">
        <div class="qr">
            <img src="<?= $qr_image ?>">
        </div>

        <div class="info">
            <p><span class="label">Nombre:</span> <?= htmlspecialchars($afiliado['nombre']) ?></p>
            <p><span class="label">Cédula:</span> <?= htmlspecialchars($afiliado['cedula']) ?></p>
            <p><span class="label">Estado:</span> <?= strtoupper($afiliado['estado']) ?></p>
        </div>
    </div>

    <!-- FOOTER -->
    <div class="footer">
        Documento oficial • Uso institucional<br>
        Emitido el <?= date('d/m/Y') ?>
    </div>

</div>

</body>
</html>
