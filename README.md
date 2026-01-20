# SGA-SEBANA - Sistema de Gestión Administrativa

**SGA-SEBANA** es un sistema de administración web diseñado para gestionar información desde un panel administrativo moderno, claro y responsive. Está construido con HTML, CSS, JavaScript (Vanilla) y Bootstrap 5, pensado para ejecutarse fácilmente en entornos locales como XAMPP y escalarse con backend (PHP + MySQL).

---

## Características principales

- Diseño responsive (funciona en PC, tablet y celular)
- Panel administrativo completo
- Gráficas y estadísticas integradas
- Tablas y formularios administrativos
- Pantallas de autenticación (login, registro, recuperación)
- No requiere procesos de compilación
- Estructura clara y fácil de modificar

---

## Tecnologías usadas

- **HTML5**
- **CSS3**
- **Bootstrap 5**
- **JavaScript Vanilla** (sin jQuery)
- **Chart.js** (gráficas)
- **Font Awesome** (iconos)
- **XAMPP** (servidor local)

---

## Estructura básica del proyecto
```
SGA-SEBANA/
├── css/            → Estilos del sistema
├── js/             → Lógica JavaScript (gráficas, interacciones)
├── vendor/         → Librerías externas (Bootstrap, Chart.js, etc.)
├── images/         → Imágenes e iconos
├── fonts/          → Fuentes tipográficas
├── index.html      → Dashboard principal
├── login.html      → Inicio de sesión
├── table.html      → Gestión de tablas
├── form.html       → Formularios
└── ...
```

---

## Requisitos previos

Antes de comenzar, necesitas tener instalado:

1. **XAMPP** - Servidor local que incluye Apache
2. **Git** (opcional, solo si vas a clonar el proyecto desde GitHub)

---

## Guía completa para principiantes

### Opción 1: Instalación manual (sin Git)

Esta opción es ideal si descargaste el proyecto como archivo ZIP o lo tienes en una carpeta.

#### Paso 1: Descargar e instalar XAMPP

