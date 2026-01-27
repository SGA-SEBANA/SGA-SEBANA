# Guía de Configuración de Base de Datos - SGA-SEBANA

**Última actualización:** 27 de enero, 2026  
**Autores:** Equipo de Desarrollo SGA-SEBANA

**IMPORTANTE - CREDENCIALES:**
Este documento NO contiene credenciales reales por razones de seguridad. Los valores como hostname, nombre de base de datos, usuario y contraseña deben ser solicitados al administrador del proyecto a través de un canal seguro (NO por GitHub).

---

## Índice

1. [Introducción](#introducción)
2. [Diferencia entre Base de Datos Local y Remota](#diferencia-entre-base-de-datos-local-y-remota)
3. [Requisitos Previos](#requisitos-previos)
4. [Configuración del Archivo .env](#configuración-del-archivo-env)
5. [Configuración de Base de Datos Local](#configuración-de-base-de-datos-local)
6. [Configuración de Base de Datos Remota](#configuración-de-base-de-datos-remota)
7. [Configuración de phpMyAdmin para Acceso Remoto](#configuración-de-phpmyadmin-para-acceso-remoto)
8. [Configuración con MySQL Workbench](#configuración-con-mysql-workbench)
9. [Herramientas de Desarrollo (Carpeta tools/)](#herramientas-de-desarrollo)
10. [Guía Básica de phpMyAdmin](#guía-básica-de-phpmyadmin)
11. [Solución de Problemas Comunes](#solución-de-problemas-comunes)
12. [Comandos Útiles](#comandos-útiles)

---

## Introducción

Este documento proporciona instrucciones completas para que todos los miembros del equipo configuren correctamente las bases de datos del proyecto SGA-SEBANA en sus máquinas locales.

El proyecto utiliza **dos bases de datos**:
- **Base de datos LOCAL:** Para desarrollo en tu computadora (XAMPP)
- **Base de datos REMOTA:** Alojada en BananaHosting (producción/compartida)

---

## Diferencia entre Base de Datos Local y Remota

### Base de Datos Local (XAMPP)

**Ubicación:** Tu computadora  
**Servidor:** localhost  
**Puerto:** 3306  
**Usuario:** root  
**Contraseña:** (vacía)  
**Nombre de BD:** nfvxeqzb_sga_sbna

**Características:**
- Solo tú puedes acceder a ella
- Cambios NO afectan a otros desarrolladores
- Ideal para desarrollo y pruebas
- Datos se borran si desinstalas XAMPP
- Más rápida (sin latencia de red)

**Cuándo usar:**
- Desarrollo diario
- Pruebas de nuevas funcionalidades
- Experimentación sin riesgo

### Base de Datos Remota (BananaHosting)

**Ubicación:** Servidor de BananaHosting  
**Servidor:** [solicitar al administrador]  
**Puerto:** 3306  
**Usuario:** [solicitar al administrador]  
**Contraseña:** [solicitar al administrador]  
**Nombre de BD:** [solicitar al administrador]

**Características:**
- Accesible por todo el equipo
- Cambios afectan a todos
- Datos persistentes y respaldados
- Conexión más lenta (internet)
- Requiere permisos de acceso remoto

**Cuándo usar:**
- Pruebas de integración
- Demos al cliente
- Verificación final antes de producción
- Cuando necesites datos compartidos

---

## Requisitos Previos

Antes de comenzar, asegúrate de tener instalado:

1. **XAMPP** (incluye Apache y MySQL/MariaDB)
   - Descarga: https://www.apachefriends.org/
   - Versión recomendada: 8.2.x o superior

2. **Git** (para clonar el proyecto)
   - Descarga: https://git-scm.com/downloads

3. **Editor de código** (recomendado: Visual Studio Code)
   - Descarga: https://code.visualstudio.com/

4. **Proyecto clonado** en `C:\xampp\htdocs\SGA-SEBANA`

---

## Configuración del Archivo .env

### Paso 0: Obtener Credenciales

**ANTES de crear el archivo .env, necesitas obtener las credenciales de la base de datos remota.**

**Cómo obtener las credenciales:**

1. Contacta al administrador del proyecto (ver sección de Contacto al final)
2. Solicita las siguientes credenciales de la base de datos remota:
   - Hostname (servidor)
   - Puerto (generalmente 3306)
   - Nombre de la base de datos
   - Usuario
   - Contraseña
3. Las credenciales deben compartirse por un canal seguro:
   - Mensaje directo en Slack/Discord
   - Email cifrado
   - Videollamada
   - **NUNCA por GitHub, Pull Requests o Issues públicos**

### Paso 1: Crear el archivo .env

El archivo `.env` contiene las credenciales de las bases de datos y NO debe subirse a GitHub por seguridad.

1. Abre tu proyecto en Visual Studio Code
2. En la **raíz del proyecto** (mismo nivel que la carpeta `public/`), crea un archivo llamado `.env`
3. Copia y pega el siguiente contenido:

```properties
# ============================================
# Base de datos REMOTA (BananaHosting)
# ============================================
DB_HOST=[solicitar_al_administrador]
DB_PORT=3306
DB_DATABASE=[solicitar_al_administrador]
DB_USERNAME=[solicitar_al_administrador]
DB_PASSWORD=[solicitar_al_administrador]
DB_CHARSET=utf8mb4

# ============================================
# Base de datos LOCAL (XAMPP)
# ============================================
DB_LOCAL_HOST=localhost
DB_LOCAL_PORT=3306
DB_LOCAL_DATABASE=[nombre_de_tu_base_local]
DB_LOCAL_USERNAME=root
DB_LOCAL_PASSWORD=
DB_LOCAL_CHARSET=utf8mb4
```

4. **Guarda el archivo** (Ctrl + S)

### Paso 2: Verificar que .env esté en .gitignore

Abre el archivo `.gitignore` en la raíz del proyecto y verifica que contenga:

```gitignore
# Variables de entorno
.env

# Configuraciones con contraseñas
phpMyAdmin/config.inc.php
```

Si no existe `.gitignore`, créalo con el contenido anterior.

### Paso 3: Verificar la estructura de archivos

Tu proyecto debe tener esta estructura:

```
SGA-SEBANA/
├── .env                              <-- CREAR (no subir a GitHub)
├── .gitignore                        <-- Verificar que incluya .env
├── app/
│   └── config/
│       ├── database.local.php        <-- Config base local
│       └── database.remote.php       <-- Config base remota
├── tools/
│   ├── index.php                     <-- Menú de herramientas
│   └── test_db.php           <-- Test de conexión
└── public/
    └── index.php
```

---

## Configuración de Base de Datos Local

### Paso 1: Iniciar XAMPP

1. Abre **XAMPP Control Panel**
2. Haz clic en **Start** junto a **Apache**
3. Haz clic en **Start** junto a **MySQL**
4. Ambos deben mostrar un fondo verde

### Paso 2: Crear la base de datos local

**Opción A: Desde phpMyAdmin**

1. Abre tu navegador
2. Ve a: `http://localhost/phpmyadmin`
3. Haz clic en **Nueva** (o **New**) en el menú lateral izquierdo
4. En el campo "Nombre de la base de datos", escribe: `nfvxeqzb_sga_sbna` 
5. En "Cotejamiento", selecciona: `utf8mb4_general_ci`
6. Haz clic en **Crear**

**Opción B: Desde la terminal**

Abre CMD o PowerShell y ejecuta:

```bash
cd C:\xampp\mysql\bin
mysql -u root -e "CREATE DATABASE sga_sebana CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;"
```

### Paso 3: Verificar la creación

1. Ve a: `http://localhost/phpmyadmin`
2. En el menú lateral izquierdo, deberías ver tu base de datos
3. Haz clic en ella
4. Debe aparecer: "No hay tablas en la base de datos" (es correcto, las tablas se crearán después con migraciones)

### Paso 4: Verificar conexión local

1. Ve a: `http://localhost/SGA-SEBANA/tools/`
2. Haz clic en **Ejecutar** en "Test de Conexion de Base de Datos"
3. Deberías ver: **CONEXIÓN LOCAL EXITOSA**

---

## Configuración de Base de Datos Remota

La base de datos remota **ya está creada** en BananaHosting. Solo necesitas configurar el acceso desde tu computadora.

### Paso 1: Obtener tu IP pública

1. Ve a: https://www.whatismyip.com/
2. Copia tu dirección IPv4 (ejemplo: `181.224.123.45`)

### Paso 2: Solicitar acceso remoto

**IMPORTANTE:** Solo el administrador del hosting puede hacer esto.

1. Envía tu IP pública al administrador del proyecto
2. Solicita que te agreguen a la lista de IPs permitidas

**Nota:** Si tu IP cambia (conexión dinámica), deberás repetir este paso.

### Paso 3: Verificar conexión remota

1. Ve a: `http://localhost/SGA-SEBANA/tools/`
2. Haz clic en **Ejecutar** en "Test de Conexion de Base de Datos"
3. Deberías ver: **CONEXIÓN REMOTA EXITOSA**

Si aparece un error, consulta la sección [Solución de Problemas](#solución-de-problemas-comunes).

---

## Configuración de phpMyAdmin para Acceso Remoto

Puedes configurar phpMyAdmin para administrar la base de datos remota desde tu navegador local.

### Paso 1: Localizar el archivo de configuración

1. Abre el Explorador de archivos de Windows
2. Ve a: `C:\xampp\phpMyAdmin\`
3. Busca el archivo: `config.inc.php`

### Paso 2: Hacer respaldo del archivo

Antes de modificar, copia `config.inc.php` y guárdalo como `config.inc.php.backup`

### Paso 3: Editar config.inc.php

1. Abre `config.inc.php` con Visual Studio Code o Notepad++
2. **Busca esta sección** (cerca de la línea 30):

```php
$i = 0;

/*
 * First server
 */
$i++;
$cfg['Servers'][$i]['auth_type'] = 'config';
$cfg['Servers'][$i]['user'] = 'root';
$cfg['Servers'][$i]['password'] = '';
```

3. **DESPUÉS de esa configuración, AGREGA** (no reemplaces):
Cambia los valores que dicen solicitar al administrador por los datos de .env (sebanacr)

```php
/*
 * Second server - BananaHosting Remote (SEBANA CR)
 */
$i++;
$cfg['Servers'][$i]['auth_type'] = 'config';
$cfg['Servers'][$i]['host'] = '[solicitar_al_administrador]';
$cfg['Servers'][$i]['port'] = '3306';
$cfg['Servers'][$i]['user'] = '[solicitar_al_administrador]';
$cfg['Servers'][$i]['password'] = '[solicitar_al_administrador]';
$cfg['Servers'][$i]['verbose'] = 'BananaHosting - SEBANA CR';
$cfg['Servers'][$i]['extension'] = 'mysqli';
$cfg['Servers'][$i]['compress'] = false;
$cfg['Servers'][$i]['AllowNoPassword'] = false;
```

4. **Guarda el archivo** (Ctrl + S)

### Paso 4: Acceder a phpMyAdmin con servidor remoto

1. Cierra todas las pestañas de phpMyAdmin abiertas
2. Abre una nueva pestaña
3. Ve a: `http://localhost/phpmyadmin`
4. Ahora verás un **selector de servidor** en la pantalla de inicio
5. Selecciona: **BananaHosting - SEBANA CR**
6. Ya no necesitas escribir usuario/contraseña (están en el config)
7. Deberías ver la base de datos remota

### Paso 5: Cambiar entre servidores

Para cambiar de servidor:

1. Haz clic en el logo de phpMyAdmin (arriba a la izquierda)
2. O ve directamente a: `http://localhost/phpmyadmin`
3. Selecciona el servidor que desees usar:
   - **127.0.0.1** = Base de datos local (XAMPP)
   - **BananaHosting - SEBANA CR** = Base de datos remota

---

## Configuración con MySQL Workbench

MySQL Workbench es una alternativa profesional a phpMyAdmin con más funcionalidades.

### Paso 1: Descargar e instalar

1. Ve a: https://dev.mysql.com/downloads/workbench/
2. Descarga la versión para Windows
3. Ejecuta el instalador
4. Sigue el asistente de instalación (deja las opciones por defecto)

### Paso 2: Configurar conexión LOCAL

1. Abre **MySQL Workbench**
2. Haz clic en el **+** junto a "MySQL Connections"
3. Configura así:

```
Connection Name: XAMPP Local - SGA-SEBANA
Connection Method: Standard (TCP/IP)

Hostname: 127.0.0.1
Port: 3306
Username: root
Password: [dejar vacío o clic en "Store in Vault" y dejar vacío]

Default Schema: nfvxeqzb_sga_sbna
```

4. Haz clic en **Test Connection**
5. Si dice "Successfully made the MySQL connection", haz clic en **OK**

### Paso 3: Configurar conexión REMOTA

1. Haz clic en el **+** junto a "MySQL Connections" nuevamente
2. Configura así:

```
Connection Name: BananaHosting - SEBANA CR
Connection Method: Standard (TCP/IP)

Hostname: [solicitar_al_administrador]
Port: 3306
Username: [solicitar_al_administrador]
Password: [clic en "Store in Vault"] → [solicitar_al_administrador]

Default Schema: [solicitar_al_administrador]
```

3. Haz clic en **Test Connection**
4. Si dice "Successfully made the MySQL connection", haz clic en **OK**

### Paso 4: Usar las conexiones

Para conectarte:

1. Haz **doble clic** en la conexión que desees usar
2. Se abrirá una nueva ventana con el editor SQL
3. En el panel izquierdo verás las bases de datos y tablas
4. Puedes ejecutar consultas SQL en el editor central

---

## Herramientas de Desarrollo

El proyecto incluye una carpeta `tools/` con scripts útiles para desarrollo.

### Ubicación

```
SGA-SEBANA/
└── tools/
    ├── index.php              <-- Menú principal de herramientas
    ├── test_db.php     <-- Test de conexión de BD
    └── migrate_html.php       <-- (deshabilitado)
```

### Acceder al menú de herramientas

1. Asegúrate de que Apache esté corriendo en XAMPP
2. Abre tu navegador
3. Ve a: `http://localhost/SGA-SEBANA/tools/`
4. Verás un menú con todas las herramientas disponibles

### Herramientas disponibles

#### 1. Test de Conexión de Base de Datos

**Archivo:** `tools/test_db.php`

**Qué hace:**
- Verifica la conexión a la base de datos local
- Verifica la conexión a la base de datos remota
- Muestra información del servidor (versión, tablas, etc.)
- Muestra errores detallados si algo falla

**Cuándo usar:**
- Primera vez que configuras el proyecto
- Después de cambiar credenciales en .env
- Cuando tienes problemas de conexión
- Para verificar que todo funciona correctamente

**Cómo usar:**
1. Ve a: `http://localhost/SGA-SEBANA/tools/`
2. Haz clic en **Ejecutar** en "Test de Conexion de Base de Datos"
3. Revisa los resultados

#### 2. Otras herramientas (próximamente)

Las siguientes herramientas estarán disponibles en futuras actualizaciones:

- **Ejecutar Migraciones:** Crear tablas automáticamente
- **Datos de Prueba:** Llenar la BD con datos de ejemplo
- **Backup de BD:** Respaldar la base de datos
- **Visor de Logs:** Ver errores y eventos del sistema

### Importante sobre tools/

**ADVERTENCIA:** La carpeta `tools/` contiene scripts que pueden ejecutar operaciones sensibles. **NUNCA debe estar accesible en producción.**

Antes de subir el proyecto al servidor de producción:
1. Elimina la carpeta `tools/`
2. O protégela con autenticación HTTP
3. O agrégala al archivo `.htaccess` para bloquear el acceso

---

## Guía Básica de phpMyAdmin

phpMyAdmin es una herramienta web para administrar bases de datos MySQL/MariaDB.

### Acceder a phpMyAdmin

**Local:**
```
http://localhost/phpmyadmin
```

**Remota (si configuraste el config.inc.php):**
```
http://localhost/phpmyadmin
→ Selecciona "BananaHosting - SEBANA CR"
```

### Navegación básica

#### Panel izquierdo (Lista de bases de datos)
- Muestra todas las bases de datos disponibles
- Haz clic en una para verla
- Dentro de cada base, verás las tablas

#### Panel superior (Pestañas)
- **Estructura:** Ver tablas y sus columnas
- **SQL:** Ejecutar consultas SQL
- **Buscar:** Buscar datos en tablas
- **Importar:** Subir archivos SQL
- **Exportar:** Descargar respaldo de la BD

### Operaciones comunes

#### Crear una tabla

1. Selecciona la base de datos en el panel izquierdo
2. Haz clic en la pestaña **Estructura**
3. Scroll hacia abajo hasta "Crear tabla"
4. Escribe el nombre de la tabla
5. Define el número de columnas
6. Haz clic en **Continuar**
7. Define cada columna (nombre, tipo, longitud, etc.)
8. Haz clic en **Guardar**

#### Ejecutar una consulta SQL

1. Selecciona la base de datos
2. Haz clic en la pestaña **SQL**
3. Escribe o pega tu consulta SQL
4. Haz clic en **Continuar** o presiona **Ctrl + Enter**

Ejemplo:
```sql
SELECT * FROM usuarios WHERE rol = 'admin';
```

#### Insertar datos

**Opción A: Con interfaz gráfica**

1. Selecciona la tabla
2. Haz clic en **Insertar**
3. Llena los campos
4. Haz clic en **Continuar**

**Opción B: Con SQL**

1. Pestaña **SQL**
2. Escribe:
```sql
INSERT INTO usuarios (nombre, email, rol) 
VALUES ('Juan Pérez', 'juan@example.com', 'admin');
```
3. Haz clic en **Continuar**

#### Ver datos de una tabla

1. Selecciona la tabla en el panel izquierdo
2. Haz clic en **Examinar** o **Browse**
3. Verás todos los registros en forma de tabla

#### Editar un registro

1. En la vista de datos (Examinar)
2. Haz clic en el ícono de **lápiz** (Editar) junto al registro
3. Modifica los valores
4. Haz clic en **Continuar**

#### Eliminar un registro

1. En la vista de datos (Examinar)
2. Haz clic en el ícono de **X** (Eliminar) junto al registro
3. Confirma la eliminación

#### Exportar base de datos (respaldo)

1. Selecciona la base de datos
2. Haz clic en **Exportar**
3. Método: **Rápido**
4. Formato: **SQL**
5. Haz clic en **Continuar**
6. Se descargará un archivo `.sql`

#### Importar base de datos

1. Selecciona la base de datos
2. Haz clic en **Importar**
3. Haz clic en **Seleccionar archivo**
4. Elige tu archivo `.sql`
5. Haz clic en **Continuar**
6. Espera a que termine la importación

### Consejos útiles

**Atajos de teclado:**
- `Ctrl + Enter` = Ejecutar consulta SQL
- `Ctrl + S` = Guardar cambios

**Búsqueda rápida:**
- Usa el cuadro de búsqueda arriba del panel izquierdo para encontrar tablas rápidamente

**Paginación:**
- Por defecto muestra 25 registros por página
- Cambia esto en los controles de paginación abajo de la tabla

**Filtros:**
- Usa la pestaña **Buscar** para filtrar datos sin escribir SQL

---

## Solución de Problemas Comunes

### Error: "No se encontró el archivo .env"

**Causa:** El archivo `.env` no existe o está en la ubicación incorrecta.

**Solución:**
1. Verifica que `.env` esté en la **raíz del proyecto** (no en `public/` ni en `app/`)
2. Debe estar al mismo nivel que las carpetas `app/`, `public/`, `tools/`
3. Si no existe, créalo siguiendo la sección [Configuración del Archivo .env](#configuración-del-archivo-env)

---

### Error: "Can't connect to MySQL server" (Local)

**Causa:** MySQL no está corriendo en XAMPP.

**Solución:**
1. Abre **XAMPP Control Panel**
2. Verifica que **MySQL** tenga un fondo verde
3. Si no, haz clic en **Start** junto a MySQL
4. Si no inicia, puede haber un conflicto de puertos

**Si sigue sin funcionar:**

1. En XAMPP Control Panel, haz clic en **Config** junto a MySQL
2. Selecciona **my.ini**
3. Busca la línea: `port = 3306`
4. Si otro programa usa el puerto 3306, cámbialo a `port = 3307`
5. Guarda y reinicia MySQL
6. Actualiza el archivo `.env`:
```properties
DB_LOCAL_PORT=3307
```

---

### Error: "Can't connect to MySQL server" (Remoto)

**Causa 1:** Tu IP no está en la lista de acceso remoto.

**Solución:**
1. Ve a: https://www.whatismyip.com/
2. Copia tu IP
3. Envíala al administrador del proyecto para que te agregue
4. O si eres el administrador, agrégala en cPanel → Remote MySQL

**Causa 2:** Firewall o antivirus bloqueando el puerto 3306.

**Solución:**
1. Desactiva temporalmente el firewall/antivirus
2. Prueba la conexión
3. Si funciona, agrega una excepción para el puerto 3306
4. Vuelve a activar el firewall/antivirus

**Causa 3:** Host incorrecto.

**Solución:**
Contacta al administrador del proyecto para obtener el hostname correcto. A veces puede ser necesario usar la dirección IP directa en lugar del nombre de dominio.

---

### Error: "Access denied for user"

**Causa:** Usuario o contraseña incorrectos.

**Solución para base LOCAL:**
1. El usuario por defecto de XAMPP es: `root`
2. La contraseña por defecto está vacía
3. Verifica el archivo `.env`:
```properties
DB_LOCAL_USERNAME=root
DB_LOCAL_PASSWORD=
```

**Solución para base REMOTA:**
1. Verifica que copiaste bien la contraseña en `.env`
2. No debe tener espacios al inicio o final
3. Respeta mayúsculas y minúsculas
4. Si sigue fallando, contacta al administrador para verificar credenciales

---

### Error: "Unknown database 'nfvxeqzb_sga_sbna'"

**Causa:** La base de datos no existe.

**Solución para base LOCAL:**
1. Ve a: `http://localhost/phpmyadmin`
2. Haz clic en **Nueva**
3. Nombre: `nfvxeqzb_sga_sbna`
4. Cotejamiento: `utf8mb4_general_ci`
5. Haz clic en **Crear**

**Solución para base REMOTA:**
La base remota ya está creada. Si ves este error, verifica que el nombre en `.env` sea exactamente el que te proporcionó el administrador del proyecto.

---

### Error: "SQLSTATE[HY000] [2002] Connection timed out"

**Causa:** No se puede conectar al servidor remoto (timeout).

**Solución:**
1. Verifica tu conexión a internet
2. Verifica que tu IP esté en Remote MySQL (cPanel)
3. Si usas VPN, desactívala temporalmente
4. Contacta al administrador si el problema persiste

---

### phpMyAdmin no muestra el selector de servidor

**Causa:** La configuración del servidor remoto no se agregó correctamente en `config.inc.php`.

**Solución:**
1. Verifica que agregaste la configuración DESPUÉS del servidor local
2. Verifica que incrementaste `$i` antes de agregar el nuevo servidor:
```php
$i++;  // <-- IMPORTANTE
$cfg['Servers'][$i]['auth_type'] = 'config';
```
3. Guarda el archivo y cierra todas las pestañas de phpMyAdmin
4. Abre una nueva pestaña en `http://localhost/phpmyadmin`

---

### Puerto 3306 ya está en uso

**Causa:** Otro programa (MySQL instalado por separado, PostgreSQL, etc.) está usando el puerto 3306.

**Solución:**
1. Cierra el programa que usa el puerto
2. O cambia el puerto de MySQL en XAMPP:
   - Config → my.ini → `port = 3307`
3. Actualiza todos los archivos de configuración con el nuevo puerto

---

### Conexión remota muy lenta

**Causa:** Latencia de red (normal en conexiones remotas).

**Solución:**
- Para desarrollo diario, usa la base de datos LOCAL
- Solo usa la remota cuando necesites:
  - Compartir datos con el equipo
  - Probar en producción
  - Demos al cliente

---

## Comandos Útiles

### MySQL desde línea de comandos

**Conectar a base local:**
```bash
cd C:\xampp\mysql\bin
mysql -u root
```

**Conectar a base remota:**
```bash
mysql -h [host_remoto] -u [usuario_remoto] -p
# Luego ingresa la contraseña cuando se te solicite
```

**Crear base de datos:**
```sql
CREATE DATABASE nombre_base_datos CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
```

**Ver bases de datos:**
```sql
SHOW DATABASES;
```

**Seleccionar base de datos:**
```sql
USE nombre_base_datos;
```

**Ver tablas:**
```sql
SHOW TABLES;
```

**Ver estructura de tabla:**
```sql
DESCRIBE nombre_tabla;
```

**Exportar base de datos:**
```bash
cd C:\xampp\mysql\bin
mysqldump -u root nombre_base_datos > backup.sql
```

**Importar base de datos:**
```bash
cd C:\xampp\mysql\bin
mysql -u root nombre_base_datos < backup.sql
```

---

## Resumen de Configuración

### Checklist para nuevos miembros

- [ ] XAMPP instalado y funcionando (Apache + MySQL)
- [ ] Proyecto clonado en `C:\xampp\htdocs\SGA-SEBANA`
- [ ] **Credenciales de BD remota solicitadas al administrador**
- [ ] Archivo `.env` creado en la raíz con las credenciales correctas
- [ ] Base de datos local creada en phpMyAdmin
- [ ] Test de conexión local exitoso (`http://localhost/SGA-SEBANA/tools/`)
- [ ] IP agregada a Remote MySQL en cPanel (solicitar al administrador)
- [ ] Test de conexión remota exitoso
- [ ] (Opcional) phpMyAdmin configurado para acceso remoto
- [ ] (Opcional) MySQL Workbench instalado y configurado

### Información rápida de referencia

**Base de datos LOCAL:**
```
Host: localhost
Puerto: 3306
BD: [nombre_de_tu_base_local]
Usuario: root
Contraseña: (vacía)
```

**Base de datos REMOTA:**
```
Host: [solicitar_al_administrador]
Puerto: 3306
BD: [solicitar_al_administrador]
Usuario: [solicitar_al_administrador]
Contraseña: [solicitar_al_administrador]
```

**URLs importantes:**
```
Proyecto: http://localhost/SGA-SEBANA/
Herramientas: http://localhost/SGA-SEBANA/tools/
phpMyAdmin: http://localhost/phpmyadmin
```

---

## Contacto y Soporte

Si tienes problemas que no se resuelven con esta guía:

1. Revisa los mensajes de error completos
2. Consulta la sección de [Solución de Problemas](#solución-de-problemas-comunes)
3. Contacta al equipo en el canal de desarrollo

**Equipo de Desarrollo:**
- Julián Clot Córdoba (1-1926-0815)
- Joel Josué Peralta Pérez (1-1922-0621)
- Derlis Hernández Carranza (7-0200-0717)
- Jorge Luis Castrillo Molina (2-0872-0752)

---

**Última actualización:** 27 de enero, 2026  
**Versión del documento:** 1.0
