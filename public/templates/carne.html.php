<!DOCTYPE html>
<html lang="es">
 <!-- Plantilla del carnet-->
<head>
    <!-- Required meta tags -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="SGA-SEBANA Carné">
    <meta name="author" content="SGA-SEBANA Team">
    <meta name="keywords" content="sga sebana carnet">

    <!-- Title Page -->
    <title><?= $title ?? 'Carné Institucional' ?></title>

    <!-- Bootstrap CSS -->
    <link href="/SGA-SEBANA/public/assets/vendor/bootstrap-5.3.8.min.css" rel="stylesheet" media="all">

    <!-- Fontfaces CSS -->
    <link href="/SGA-SEBANA/public/assets/css/font-face.css" rel="stylesheet" media="all">
    <link href="/SGA-SEBANA/public/assets/vendor/fontawesome-7.1.0/css/all.min.css" rel="stylesheet" media="all">

    <!-- Main CSS -->
    <link href="/SGA-SEBANA/public/assets/css/theme.css" rel="stylesheet" media="all">

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
                        <img src="/SGA-SEBANA/public/assets/images/logo.png" alt="Logo Institucional">
                        <h4>Institución Ejemplo</h4>
                    </div>
                    <div class="carnet-body">
                        <!-- Foto de perfil -->
                        <img src="<?= $afiliado->foto ?? '/SGA-SEBANA/public/assets/images/default.png' ?>" alt="Foto de perfil" class="carnet-img">

                        
                        <!-- Datos del afiliado -->
                        <div class="carnet-details">
                            <p><strong>Nombre:</strong> <?= $afiliado->nombre ?></p>
                            <p><strong>Cédula:</strong> <?= $afiliado->cedula ?></p>
                            <p><strong>Estado:</strong> <?= $afiliado->estado ?></p>
                        </div>
                    </div>
                    <div class="carnet-footer">
                        <p>Emitido el: <?= date('d/m/Y') ?></p>
                        <p>Código QR aquí</p>
                    </div>
                </div>
            </div>
        </div>
    </div>




 <!-- Ajustamiento  de como se ve en cada dispositivo -->
    <!-- Bootstrap JS -->
    <script src="/SGA-SEBANA/public/assets/vendor/bootstrap-5.3.8.bundle.min.js"></script>

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
  .carnet-img { width: 90%; }
}

/* Tablets */
@media (min-width: 768px) and (max-width: 1199px) {
  .carnet-img { width: 70%; }
}

/* Pantallas grandes (PC) */
@media (min-width: 1200px) {
  .carnet-img { width: 50%; }
}
</style>

</body>

</html>