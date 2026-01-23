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
├── README.md       → Este archivo (documentación principal)
└── BRANCH.md       → Guía de trabajo con ramas (branches)
```

---

## Equipo de Desarrollo

Este proyecto es desarrollado por:

- **Julián Clot Córdoba** (1-1926-0815) - Rama: `julian-clot`
- **Joel Josué Peralta Pérez** (1-1922-0621) - Rama: `joel-peralta`
- **Derlis Hernández Carranza** (7-0200-0717) - Rama: `derlis-hernandez`
- **Jorge Luis Castrillo Molina** (2-0872-0752) - Rama: `jorge-castrillo`

Cada integrante trabaja en su propia rama (branch) y debe seguir las instrucciones detalladas en **BRANCH.md**.

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
   - Archivos: `index.html`, `login.html`, `table.html`, `form.html`, `README.md`, `BRANCH.md`, etc.

**¿Por qué clonar en lugar de descargar?**

Al clonar el proyecto con Git:
- Mantienes conexión con el repositorio de GitHub
- Puedes descargar actualizaciones fácilmente con `git pull`
- Puedes subir tus cambios con `git push`
- Trabajas con control de versiones (puedes volver a versiones anteriores si algo sale mal)
- Colaboras con otras personas en el mismo proyecto
- **Puedes trabajar en tu propia rama (branch) sin afectar el código principal**

### Paso 5: Configurar tu rama de trabajo

**MUY IMPORTANTE**: Cada integrante del equipo debe trabajar en su propia rama (branch). **NUNCA trabajes directamente en la rama `main`**.

1. Navega al proyecto en la terminal (si no estás allí):
```bash
cd C:\xampp\htdocs\SGA-SEBANA
```

2. Verifica en qué rama estás:
```bash
git branch
```

Probablemente verás:
```
* main
```

3. Descarga la información de todas las ramas desde GitHub:
```bash
git fetch origin
```

4. Cambia a tu rama personal según tu nombre:

**Para Julián Clot Córdoba**:
```bash
git checkout julian-clot
```

**Para Joel Josué Peralta Pérez**:
```bash
git checkout joel-peralta
```

**Para Derlis Hernández Carranza**:
```bash
git checkout derlis-hernandez
```

**Para Jorge Luis Castrillo Molina**:
```bash
git checkout jorge-castrillo
```

5. Si tu rama no existe aún, créala con:
```bash
git checkout -b tu-nombre-de-rama
```

Por ejemplo:
```bash
git checkout -b julian-clot
```

6. Sube tu rama a GitHub (solo la primera vez):
```bash
git push -u origin tu-nombre-de-rama
```

Por ejemplo:
```bash
git push -u origin julian-clot
```

**Para más detalles sobre cómo trabajar con ramas, consulta el archivo BRANCH.md que contiene una guía completa y detallada.**

### Paso 6: Configurar Git con tu información

Antes de hacer commits, debes configurar tu nombre y correo (solo se hace una vez):
```bash
git config --global user.name "Tu Nombre Completo"
git config --global user.email "tuemail@ejemplo.com"
```

**Ejemplo para Julián**:
```bash
git config --global user.name "Julián Clot Córdoba"
git config --global user.email "julian.clot@ejemplo.com"
```

### Paso 7: Iniciar Apache en XAMPP

1. Busca **XAMPP Control Panel** en tu menú de inicio de Windows
2. Haz clic derecho sobre él y selecciona **Ejecutar como administrador**
3. Se abrirá una ventana con varios servicios (Apache, MySQL, FileZilla, etc.)
4. En la fila de **Apache**, haz clic en el botón **Start**
5. El botón se pondrá verde y dirá **Stop** cuando Apache esté funcionando
6. **Importante**: Deja esta ventana abierta mientras trabajes con el proyecto

**Nota**: Si Apache no inicia y muestra un error de puerto, probablemente otro programa esté usando el puerto 80. Ve a la sección de **Solución de problemas** más abajo.

### Paso 8: Abrir el proyecto en el navegador

1. Asegúrate de que Apache esté corriendo (luz verde en XAMPP Control Panel)
2. Abre tu navegador web (Chrome, Firefox, Edge, etc.)
3. En la barra de direcciones, escribe: `http://localhost/SGA-SEBANA/public/`
4. Presiona **Enter**
5. Deberías ver la página principal del sistema administrativo

**Rutas alternativas que puedes usar**:
- `http://localhost/SGA-SEBANA/public/` (abre index.html automáticamente)
- `http://localhost/SGA-SEBANA/public/login.html` (para ver la página de login)
- `http://localhost/SGA-SEBANA/public/table.html` (para ver las tablas)
- `http://localhost/SGA-SEBANA/public/form.html` (para ver los formularios)

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

7. **IMPORTANTE**: Antes de editar, asegúrate de estar en tu rama personal:
   - Abre la terminal integrada en Visual Studio Code (Ctrl + Ñ o View → Terminal)
   - Escribe: `git branch`
   - Debes ver un asterisco (*) junto al nombre de tu rama, NO junto a `main`

8. Haz clic en cualquier archivo para editarlo (por ejemplo, `index.html`)

9. Realiza los cambios que necesites

10. Guarda los cambios presionando **Ctrl + S**

11. Recarga la página en tu navegador (F5) para ver los cambios

### Cómo ver los cambios en tiempo real

Para trabajar eficientemente, mantén estas ventanas abiertas simultáneamente:

1. **XAMPP Control Panel** (minimizado en la barra de tareas con Apache corriendo)
2. **Tu editor de código** (Visual Studio Code, por ejemplo) ocupando la mitad izquierda de la pantalla
3. **Tu navegador** con `http://localhost/SGA-SEBANA/public/` ocupando la mitad derecha de la pantalla

