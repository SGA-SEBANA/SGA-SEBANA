<!DOCTYPE html>
<html lang="es">
<<<<<<< HEAD
=======
<!-- Plantilla del carnet-->

>>>>>>> 48a2b02583d7a5e139ed278bad28a6c068032d90
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

<<<<<<< HEAD
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
=======
    <style>
        .carnet {
            width: 350px;
            height: 500px;
            border: 2px solid #000;
            margin: 20px auto;
            padding: 15px;
            font-family: Arial, sans-serif;
            position: relative;
        }

        .carnet-header {
            text-align: center;
            border-bottom: 1px solid #000;
            padding-bottom: 10px;
        }

        .carnet-header img {
            max-height: 60px;
        }

        .carnet-body {
            display: flex;
            margin-top: 20px;
        }

        .carnet-body img {
            width: 100px;
            height: 120px;
            object-fit: cover;
            border: 1px solid #000;
            margin-right: 15px;
        }

        .carnet-details p {
            margin: 5px 0;
            font-size: 14px;
        }

        .carnet-footer {
            position: absolute;
            bottom: 20px;
            width: 90%;
            text-align: center;
            font-size: 12px;
            border-top: 1px solid #000;
            padding-top: 10px;
        }
    </style>
</head>

<body>
    <div class="page-wrapper">
        <div class="page-content--bge5">
            <div class="container">
                <div class="carnet">
                    <div class="carnet-header">
                        <img src="/SGA-SEBANA/public/assets/img/icon/sebana_logo.jpg" alt="Logo Institucional">
                        <h4>Institución Ejemplo</h4>
                    </div>
                    <div class="carnet-body">
                        <!-- Foto de perfil -->
                        <img src="<?= $afiliado->foto ?? '/SGA-SEBANA/public/assets/images/default.png' ?>"
                            alt="Foto de perfil" class="carnet-img">


                        <!-- Datos del afiliado -->
                        <div class="carnet-details">
                            <p><strong>Nombre:</strong> <?= $afiliado->nombre ?></p>
                            <p><strong>Cédula:</strong> <?= $afiliado->cedula ?></p>
                            <p><strong>Estado:</strong> <?= $afiliado->estado ?></p>
                        </div>
                    </div>
                    <div class="carnet-footer">
                        <p>Emitido el: <?= date('d/m/Y') ?></p>
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=https://sga-sebana.org/validar.php?id=<?= $afiliado->id ?>"
                            alt="QR de validación">
                    </div>
>>>>>>> 48a2b02583d7a5e139ed278bad28a6c068032d90

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

<<<<<<< HEAD
</div>
=======
    <!-- Ajustamiento  de como se ve en cada dispositivo -->


    <style>
        .carnet-container {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 1rem;
        }

        .carnet-img {
            max-width: 100%;
            height: auto;
            object-fit: contain;
        }

        /* Teléfonos pequeños */
        @media (max-width: 480px) {
            .carnet-img {
                width: 90%;
            }
        }

        /* Tablets */
        @media (min-width: 768px) and (max-width: 1199px) {
            .carnet-img {
                width: 70%;
            }
        }

        /* Pantallas grandes (PC) */
        @media (min-width: 1200px) {
            .carnet-img {
                width: 50%;
            }
        }
    </style>


>>>>>>> 48a2b02583d7a5e139ed278bad28a6c068032d90

</body>
</html>
