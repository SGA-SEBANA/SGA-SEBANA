<?php
// tools/index.php
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Herramientas - SGA-SEBANA</title>
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

        .alert {
            background: white;
            border: 1px solid #e1e8ed;
            border-left: 4px solid #5b7dff;
            padding: 15px 20px;
            margin-bottom: 25px;
            border-radius: 4px;
        }

        .alert p {
            font-size: 14px;
            color: #5a6c7d;
        }

        .alert strong {
            color: #2c3e50;
        }

        .alert.warning {
            border-left-color: #f39c12;
        }

        .tools-list {
            background: white;
            border: 1px solid #e1e8ed;
            border-radius: 4px;
            margin-bottom: 25px;
        }

        .tool-item {
            padding: 20px;
            border-bottom: 1px solid #e1e8ed;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: background 0.2s;
        }

        .tool-item:last-child {
            border-bottom: none;
        }

        .tool-item:hover {
            background: #f8f9fa;
        }

        .tool-item.disabled {
            opacity: 0.5;
            pointer-events: none;
        }

        .tool-info {
            flex: 1;
        }

        .tool-name {
            font-size: 16px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .tool-description {
            font-size: 14px;
            color: #7f8c8d;
            margin-bottom: 5px;
        }

        .tool-file {
            font-size: 12px;
            color: #95a5a6;
        }

        .tool-action {
            margin-left: 20px;
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
            border: none;
            cursor: pointer;
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
            border-color: #d1d8dd;
        }

        .btn-disabled {
            background: #e1e8ed;
            color: #95a5a6;
            cursor: not-allowed;
        }

        .btn-disabled:hover {
            background: #e1e8ed;
        }

        .back-section {
            background: white;
            border: 1px solid #e1e8ed;
            border-radius: 4px;
            padding: 20px;
            text-align: center;
        }

        .badge {
            display: inline-block;
            padding: 4px 8px;
            background: #e1e8ed;
            color: #5a6c7d;
            font-size: 11px;
            font-weight: 600;
            border-radius: 3px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="header-content">
            <h1>Herramientas de Desarrollo</h1>
            <p>SGA-SEBANA - Sistema de Gestion Administrativa</p>
        </div>
    </div>

    <div class="container">
        <div class="alert">
            <p><strong>Nota:</strong> Estas herramientas estan disenadas para facilitar el desarrollo y testing. No
                deben estar accesibles en produccion.</p>
        </div>

        <div class="tools-list">
            <div class="tool-item">
                <div class="tool-info">
                    <div class="tool-name">Test de Conexion de Base de Datos</div>
                    <div class="tool-description">Prueba las conexiones a las bases de datos local y remota. Verifica
                        credenciales y estado.</div>
                    <div class="tool-file">tools/test_db.php</div>
                </div>
                <div class="tool-action">
                    <a href="test_db.php" class="btn">Ejecutar</a>
                </div>
            </div>

            <div class="tool-item disabled">
                <div class="tool-info">
                    <div class="tool-name">Migracion de Archivos HTML <span class="badge">Deshabilitado</span></div>
                    <div class="tool-description">Migra archivos HTML estaticos a la estructura MVC. Ajusta rutas y
                        genera archivos PHP.</div>
                    <div class="tool-file">tools/migrate_html.php</div>
                </div>
                <div class="tool-action">
                    <button class="btn btn-disabled" disabled>Ejecutar</button>
                </div>
            </div>

            <div class="tool-item disabled">
                <div class="tool-info">
                    <div class="tool-name">Ejecutar Migraciones <span class="badge">Proximamente</span></div>
                    <div class="tool-description">Ejecuta automaticamente archivos de migracion SQL para crear tablas.
                    </div>
                    <div class="tool-file">tools/run_migrations.php</div>
                </div>
                <div class="tool-action">
                    <button class="btn btn-disabled" disabled>Ejecutar</button>
                </div>
            </div>

            <div class="tool-item disabled">
                <div class="tool-info">
                    <div class="tool-name">Datos de Prueba <span class="badge">Proximamente</span></div>
                    <div class="tool-description">Llena la base de datos con datos de prueba para desarrollo y testing.
                    </div>
                    <div class="tool-file">tools/seed_database.php</div>
                </div>
                <div class="tool-action">
                    <button class="btn btn-disabled" disabled>Ejecutar</button>
                </div>
            </div>

            <div class="tool-item disabled">
                <div class="tool-info">
                    <div class="tool-name">Backup de Base de Datos <span class="badge">Proximamente</span></div>
                    <div class="tool-description">Genera respaldos automaticos de la base de datos en formato SQL.</div>
                    <div class="tool-file">tools/backup_db.php</div>
                </div>
                <div class="tool-action">
                    <button class="btn btn-disabled" disabled>Ejecutar</button>
                </div>
            </div>

            <div class="tool-item disabled">
                <div class="tool-info">
                    <div class="tool-name">Visor de Logs <span class="badge">Proximamente</span></div>
                    <div class="tool-description">Visualiza y analiza los logs del sistema, errores y eventos
                        importantes.</div>
                    <div class="tool-file">tools/view_logs.php</div>
                </div>
                <div class="tool-action">
                    <button class="btn btn-disabled" disabled>Ejecutar</button>
                </div>
            </div>
        </div>

        <div class="alert warning">
            <p><strong>Advertencia:</strong> Recuerda eliminar o proteger esta carpeta antes de subir el proyecto a
                produccion.</p>
        </div>

        <div class="back-section">
            <a href="../public/" class="btn btn-secondary">Volver al Proyecto</a>
        </div>
    </div>
</body>

</html>