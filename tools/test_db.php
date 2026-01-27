<?php
// tools/test_db_simple.php

$envFile = __DIR__ . '/../.env';

if (!file_exists($envFile)) {
    die("Error: No se encontró el archivo .env en la raíz del proyecto");
}

$lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
foreach ($lines as $line) {
    if (strpos(trim($line), '#') === 0) {
        continue;
    }

    if (strpos($line, '=') !== false) {
        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        $_ENV[$key] = $value;
        putenv("$key=$value");
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test de Conexión BD - SGA-SEBANA</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: #f5f7fa;
            color: #2c3e50;
            line-height: 1.6;
        }

        .header {
            background: white;
            border-bottom: 1px solid #e1e8ed;
            padding: 20px 0;
            margin-bottom: 30px;
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .header h1 {
            font-size: 24px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 14px;
            color: #7f8c8d;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px 40px;
        }

        .card {
            background: white;
            border: 1px solid #e1e8ed;
            border-radius: 4px;
            margin-bottom: 20px;
            overflow: hidden;
        }

        .card-header {
            padding: 20px;
            border-bottom: 1px solid #e1e8ed;
            background: #fafbfc;
        }

        .card-header h2 {
            font-size: 18px;
            font-weight: 600;
            color: #2c3e50;
        }

        .card-body {
            padding: 20px;
        }

        .status-box {
            padding: 15px 20px;
            border-radius: 4px;
            margin-bottom: 20px;
            border-left: 4px solid;
        }

        .status-success {
            background: #d4edda;
            border-left-color: #28a745;
            color: #155724;
        }

        .status-error {
            background: #f8d7da;
            border-left-color: #dc3545;
            color: #721c24;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }

        .info-item {
            padding: 12px;
            background: #f8f9fa;
            border-radius: 4px;
            border: 1px solid #e1e8ed;
        }

        .info-label {
            font-size: 12px;
            color: #7f8c8d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }

        .info-value {
            font-size: 14px;
            color: #2c3e50;
            font-weight: 500;
        }

        .table-list {
            list-style: none;
            padding: 0;
            margin-top: 10px;
        }

        .table-list li {
            padding: 8px 12px;
            background: #f8f9fa;
            border: 1px solid #e1e8ed;
            margin-bottom: 5px;
            border-radius: 3px;
            font-size: 14px;
            color: #5a6c7d;
        }

        .error-details {
            margin-top: 15px;
        }

        .error-message {
            padding: 12px;
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 4px;
            margin-bottom: 15px;
            font-size: 14px;
            color: #856404;
        }

        .solutions-list {
            list-style: none;
            padding: 0;
        }

        .solutions-list li {
            padding: 8px 0;
            padding-left: 20px;
            position: relative;
            font-size: 14px;
            color: #5a6c7d;
        }

        .solutions-list li:before {
            content: "•";
            position: absolute;
            left: 5px;
            color: #7f8c8d;
        }

        .debug-section {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e1e8ed;
        }

        .debug-grid {
            display: grid;
            gap: 10px;
            margin-top: 15px;
        }

        .debug-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 12px;
            background: #f8f9fa;
            border-radius: 3px;
            font-size: 13px;
        }

        .debug-label {
            color: #7f8c8d;
        }

        .debug-value {
            color: #2c3e50;
            font-weight: 500;
        }

        .debug-value.success {
            color: #28a745;
        }

        .debug-value.error {
            color: #dc3545;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #5b7dff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 500;
            transition: background 0.2s;
        }

        .btn:hover {
            background: #4c6edb;
        }

        .btn-secondary {
            background: white;
            color: #5a6c7d;
            border: 1px solid #e1e8ed;
        }

        .btn-secondary:hover {
            background: #f8f9fa;
        }

        .back-section {
            text-align: center;
            margin-top: 30px;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="header-content">
            <h1>Test de Conexión de Base de Datos</h1>
            <p>Verificación de conexiones local y remota</p>
        </div>
    </div>

    <div class="container">

        <!-- BASE DE DATOS LOCAL -->
        <div class="card">
            <div class="card-header">
                <h2>Base de Datos LOCAL (XAMPP)</h2>
            </div>
            <div class="card-body">
                <?php
                try {
                    $configLocal = require __DIR__ . '/../app/config/database.local.php';

                    $dsn = "mysql:host={$configLocal['host']};port={$configLocal['port']};dbname={$configLocal['dbname']};charset={$configLocal['charset']}";
                    $pdo = new PDO($dsn, $configLocal['username'], $configLocal['password'], $configLocal['options']);

                    echo '<div class="status-box status-success">';
                    echo '<strong>CONEXIÓN EXITOSA</strong>';
                    echo '</div>';

                    echo '<div class="info-grid">';
                    echo '<div class="info-item">';
                    echo '<div class="info-label">Host</div>';
                    echo '<div class="info-value">' . htmlspecialchars($configLocal['host']) . '</div>';
                    echo '</div>';

                    echo '<div class="info-item">';
                    echo '<div class="info-label">Base de Datos</div>';
                    echo '<div class="info-value">' . htmlspecialchars($configLocal['dbname']) . '</div>';
                    echo '</div>';

                    echo '<div class="info-item">';
                    echo '<div class="info-label">Usuario</div>';
                    echo '<div class="info-value">' . htmlspecialchars($configLocal['username']) . '</div>';
                    echo '</div>';

                    $version = $pdo->query("SELECT VERSION()")->fetchColumn();
                    echo '<div class="info-item">';
                    echo '<div class="info-label">Versión</div>';
                    echo '<div class="info-value">' . htmlspecialchars($version) . '</div>';
                    echo '</div>';
                    echo '</div>';

                    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
                    echo '<div style="margin-top: 20px;">';
                    echo '<div class="info-label">TABLAS ENCONTRADAS (' . count($tables) . ')</div>';

                    if (count($tables) > 0) {
                        echo '<ul class="table-list">';
                        foreach ($tables as $table) {
                            echo '<li>' . htmlspecialchars($table) . '</li>';
                        }
                        echo '</ul>';
                    } else {
                        echo '<p style="color: #7f8c8d; margin-top: 10px; font-size: 14px;">No hay tablas en la base de datos</p>';
                    }
                    echo '</div>';

                } catch (Exception $e) {
                    echo '<div class="status-box status-error">';
                    echo '<strong>ERROR DE CONEXIÓN</strong>';
                    echo '</div>';

                    echo '<div class="error-details">';
                    echo '<div class="error-message">' . htmlspecialchars($e->getMessage()) . '</div>';

                    echo '<div class="info-label">POSIBLES SOLUCIONES</div>';
                    echo '<ul class="solutions-list">';
                    echo '<li>Verifica que MySQL esté corriendo en XAMPP</li>';
                    echo '<li>Verifica que la base de datos exista</li>';
                    echo '<li>Revisa las credenciales en el archivo .env</li>';
                    echo '</ul>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>

        <!-- BASE DE DATOS REMOTA -->
        <div class="card">
            <div class="card-header">
                <h2>Base de Datos REMOTA (BananaHosting)</h2>
            </div>
            <div class="card-body">
                <?php
                try {
                    $configRemote = require __DIR__ . '/../app/config/database.remote.php';

                    $dsn = "mysql:host={$configRemote['host']};port={$configRemote['port']};dbname={$configRemote['dbname']};charset={$configRemote['charset']}";
                    $pdo = new PDO($dsn, $configRemote['username'], $configRemote['password'], $configRemote['options']);

                    echo '<div class="status-box status-success">';
                    echo '<strong>CONEXIÓN EXITOSA</strong>';
                    echo '</div>';

                    echo '<div class="info-grid">';
                    echo '<div class="info-item">';
                    echo '<div class="info-label">Host</div>';
                    echo '<div class="info-value">' . htmlspecialchars($configRemote['host']) . '</div>';
                    echo '</div>';

                    echo '<div class="info-item">';
                    echo '<div class="info-label">Base de Datos</div>';
                    echo '<div class="info-value">' . htmlspecialchars($configRemote['dbname']) . '</div>';
                    echo '</div>';

                    echo '<div class="info-item">';
                    echo '<div class="info-label">Usuario</div>';
                    echo '<div class="info-value">' . htmlspecialchars($configRemote['username']) . '</div>';
                    echo '</div>';

                    $version = $pdo->query("SELECT VERSION()")->fetchColumn();
                    echo '<div class="info-item">';
                    echo '<div class="info-label">Versión</div>';
                    echo '<div class="info-value">' . htmlspecialchars($version) . '</div>';
                    echo '</div>';
                    echo '</div>';

                    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
                    echo '<div style="margin-top: 20px;">';
                    echo '<div class="info-label">TABLAS ENCONTRADAS (' . count($tables) . ')</div>';

                    if (count($tables) > 0) {
                        echo '<ul class="table-list">';
                        foreach ($tables as $table) {
                            echo '<li>' . htmlspecialchars($table) . '</li>';
                        }
                        echo '</ul>';
                    } else {
                        echo '<p style="color: #7f8c8d; margin-top: 10px; font-size: 14px;">No hay tablas en la base de datos</p>';
                    }
                    echo '</div>';

                } catch (Exception $e) {
                    echo '<div class="status-box status-error">';
                    echo '<strong>ERROR DE CONEXIÓN</strong>';
                    echo '</div>';

                    echo '<div class="error-details">';
                    echo '<div class="error-message">' . htmlspecialchars($e->getMessage()) . '</div>';

                    echo '<div class="info-label">POSIBLES CAUSAS</div>';
                    echo '<ul class="solutions-list">';
                    echo '<li>Tu IP no está en Remote MySQL (cPanel)</li>';
                    echo '<li>Contraseña incorrecta en el archivo .env</li>';
                    echo '<li>Host incorrecto (prueba con 216.246.46.71)</li>';
                    echo '<li>Firewall o puerto 3306 bloqueado</li>';
                    echo '</ul>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>

        <!-- INFORMACIÓN DE DEBUG -->
        <div class="card">
            <div class="card-header">
                <h2>Información de Debug</h2>
            </div>
            <div class="card-body">
                <div class="debug-grid">
                    <div class="debug-item">
                        <span class="debug-label">Directorio actual:</span>
                        <span class="debug-value"><?php echo htmlspecialchars(__DIR__); ?></span>
                    </div>
                    <div class="debug-item">
                        <span class="debug-label">Archivo .env:</span>
                        <span class="debug-value <?php echo file_exists($envFile) ? 'success' : 'error'; ?>">
                            <?php echo file_exists($envFile) ? 'Existe' : 'No existe'; ?>
                        </span>
                    </div>
                    <div class="debug-item">
                        <span class="debug-label">Config local:</span>
                        <span
                            class="debug-value <?php echo file_exists(__DIR__ . '/../app/config/database.local.php') ? 'success' : 'error'; ?>">
                            <?php echo file_exists(__DIR__ . '/../app/config/database.local.php') ? 'Existe' : 'No existe'; ?>
                        </span>
                    </div>
                    <div class="debug-item">
                        <span class="debug-label">Config remota:</span>
                        <span
                            class="debug-value <?php echo file_exists(__DIR__ . '/../app/config/database.remote.php') ? 'success' : 'error'; ?>">
                            <?php echo file_exists(__DIR__ . '/../app/config/database.remote.php') ? 'Existe' : 'No existe'; ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="back-section">
            <a href="index.php" class="btn btn-secondary">Volver a Herramientas</a>
        </div>
    </div>
</body>

</html>