**Flujo de trabajo**:
1. Editas código en Visual Studio Code (lado izquierdo)
2. Guardas con Ctrl + S
3. Recargas el navegador con F5 (lado derecho)
4. Ves inmediatamente los cambios

**Consejo**: En Visual Studio Code, puedes instalar la extensión "Live Server" para que los cambios se reflejen automáticamente sin necesidad de recargar manualmente.

---

## Flujo de trabajo con Git y Branches

**IMPORTANTE**: Este proyecto usa ramas (branches) de Git para que cada integrante trabaje de forma independiente.

### Reglas fundamentales:

1. **NUNCA trabajes directamente en la rama `main`**
2. Siempre trabaja en tu rama personal (`julian-clot`, `joel-peralta`, `derlis-hernandez` o `jorge-castrillo`)
3. Actualiza tu rama con `main` todos los días antes de trabajar
4. Haz commits frecuentes con mensajes descriptivos
5. Sube tus cambios a GitHub regularmente

### Rutina diaria básica:
```bash
# 1. Navegar al proyecto
cd C:\xampp\htdocs\SGA-SEBANA

# 2. Verificar que estás en tu rama
git branch

# 3. Actualizar la rama main
git checkout main
git pull origin main

# 4. Volver a tu rama
git checkout tu-nombre-de-rama

# 5. Fusionar cambios de main en tu rama
git merge main

# 6. Trabajar en tus archivos...

# 7. Guardar cambios
git add .
git commit -m "Descripción clara de los cambios"

# 8. Subir a GitHub
git push origin tu-nombre-de-rama
```

**Para una guía completa y detallada sobre cómo trabajar con branches, resolver conflictos, y más, consulta el archivo `BRANCH.md`.**

---

## Comandos de Git más usados

### Ver en qué rama estás:
```bash
git branch
```

### Cambiar a tu rama:
```bash
git checkout tu-nombre-de-rama
```

### Ver qué archivos modificaste:
```bash
git status
```

### Guardar cambios:
```bash
git add .
git commit -m "Descripción de cambios"
git push origin tu-nombre-de-rama
```

### Actualizar tu rama con main:
```bash
git checkout main
git pull origin main
git checkout tu-nombre-de-rama
git merge main
```

**Para más comandos y explicaciones detalladas, consulta `BRANCH.md`.**

---

## Estructura de ramas del proyecto
```
main (rama principal - código oficial)
├── julian-clot (Julián Clot Córdoba)
├── joel-peralta (Joel Josué Peralta Pérez)
├── derlis-hernandez (Derlis Hernández Carranza)
└── jorge-castrillo (Jorge Luis Castrillo Molina)
```

- **main**: Contiene el código oficial y estable. Solo se actualiza mediante Pull Requests revisados.
- **Ramas personales**: Cada integrante trabaja en su rama y sube sus cambios sin afectar a los demás.

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
11. Ahora accede al proyecto con: `http://localhost:8080/SGA-SEBANA/public/`

### La página no carga (error 404)

**Problema**: Al escribir `http://localhost/SGA-SEBANA/public/` aparece "Not Found" o "No se pudo encontrar la página".

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
   - Asegúrate de escribir correctamente: `http://localhost/SGA-SEBANA/public`
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

### Estoy trabajando en la rama main por error

**Problema**: Te diste cuenta de que estás haciendo cambios en `main` en lugar de tu rama personal.

**Solución**:

1. **Si NO has hecho commit aún**:
```bash
# Guarda tus cambios temporalmente
git stash

# Cambia a tu rama
git checkout tu-nombre-de-rama

# Recupera tus cambios en tu rama
git stash pop
```

2. **Si YA hiciste commit en main**:
```bash
# Deshacer el último commit (mantiene los cambios)
git reset --soft HEAD~1

# Cambia a tu rama
git checkout tu-nombre-de-rama

# Ahora haz commit en tu rama
git add .
git commit -m "Descripción de cambios"
```

### No puedo hacer push a GitHub (error de autenticación)

**Problema**: Al ejecutar `git push` aparece un error de usuario/contraseña o autenticación.

**Solución**: Consulta la sección "No puedo hacer push a GitHub" en `BRANCH.md` para instrucciones detalladas sobre cómo crear un Personal Access Token.

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

## Documentación adicional

- **BRANCH.md**: Guía completa y detallada sobre cómo trabajar con ramas (branches), resolver conflictos, y flujo de trabajo diario con Git.

---

## Recursos adicionales

### Documentación oficial

- **XAMPP**: [https://www.apachefriends.org/faq.html](https://www.apachefriends.org/faq.html)
- **Git**: [https://git-scm.com/doc](https://git-scm.com/doc)
- **GitHub**: [https://docs.github.com/](https://docs.github.com/)
- **Bootstrap 5**: [https://getbootstrap.com/docs/5.0/getting-started/introduction/](https://getbootstrap.com/docs/5.0/getting-started/introduction/)

### Tutoriales útiles

- **Git básico**: [https://www.youtube.com/watch?v=HiXLkL42tMU](https://www.youtube.com/watch?v=HiXLkL42tMU)
- **XAMPP tutorial**: [https://www.youtube.com/watch?v=h6DEDm7C37A](https://www.youtube.com/watch?v=h6DEDm7C37A)
- **Bootstrap 5**: [https://www.youtube.com/watch?v=O_9u1P5YjVc](https://www.youtube.com/watch?v=O_9u1P5YjVc)