1. Abre tu navegador web
2. Ve a: [https://www.apachefriends.org/](https://www.apachefriends.org/)
3. Haz clic en el botón de descarga para tu sistema operativo (Windows, Mac o Linux)
4. Espera a que se descargue el instalador (puede tardar unos minutos)
5. Una vez descargado, busca el archivo en tu carpeta de **Descargas**
6. Haz doble clic en el instalador para ejecutarlo
7. Si Windows te pregunta "¿Desea permitir que esta aplicación haga cambios?", haz clic en **Sí**
8. Sigue el asistente de instalación:
   - Haz clic en **Next** (Siguiente)
   - Selecciona los componentes (deja las opciones por defecto)
   - Elige la ubicación de instalación (se recomienda dejarlo en `C:\xampp`)
   - Haz clic en **Next** hasta finalizar
9. Al terminar, XAMPP se instalará en `C:\xampp`

#### Paso 2: Iniciar XAMPP

1. Busca **XAMPP Control Panel** en tu menú de inicio de Windows
2. Haz clic derecho sobre él y selecciona **Ejecutar como administrador**
3. Se abrirá una ventana con varios servicios (Apache, MySQL, FileZilla, etc.)
4. En la fila de **Apache**, haz clic en el botón **Start**
5. El botón se pondrá verde y dirá **Stop** cuando Apache esté funcionando
6. **Importante**: Deja esta ventana abierta mientras trabajes con el proyecto

**Nota**: Si Apache no inicia y muestra un error de puerto, probablemente otro programa esté usando el puerto 80. Cierra programas que puedan estar usando ese puerto e intenta nuevamente.

#### Paso 3: Ubicar la carpeta htdocs

1. Abre el **Explorador de archivos** de Windows (icono de carpeta en la barra de tareas)
2. En la barra de dirección, escribe o navega a: `C:\xampp\htdocs`
3. Presiona **Enter**
4. Esta carpeta (`htdocs`) es donde debes colocar todos tus proyectos web

**¿Qué es htdocs?**: Es la carpeta raíz del servidor Apache. Todo lo que coloques aquí será accesible desde tu navegador usando `http://localhost/`

#### Paso 4: Copiar el proyecto SGA-SEBANA a htdocs

**Opción A: Si tienes el proyecto en una carpeta**

1. Clona el proyecto desde la terminal o Github Desktop directamente en la carpeta de htdocs. 
1. Busca la carpeta del proyecto **SGA-SEBANA** en tu computadora
2. Haz clic derecho sobre la carpeta **SGA-SEBANA**
3. Selecciona **Copiar**
4. Ve a la carpeta `C:\xampp\htdocs`
5. Haz clic derecho en un espacio vacío
6. Selecciona **Pegar**
7. Ahora deberías tener: `C:\xampp\htdocs\SGA-SEBANA`

**Opción B: Si descargaste un archivo ZIP**

1. Busca el archivo **SGA-SEBANA.zip** en tu carpeta de Descargas
2. Haz clic derecho sobre el archivo ZIP
3. Selecciona **Extraer todo...**
4. En la ventana que aparece, cambia la ubicación de destino a: `C:\xampp\htdocs`
5. Haz clic en **Extraer**
6. Ahora deberías tener: `C:\xampp\htdocs\SGA-SEBANA`

#### Paso 5: Verificar que los archivos estén en el lugar correcto

1. Abre `C:\xampp\htdocs\SGA-SEBANA`
2. Dentro deberías ver las carpetas y archivos del proyecto:
   - Carpetas: `css`, `js`, `vendor`, `images`, `fonts`
   - Archivos: `index.html`, `login.html`, `table.html`, `form.html`, etc.

**Importante**: Si al abrir `SGA-SEBANA` encuentras otra carpeta `SGA-SEBANA` dentro, significa que tienes una carpeta anidada. Mueve el contenido de la carpeta interna directamente a `C:\xampp\htdocs\SGA-SEBANA`.

#### Paso 6: Abrir el proyecto en el navegador

1. Asegúrate de que Apache esté corriendo (luz verde en XAMPP Control Panel)
2. Abre tu navegador web (Chrome, Firefox, Edge, etc.)
3. En la barra de direcciones, escribe: `http://localhost/SGA-SEBANA/index.html`
4. Presiona **Enter**
5. Deberías ver la página principal del sistema administrativo

**Rutas alternativas que puedes usar**:
- `http://localhost/SGA-SEBANA/` (abre index.html automáticamente)
- `http://localhost/SGA-SEBANA/login.html` (para ver la página de login)
- `http://localhost/SGA-SEBANA/table.html` (para ver las tablas)
- `http://localhost/SGA-SEBANA/form.html` (para ver los formularios)

---

### Opción 2: Instalación con Git (método avanzado)

Esta opción es para quienes quieren clonar el proyecto directamente desde GitHub y trabajar con control de versiones.

#### Paso 1: Instalar Git

1. Ve a: [https://git-scm.com/downloads](https://git-scm.com/downloads)
2. Descarga Git para tu sistema operativo
3. Ejecuta el instalador descargado
4. Durante la instalación:
   - Deja todas las opciones por defecto
   - Haz clic en **Next** hasta finalizar
5. Git se instalará en tu sistema

#### Paso 2: Verificar que Git esté instalado

1. Presiona las teclas **Windows + R** en tu teclado
2. Escribe: `cmd` y presiona **Enter**
3. En la ventana negra (símbolo del sistema), escribe: `git --version`
4. Presiona **Enter**
5. Deberías ver algo como: `git version 2.40.0`
6. Si ves esto, Git está instalado correctamente

#### Paso 3: Instalar XAMPP (si no lo has hecho)

Sigue los pasos del **Paso 1** de la Opción 1 (arriba).

#### Paso 4: Navegar a la carpeta htdocs usando la terminal

1. Presiona las teclas **Windows + R**
2. Escribe: `cmd` y presiona **Enter**
3. En la ventana negra, escribe: `cd C:\xampp\htdocs`
4. Presiona **Enter**
5. Ahora estarás dentro de la carpeta htdocs desde la terminal

**¿Qué significa `cd`?**: Significa "change directory" (cambiar directorio). Es el comando para moverte entre carpetas en la terminal.

#### Paso 5: Clonar el repositorio desde GitHub

1. En la misma ventana de terminal (deberías estar en `C:\xampp\htdocs`), escribe:
```bash
git clone https://github.com/USUARIO/SGA-SEBANA.git
```

**Importante**: Reemplaza `USUARIO` con el nombre de usuario real de GitHub donde esté alojado el proyecto.

2. Presiona **Enter**
3. Git comenzará a descargar el proyecto
4. Verás mensajes como:
```
   Cloning into 'SGA-SEBANA'...
   remote: Enumerating objects: 100, done.
   remote: Counting objects: 100% (100/100), done.
```
5. Espera a que termine (puede tardar unos segundos)
6. Cuando veas el cursor parpadeante nuevamente, significa que terminó

#### Paso 6: Verificar que el proyecto se clonó correctamente

1. Abre el explorador de archivos
2. Ve a: `C:\xampp\htdocs\SGA-SEBANA`
3. Deberías ver todas las carpetas y archivos del proyecto

#### Paso 7: Iniciar Apache y abrir el proyecto

1. Abre **XAMPP Control Panel** como administrador
2. Haz clic en **Start** en la fila de Apache
3. Abre tu navegador
4. Escribe: `http://localhost/SGA-SEBANA/index.html`
5. Presiona **Enter**
6. El sistema debería cargarse correctamente

---

## Trabajar con el proyecto

### Cómo editar archivos del proyecto

1. Puedes usar cualquier editor de texto o código:
   - **Notepad++** (simple y gratuito)
   - **Visual Studio Code** (recomendado para desarrollo web)
   - **Sublime Text**
   - Incluso el Bloc de notas de Windows (no recomendado)

2. Para editar un archivo:
   - Abre tu editor de código
   - Ve a **Archivo** → **Abrir carpeta**
   - Navega a `C:\xampp\htdocs\SGA-SEBANA`
   - Selecciona la carpeta y ábrela
   - Ahora puedes ver y editar todos los archivos del proyecto

3. Haz los cambios que necesites en HTML, CSS o JavaScript

4. Guarda los cambios (Ctrl + S)

5. Recarga la página en tu navegador (F5) para ver los cambios

### Cómo ver los cambios en tiempo real

1. Mantén XAMPP Control Panel abierto con Apache corriendo
2. Mantén tu navegador abierto en `http://localhost/SGA-SEBANA/`
3. Mantén tu editor de código abierto
4. Cada vez que guardes cambios en tu editor, solo recarga el navegador (F5)

**Consejo**: Puedes tener tres ventanas abiertas simultáneamente:
- Tu editor de código (izquierda)
- Tu navegador (derecha)
- XAMPP Control Panel (minimizado en la barra de tareas)

---

## Comandos básicos de Git (si usaste la Opción 2)

Una vez que hayas clonado el proyecto con Git, puedes usar estos comandos para trabajar con el control de versiones.

### Configurar Git por primera vez

Antes de hacer commits, debes configurar tu nombre y correo (solo se hace una vez):

1. Abre la terminal (cmd)
2. Escribe estos comandos (reemplaza con tus datos):
```bash
git config --global user.name "Tu Nombre"
git config --global user.email "tuemail@ejemplo.com"
```

### Ver el estado de tus archivos

Este comando te muestra qué archivos has modificado:
```bash
git status
```

**Qué verás**:
- **Archivos en rojo**: archivos modificados que no has agregado aún
- **Archivos en verde**: archivos listos para hacer commit
- **Untracked files**: archivos nuevos que Git aún no está rastreando

### Agregar archivos al área de preparación

Antes de guardar cambios (commit), debes agregarlos:

**Agregar un archivo específico**:
```bash
git add nombre-archivo.html
```

**Agregar todos los archivos modificados**:
```bash
git add .
```

El punto `.` significa "todos los archivos en el directorio actual".

### Hacer un commit (guardar cambios)

Un commit es como tomar una "fotografía" del estado actual de tu proyecto:
```bash
git commit -m "Descripción breve de los cambios realizados"
```

**Ejemplos de buenos mensajes de commit**:
- `git commit -m "Agregado formulario de contacto"`
- `git commit -m "Corregido error en tabla de usuarios"`
- `git commit -m "Actualizado diseño del dashboard"`

### Subir cambios al repositorio remoto (GitHub)

Una vez que hayas hecho commits localmente, puedes subirlos a GitHub:
```bash
git push origin main
```

**Nota**: Algunos repositorios usan `master` en lugar de `main`. Si `main` no funciona, intenta:
```bash
git push origin master
```

### Descargar cambios del repositorio remoto

Si otras personas han hecho cambios en el repositorio de GitHub, puedes descargarlos:
```bash
git pull origin main
```

### Flujo de trabajo completo con Git

Este es el proceso que seguirás cada vez que trabajes en el proyecto:

1. **Abre la terminal y navega al proyecto**:
```bash
cd C:\xampp\htdocs\SGA-SEBANA
```

2. **Descarga los últimos cambios** (por si alguien más actualizó el proyecto):
```bash
git pull origin main
```

3. **Haz tus modificaciones** en los archivos con tu editor de código

4. **Verifica qué archivos cambiaron**:
```bash
git status
```

5. **Agrega los archivos modificados**:
```bash
git add .
```

6. **Haz un commit con un mensaje descriptivo**:
```bash
git commit -m "Descripción de los cambios"
```

7. **Sube los cambios a GitHub**:
```bash
git push origin main
```

---

## Solución de problemas comunes

### Apache no inicia en XAMPP

**Problema**: Al hacer clic en Start en Apache, se muestra un error o se detiene inmediatamente.

**Solución**:
1. Otro programa está usando el puerto 80 (probablemente algún servicio de Windows)
2. Cierralo si lo tienes abierto
3. O cambia el puerto de Apache:
   - En XAMPP Control Panel, haz clic en **Config** junto a Apache
   - Selecciona **httpd.conf**
   - Busca la línea que dice `Listen 80`
   - Cámbiala por `Listen 8080`
   - Guarda el archivo
   - Reinicia Apache
   - Ahora accede con: `http://localhost:8080/SGA-SEBANA/`

### La página no carga (error 404)

**Problema**: Al escribir `http://localhost/SGA-SEBANA/` aparece un error "Not Found" o "No se pudo encontrar".

**Solución**:
1. Verifica que Apache esté corriendo (luz verde en XAMPP)
2. Verifica que la carpeta esté en `C:\xampp\htdocs\SGA-SEBANA`
3. Verifica que dentro de esa carpeta esté el archivo `index.html`
4. Asegúrate de escribir bien la URL (mayúsculas y minúsculas importan en algunos casos)

### Los cambios no se ven en el navegador

**Problema**: Guardaste cambios en el código pero no se reflejan en el navegador.

**Solución**:
1. Presiona **Ctrl + F5** (recarga forzada) en lugar de solo F5
2. Esto fuerza al navegador a descargar los archivos nuevamente sin usar la caché
3. O presiona **Ctrl + Shift + Delete**, selecciona "Caché" y limpia el historial

### Git no se reconoce como comando

**Problema**: Al escribir `git` en la terminal aparece "git no se reconoce como un comando interno o externo".

**Solución**:
1. Cierra y vuelve a abrir la terminal después de instalar Git
2. O reinicia tu computadora
3. Verifica que Git se instaló correctamente yendo a: `C:\Program Files\Git`

### No puedo hacer push a GitHub

**Problema**: Al ejecutar `git push` aparece un error de autenticación.

**Solución**:
1. GitHub ya no permite contraseñas normales
2. Necesitas crear un **Personal Access Token**:
   - Ve a GitHub.com
   - Inicia sesión
   - Ve a Settings → Developer settings → Personal access tokens
   - Genera un nuevo token
   - Copia el token
   - Úsalo como contraseña cuando Git te lo pida

---

## Importante

- **SGA-SEBANA es frontend por ahora**
- No incluye lógica de base de datos
- Está preparado para integrarse con:
  - PHP
  - MySQL
  - APIs REST
  - Frameworks backend

---

## Objetivo del sistema

SGA-SEBANA busca servir como base sólida para un sistema administrativo, permitiendo:

- Visualización de datos
- Gestión de información
- Escalabilidad futura
- Integración con backend

---

## Recursos adicionales

### Documentación oficial

- **XAMPP**: [https://www.apachefriends.org/faq.html](https://www.apachefriends.org/faq.html)
- **Git**: [https://git-scm.com/doc](https://git-scm.com/doc)
- **Bootstrap 5**: [https://getbootstrap.com/docs/5.0/getting-started/introduction/](https://getbootstrap.com/docs/5.0/getting-started/introduction/)
  
---
