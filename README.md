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
2. **Git** - Para clonar el proyecto desde GitHub y trabajar con control de versiones

---

## Guía completa de instalación

### Paso 1: Descargar e instalar XAMPP

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

### Paso 2: Instalar Git

1. Ve a: [https://git-scm.com/downloads](https://git-scm.com/downloads)
2. Descarga Git para tu sistema operativo
3. Ejecuta el instalador descargado
4. Durante la instalación:
   - Deja todas las opciones por defecto
   - Haz clic en **Next** hasta finalizar
5. Git se instalará en tu sistema

### Paso 3: Verificar que Git esté instalado

1. Presiona las teclas **Windows + R** en tu teclado
2. Escribe: `cmd` y presiona **Enter**
3. En la ventana negra (símbolo del sistema), escribe: `git --version`
4. Presiona **Enter**
5. Deberías ver algo como: `git version 2.40.0`
6. Si ves esto, Git está instalado correctamente

### Paso 4: Clonar el proyecto desde GitHub directamente en htdocs

**Importante**: Este proyecto está diseñado para trabajarse con control de versiones desde GitHub. Por eso, lo clonaremos directamente en la carpeta `htdocs` de XAMPP.

#### 4.1 Abrir la terminal en la carpeta htdocs

**Opción A: Desde el Explorador de archivos**

1. Abre el Explorador de archivos de Windows
2. Navega a: `C:\xampp\htdocs`
3. Haz clic derecho en un espacio vacío dentro de la carpeta `htdocs`
4. Selecciona **Abrir en Terminal** o **Git Bash Here** (si instalaste Git correctamente)
5. Se abrirá una ventana de terminal ya ubicada en `htdocs`

**Opción B: Desde la terminal (CMD)**

1. Presiona **Windows + R**
2. Escribe: `cmd` y presiona **Enter**
3. En la ventana de terminal, escribe: `cd C:\xampp\htdocs`
4. Presiona **Enter**
5. Ahora estarás dentro de la carpeta htdocs

#### 4.2 Clonar el repositorio

1. En la terminal (asegúrate de estar en `C:\xampp\htdocs`), escribe:
```bash
git clone https://github.com/USUARIO/SGA-SEBANA.git
```

**Importante**: Reemplaza `USUARIO` con el nombre de usuario real de GitHub donde esté alojado el proyecto.

**Ejemplo real**:
```bash
git clone https://github.com/juanperez/SGA-SEBANA.git
```

2. Presiona **Enter**
3. Git comenzará a descargar el proyecto desde GitHub
4. Verás mensajes como:
```
   Cloning into 'SGA-SEBANA'...
   remote: Enumerating objects: 100, done.
   remote: Counting objects: 100% (100/100), done.
   remote: Compressing objects: 100% (80/80), done.
   Receiving objects: 100% (100/100), 1.5 MiB | 2.0 MiB/s, done.
   Resolving deltas: 100% (40/40), done.
```
5. Espera a que termine la descarga (puede tardar desde unos segundos hasta un par de minutos, dependiendo del tamaño del proyecto y tu conexión a internet)
6. Cuando veas el cursor parpadeante nuevamente, significa que el proceso terminó

#### 4.3 Verificar que el proyecto se clonó correctamente

1. En la misma terminal, escribe: `dir` (en Windows) o `ls` (en Mac/Linux)
2. Presiona **Enter**
3. Deberías ver una carpeta llamada **SGA-SEBANA** en la lista
4. O simplemente abre el Explorador de archivos y navega a `C:\xampp\htdocs\SGA-SEBANA`
5. Dentro deberías ver todas las carpetas y archivos del proyecto:
   - Carpetas: `css`, `js`, `vendor`, `images`, `fonts`
   - Archivos: `index.html`, `login.html`, `table.html`, `form.html`, etc.

**¿Por qué clonar en lugar de descargar?**

Al clonar el proyecto con Git:
- Mantienes conexión con el repositorio de GitHub
- Puedes descargar actualizaciones fácilmente con `git pull`
- Puedes subir tus cambios con `git push`
- Trabajas con control de versiones (puedes volver a versiones anteriores si algo sale mal)
- Colaboras con otras personas en el mismo proyecto

### Paso 5: Iniciar Apache en XAMPP

1. Busca **XAMPP Control Panel** en tu menú de inicio de Windows
2. Haz clic derecho sobre él y selecciona **Ejecutar como administrador**
3. Se abrirá una ventana con varios servicios (Apache, MySQL, FileZilla, etc.)
4. En la fila de **Apache**, haz clic en el botón **Start**
5. El botón se pondrá verde y dirá **Stop** cuando Apache esté funcionando
6. **Importante**: Deja esta ventana abierta mientras trabajes con el proyecto

**Nota**: Si Apache no inicia y muestra un error de puerto, probablemente otro programa esté usando el puerto 80. Ve a la sección de **Solución de problemas** más abajo.

### Paso 6: Abrir el proyecto en el navegador

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

## Configurar Git por primera vez

Antes de hacer commits, debes configurar tu nombre y correo electrónico (solo se hace una vez):

1. Abre la terminal (cmd)
2. Escribe estos comandos (reemplaza con tus datos reales):
```bash
git config --global user.name "Tu Nombre Completo"
```
```bash
git config --global user.email "tuemail@ejemplo.com"
```

**Ejemplo**:
```bash
git config --global user.name "Juan Pérez"
git config --global user.email "juan.perez@gmail.com"
```

3. Presiona **Enter** después de cada comando

**Nota**: Este nombre y correo aparecerán en todos tus commits. Usa el mismo correo que tienes registrado en tu cuenta de GitHub.

---

## Trabajar con el proyecto

### Cómo editar archivos del proyecto

1. Descarga e instala un editor de código. Recomendamos:
   - **Visual Studio Code** (el más popular): [https://code.visualstudio.com/](https://code.visualstudio.com/)
   - **Sublime Text**: [https://www.sublimetext.com/](https://www.sublimetext.com/)
   - **Notepad++**: [https://notepad-plus-plus.org/](https://notepad-plus-plus.org/)

2. Abre Visual Studio Code (o tu editor preferido)

3. Ve a **Archivo** → **Abrir carpeta** (o presiona Ctrl + K, Ctrl + O)

4. Navega a `C:\xampp\htdocs\SGA-SEBANA`

5. Selecciona la carpeta **SGA-SEBANA** y haz clic en **Seleccionar carpeta**

6. Ahora puedes ver todos los archivos del proyecto en el panel izquierdo

7. Haz clic en cualquier archivo para editarlo (por ejemplo, `index.html`)

8. Realiza los cambios que necesites

9. Guarda los cambios presionando **Ctrl + S**

10. Recarga la página en tu navegador (F5) para ver los cambios

### Cómo ver los cambios en tiempo real

Para trabajar eficientemente, mantén estas ventanas abiertas simultáneamente:

1. **XAMPP Control Panel** (minimizado en la barra de tareas con Apache corriendo)
2. **Tu editor de código** (Visual Studio Code, por ejemplo) ocupando la mitad izquierda de la pantalla
3. **Tu navegador** con `http://localhost/SGA-SEBANA/` ocupando la mitad derecha de la pantalla

**Flujo de trabajo**:
1. Editas código en Visual Studio Code (lado izquierdo)
2. Guardas con Ctrl + S
3. Recargas el navegador con F5 (lado derecho)
4. Ves inmediatamente los cambios

**Consejo**: En Visual Studio Code, puedes instalar la extensión "Live Server" para que los cambios se reflejen automáticamente sin necesidad de recargar manualmente.

---

## Comandos básicos de Git para trabajo diario

Una vez que hayas clonado el proyecto, usarás estos comandos constantemente para trabajar con control de versiones.

### 1. Ver el estado de tus archivos

Este comando te muestra qué archivos has modificado, agregado o eliminado:
```bash
git status
```

**Qué verás**:
- **Archivos en rojo**: archivos modificados que no has agregado al área de preparación
- **Archivos en verde**: archivos listos para hacer commit
- **Untracked files**: archivos nuevos que Git aún no está rastreando

**Cuándo usarlo**: Úsalo frecuentemente para saber en qué estado está tu proyecto.

### 2. Agregar archivos al área de preparación

Antes de guardar cambios (commit), debes agregar los archivos modificados:

**Agregar un archivo específico**:
```bash
git add index.html
```

**Agregar todos los archivos modificados** (lo más común):
```bash
git add .
```

El punto `.` significa "todos los archivos modificados en el directorio actual y subdirectorios".

**Cuándo usarlo**: Después de hacer cambios y antes de hacer commit.

### 3. Hacer un commit (guardar cambios localmente)

Un commit es como tomar una "fotografía" del estado actual de tu proyecto:
```bash
git commit -m "Descripción clara y breve de los cambios"
```

**Ejemplos de buenos mensajes de commit**:
```bash
git commit -m "Agregado formulario de registro de usuarios"
git commit -m "Corregido error en tabla de productos"
git commit -m "Actualizado diseño del dashboard principal"
git commit -m "Implementadas gráficas en el panel de estadísticas"
```

**Malas prácticas** (evita estos mensajes):
```bash
git commit -m "cambios"
git commit -m "fix"
git commit -m "asdf"
git commit -m "actualizacion"
```

**Cuándo usarlo**: Cada vez que completes una funcionalidad o arregles algo específico.

### 4. Subir cambios al repositorio remoto (GitHub)

Una vez que hayas hecho uno o varios commits localmente, súbelos a GitHub:
```bash
git push origin main
```

**Nota**: Algunos repositorios antiguos usan `master` en lugar de `main`. Si recibes un error, intenta:
```bash
git push origin master
```

**Qué hace**: Sube todos los commits que has hecho localmente al repositorio en GitHub.

**Cuándo usarlo**: Al final de tu sesión de trabajo o cuando quieras respaldar tus cambios en la nube.

### 5. Descargar cambios del repositorio remoto

Si otras personas han hecho cambios en GitHub, o si tú trabajaste desde otra computadora, descarga los últimos cambios:
```bash
git pull origin main
```

**Qué hace**: Descarga los commits más recientes del repositorio de GitHub y los fusiona con tu código local.

**Cuándo usarlo**: Al inicio de cada sesión de trabajo, antes de empezar a editar archivos.

---

## Flujo de trabajo completo (rutina diaria)

Este es el proceso que seguirás **cada vez** que trabajes en el proyecto:

### Al comenzar tu sesión de trabajo:

1. **Abre la terminal** y navega al proyecto:
```bash
cd C:\xampp\htdocs\SGA-SEBANA
```

2. **Descarga los últimos cambios** del repositorio (por si alguien más actualizó el proyecto):
```bash
git pull origin main
```

3. **Inicia Apache** en XAMPP Control Panel

4. **Abre el proyecto** en tu editor de código (Visual Studio Code)

5. **Abre el proyecto en el navegador**: `http://localhost/SGA-SEBANA/`

### Durante tu trabajo:

6. **Haz las modificaciones** que necesites en los archivos

7. **Guarda los archivos** (Ctrl + S)

8. **Recarga el navegador** (F5) para ver los cambios

9. Repite los pasos 6-8 tantas veces como necesites

### Al terminar tu sesión de trabajo:

10. **Verifica qué archivos cambiaron**:
```bash
git status
```

11. **Agrega los archivos modificados**:
```bash
git add .
```

12. **Haz un commit con un mensaje descriptivo**:
```bash
git commit -m "Descripción clara de lo que hiciste"
```

Por ejemplo:
```bash
git commit -m "Agregada página de perfil de usuario"
```

13. **Sube los cambios a GitHub**:
```bash
git push origin main
```

14. **Cierra Apache** en XAMPP Control Panel (botón Stop)

15. **Cierra tu editor de código**

---

## Comandos de Git adicionales (útiles)

### Ver el historial de commits
```bash
git log
```

Esto muestra todos los commits realizados en el proyecto. Presiona `Q` para salir.

**Versión más compacta**:
```bash
git log --oneline
```

### Ver diferencias en archivos modificados
```bash
git diff
```

Esto muestra exactamente qué líneas cambiaron en cada archivo.

### Descartar cambios en un archivo específico

Si modificaste un archivo pero quieres volver a la versión anterior:
```bash
git checkout -- nombre-archivo.html
```

**Advertencia**: Esto borrará todos los cambios no guardados en ese archivo.

### Crear una rama nueva (para trabajar en funcionalidades sin afectar el código principal)
```bash
git branch nombre-de-la-rama
git checkout nombre-de-la-rama
```

O en un solo comando:
```bash
git checkout -b nombre-de-la-rama
```

**Ejemplo**:
```bash
git checkout -b feature-nuevo-login
```

### Volver a la rama principal
```bash
git checkout main
```

---

## Solución de problemas comunes

### Apache no inicia en XAMPP

**Problema**: Al hacer clic en Start en Apache, se muestra un error o se detiene inmediatamente.

**Causa**: Otro programa está usando el puerto 80 (probablemente Skype, algún servicio de Windows, o IIS).

**Solución 1**: Cerrar el programa que usa el puerto 80
1. Cierra Skype si lo tienes abierto
2. Reinicia XAMPP
3. Intenta iniciar Apache nuevamente

**Solución 2**: Cambiar el puerto de Apache
1. En XAMPP Control Panel, haz clic en **Config** junto a Apache
2. Selecciona **httpd.conf**
3. Se abrirá un archivo de texto
4. Presiona Ctrl + F y busca: `Listen 80`
5. Cámbiala por: `Listen 8080`
6. Busca también: `ServerName localhost:80`
7. Cámbiala por: `ServerName localhost:8080`
8. Guarda el archivo (Ctrl + S)
9. Cierra el archivo
10. Reinicia Apache en XAMPP
11. Ahora accede al proyecto con: `http://localhost:8080/SGA-SEBANA/`

### La página no carga (error 404)

**Problema**: Al escribir `http://localhost/SGA-SEBANA/` aparece "Not Found" o "No se pudo encontrar la página".

**Soluciones**:

1. **Verifica que Apache esté corriendo**:
   - Abre XAMPP Control Panel
   - Asegúrate de que el botón de Apache esté verde y diga "Stop"

2. **Verifica la ruta del proyecto**:
   - Abre el Explorador de archivos
   - Navega a `C:\xampp\htdocs`
   - Asegúrate de que exista la carpeta `SGA-SEBANA`
   - Dentro de esa carpeta debe estar `index.html`

3. **Verifica la URL**:
   - Asegúrate de escribir correctamente: `http://localhost/SGA-SEBANA/`
   - Revisa mayúsculas y minúsculas
   - No olvides las barras `/`

4. **Verifica que no haya carpetas anidadas**:
   - A veces al clonar queda: `C:\xampp\htdocs\SGA-SEBANA\SGA-SEBANA\`
   - Debe ser: `C:\xampp\htdocs\SGA-SEBANA\index.html`

### Los cambios no se ven en el navegador

**Problema**: Guardaste cambios en el código pero no se reflejan al recargar la página.

**Soluciones**:

1. **Recarga forzada**:
   - Presiona **Ctrl + Shift + R** o **Ctrl + F5**
   - Esto fuerza al navegador a descargar todo nuevamente sin usar la caché

2. **Limpia la caché del navegador**:
   - Presiona **Ctrl + Shift + Delete**
   - Selecciona "Imágenes y archivos en caché"
   - Haz clic en "Borrar datos"

3. **Verifica que guardaste el archivo**:
   - En tu editor, asegúrate de que el archivo no tenga un punto blanco o asterisco en la pestaña
   - Presiona Ctrl + S para guardar

4. **Verifica que estés editando el archivo correcto**:
   - Asegúrate de que el archivo que editas esté en `C:\xampp\htdocs\SGA-SEBANA\`

### Git no se reconoce como comando

**Problema**: Al escribir `git` en la terminal aparece "git no se reconoce como un comando interno o externo".

**Soluciones**:

1. **Cierra y vuelve a abrir la terminal**:
   - Cierra completamente la ventana de CMD o PowerShell
   - Abre una nueva terminal
   - Intenta nuevamente

2. **Reinicia tu computadora**:
   - A veces es necesario reiniciar después de instalar Git

3. **Verifica la instalación de Git**:
   - Ve a `C:\Program Files\Git`
   - Si la carpeta no existe, Git no se instaló correctamente
   - Vuelve a instalar Git desde [https://git-scm.com/downloads](https://git-scm.com/downloads)

### No puedo hacer push a GitHub (error de autenticación)

**Problema**: Al ejecutar `git push` aparece un error de usuario/contraseña o autenticación.

**Causa**: GitHub ya no permite usar contraseñas normales desde agosto de 2021. Debes usar un Personal Access Token (PAT).

**Solución**:

1. **Ve a GitHub.com** e inicia sesión

2. **Haz clic en tu foto de perfil** (esquina superior derecha)

3. Selecciona **Settings**

4. En el menú lateral izquierdo, baja hasta **Developer settings**

5. Haz clic en **Personal access tokens** → **Tokens (classic)**

6. Haz clic en **Generate new token** → **Generate new token (classic)**

7. **Configura el token**:
   - **Note**: Escribe algo como "Token para SGA-SEBANA"
   - **Expiration**: Selecciona "No expiration" o elige un tiempo
   - **Select scopes**: Marca la casilla **repo** (esto incluye todos los permisos necesarios)

8. Baja hasta el final y haz clic en **Generate token**

9. **IMPORTANTE**: Copia el token que aparece (es una cadena larga de letras y números)
   - Guárdalo en un lugar seguro (Bloc de notas, archivo de texto)
   - **No podrás volver a verlo después de cerrar esta página**

10. **Usa el token en lugar de tu contraseña**:
    - Cuando Git te pida usuario: escribe tu usuario de GitHub
    - Cuando Git te pida contraseña: **pega el token** (no tu contraseña normal)

**Consejo**: Guarda el token en un lugar seguro. Lo necesitarás cada vez que hagas push (a menos que configures credenciales guardadas).

### Error: "Your branch is behind"

**Problema**: Al hacer `git push` aparece un mensaje como "Your branch is behind 'origin/main'".

**Causa**: Alguien más (u otra computadora) subió cambios al repositorio que tú no tienes localmente.

**Solución**:

1. **Descarga los cambios primero**:
```bash
git pull origin main
```

2. **Si hay conflictos**, Git te lo dirá. Deberás resolverlos manualmente:
   - Abre los archivos en conflicto en tu editor
   - Busca las líneas que empiezan con `<<<<<<<`, `=======`, `>>>>>>>`
   - Decide qué código conservar
   - Elimina los marcadores de conflicto
   - Guarda el archivo

3. **Agrega los archivos resueltos**:
```bash
git add .
```

4. **Haz commit de la fusión**:
```bash
git commit -m "Resueltos conflictos de fusión"
```

5. **Sube los cambios**:
```bash
git push origin main
```

---

## Objetivo del sistema

SGA-SEBANA busca servir como base sólida para un sistema administrativo, permitiendo:

- Visualización de datos
- Gestión de información
- Escalabilidad futura
- Integración con backend (PHP, MySQL, APIs REST)

---

## Importante

- **SGA-SEBANA es actualmente frontend**
- No incluye lógica de base de datos (por ahora)
- Está preparado para integrarse con:
  - PHP
  - MySQL
  - APIs REST
  - Frameworks backend

---

## Recursos adicionales

### Documentación oficial

- **XAMPP**: [https://www.apachefriends.org/faq.html](https://www.apachefriends.org/faq.html)
- **Git**: [https://git-scm.com/doc](https://git-scm.com/doc)
- **GitHub**: [https://docs.github.com/](https://docs.github.com/)
- **Bootstrap 5**: [https://getbootstrap.com/docs/5.0/getting-started/introduction/](https://getbootstrap.com/docs/5.0/getting-started/introduction/)

### Editores de código recomendados

- **Visual Studio Code**: [https://code.visualstudio.com/](https://code.visualstudio.com/) (gratuito, muy recomendado)
- **Sublime Text**: [https://www.sublimetext.com/](https://www.sublimetext.com/)
- **Notepad++**: [https://notepad-plus-plus.org/](https://notepad-plus-plus.org/) (simple y ligero)

### Tutoriales útiles

- **Git básico**: [https://www.youtube.com/watch?v=HiXLkL42tMU](https://www.youtube.com/watch?v=HiXLkL42tMU)
- **XAMPP tutorial**: [https://www.youtube.com/watch?v=h6DEDm7C37A](https://www.youtube.com/watch?v=h6DEDm7C37A)
- **Bootstrap 5**: [https://www.youtube.com/watch?v=O_9u1P5YjVc](https://www.youtube.com/watch?v=O_9u1P5YjVc)
