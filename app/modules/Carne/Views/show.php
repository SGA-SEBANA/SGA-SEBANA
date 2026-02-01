<div class="carnet-wrapper">

    <div class="carnet-card">

        <?php if (!empty($mensaje)): ?>
            <div class="alert alert-success text-center mb-3">
                <?= htmlspecialchars($mensaje) ?>
            </div>
        <?php endif; ?>

        <!-- HEADER -->
        <div class="carnet-header">
            <img
                src="/SGA-SEBANA/public/assets/img/logo.png"
                alt="Logo"
                class="carnet-logo"
            >
            <div class="carnet-title">
                <strong>Sindicato SGA-SEBANA</strong><br>
                <span>Carné Institucional</span>
            </div>
        </div>

        <!-- QR -->
        <div class="qr-container">
            <img
                src="<?= $qr_image ?>"
                alt="QR de validación"
                class="qr-image"
            >
        </div>

        <!-- DATOS -->
        <div class="carnet-details">
            <p><span class="label">Nombre:</span> <?= htmlspecialchars($afiliado['nombre']) ?></p>
            <p><span class="label">Cédula:</span> <?= htmlspecialchars($afiliado['cedula']) ?></p>
            <p>
                <span class="label">Estado:</span>
                <span class="estado <?= strtolower($afiliado['estado']) ?>">
                    <?= strtoupper($afiliado['estado']) ?>
                </span>
            </p>
        </div>

        <!-- BOTÓN -->
        <div class="actions">
            <a
                href="/SGA-SEBANA/public/carnets/descargar/<?= $afiliado['id'] ?>"
                class="btn btn-download"
                target="_blank"
            >
                ⬇ Descargar carné (PDF)
            </a>
        </div>

        <!-- FOOTER -->
        <div class="carnet-footer">
            Documento oficial • Uso institucional
        </div>

    </div>

</div>

<!-- Bootstrap JS -->
<script src="/SGA-SEBANA/public/assets/vendor/bootstrap-5.3.8.bundle.min.js"></script>

<!-- Zoom SOLO visual -->
<script src="https://unpkg.com/@panzoom/panzoom/dist/panzoom.min.js"></script>
<script>
const elem = document.querySelector('.qr-image');
if (elem) {
    const panzoom = Panzoom(elem, { maxScale: 5, minScale: 1 });
    elem.addEventListener('wheel', panzoom.zoomWithWheel);
}
</script>

<style>
body {
    background: #f4f6f9;
}

/* CONTENEDOR */
.carnet-wrapper {
    display: flex;
    justify-content: center;
    margin-top: 40px;
}

/* TARJETA */
.carnet-card {
    width: 360px;
    background: #fff;
    border: 2px solid #0b4f6c;
    border-radius: 16px;
    padding: 18px;
    box-shadow: 0 6px 15px rgba(0,0,0,0.15);
}

/* HEADER */
.carnet-header {
    display: flex;
    align-items: center;
    border-bottom: 2px solid #0b4f6c;
    padding-bottom: 10px;
    margin-bottom: 14px;
}

.carnet-logo {
    width: 48px;
    margin-right: 10px;
}

.carnet-title strong {
    font-size: 15px;
    color: #0b4f6c;
}

.carnet-title span {
    font-size: 12px;
    color: #555;
}

/* QR */
.qr-container {
    text-align: center;
    margin: 15px 0;
}

.qr-image {
    width: 200px;
    background: #fff;
    padding: 10px;
    border: 2px solid #000;
    border-radius: 10px;
    cursor: zoom-in;
}

/* DATOS */
.carnet-details {
    font-size: 14px;
    margin-top: 10px;
}

.carnet-details p {
    margin: 4px 0;
}

.label {
    font-weight: bold;
    color: #0b4f6c;
}

/* ESTADO */
.estado {
    padding: 3px 10px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: bold;
}

.estado.activo {
    background: #d4edda;
    color: #155724;
}

.estado.inactivo {
    background: #f8d7da;
    color: #721c24;
}

/* BOTÓN */
.actions {
    margin-top: 20px;
    display: flex;
    justify-content: center;
}

.btn-download {
    background-color: #0b4f6c;
    color: #fff;
    padding: 10px 25px;
    border-radius: 25px;
    text-decoration: none;
    font-size: 14px;
}

.btn-download:hover {
    background-color: #093d54;
    color: #fff;
}

/* FOOTER */
.carnet-footer {
    margin-top: 15px;
    text-align: center;
    font-size: 11px;
    color: #777;
    border-top: 1px solid #ddd;
    padding-top: 6px;
}
</style>
