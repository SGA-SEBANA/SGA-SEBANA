# ARQUITECTURA DEL PROYECTO — SGA-SEBANA

**Última actualización:** 23 de enero, 2026  
**Autores:** Julián Clot Córdoba, Joel Josué Peralta Pérez, Derlis Hernández Carranza, Jorge Luis Castrillo Molina

---

## Índice

1. [Resumen Ejecutivo](#1-resumen-ejecutivo)
2. [Arquitectura del Sistema](#2-arquitectura-del-sistema)
3. [Estructura de Carpetas](#3-estructura-de-carpetas)
4. [Convenciones y Estándares](#4-convenciones-y-estándares)
5. [Anatomía de un Módulo](#5-anatomía-de-un-módulo)
6. [Modelo de Trabajo con Branches](#6-modelo-de-trabajo-con-branches)
7. [Gestión de Historias de Usuario](#7-gestión-de-historias-de-usuario)
8. [Proceso de Integración a Main](#8-proceso-de-integración-a-main)
9. [Resolución de Conflictos](#9-resolución-de-conflictos)

---

## 1. Resumen Ejecutivo

### Decisiones Arquitectónicas

- **Arquitectura:** MVC (Model-View-Controller) modular sin framework
- **Patrón:** Front Controller centralizado en `public/index.php`
- **Tecnologías:** PHP 8.2, MySQL 8.0, HTML5, CSS3, JavaScript, Bootstrap 5
- **Autoloading:** Composer con PSR-4
- **Plantilla:** Template base compartido (NO duplicar por módulo)
- **Base de Datos:** Migraciones SQL versionadas

### Filosofía de Trabajo

Cada integrante trabaja **directamente en su rama personal** sin crear branches adicionales. La integración se realiza mediante **Pull Requests** desde la rama personal hacia `main` cuando una historia de usuario está completa y probada.

---

## 2. Arquitectura del Sistema

### 2.1 Patrón MVC con Front Controller
```
Usuario → Apache → public/index.php (Front Controller)
                        ↓
                   Router (analiza URL)
                        ↓
                   Controller (lógica de negocio)
                        ↓
                   Model (acceso a datos) ←→ MySQL
                        ↓
                   View (HTML) → Usuario
```

### 2.2 Componentes Principales

| Componente | Ubicación | Función |
|------------|-----------|---------|
| **Front Controller** | `public/index.php` | Punto de entrada único |
| **Router** | `app/core/Router.php` | Mapea URLs a Controllers |
| **Database** | `app/core/Database.php` | Conexión PDO |
| **ControllerBase** | `app/core/ControllerBase.php` | Clase base para controllers |
| **ModelBase** | `app/core/ModelBase.php` | Clase base para models |
| **Template Base** | `public/templates/base.html.php` | Plantilla HTML compartida |

### 2.3 Justificación

- **Modularidad:** Cada módulo es independiente
- **Simplicidad:** Sin curva de aprendizaje de frameworks
- **Control:** Entendemos cada componente
- **Trabajo paralelo:** Mínimos conflictos entre módulos
- **Escalabilidad:** Fácil agregar funcionalidades

---

## 3. Estructura de Carpetas

### 3.1 Árbol del Proyecto
```
SGA-SEBANA/
│
├── public/                          # DocumentRoot (única carpeta expuesta)
│   ├── index.php                    # Front Controller
│   ├── .htaccess                    # Reglas Apache
│   ├── templates/
│   │   ├── base.html.php           # Plantilla base compartida
│   │   └── partials/               # Componentes (navbar, footer)
│   └── assets/
│       ├── css/
│       │   └── main.css            # Estilos globales
│       ├── js/
│       │   └── main.js             # JavaScript global
│       └── img/                    # Imágenes
│
├── app/                             # Código de aplicación
│   ├── core/                        # Núcleo del sistema
│   │   ├── Router.php
│   │   ├── ControllerBase.php
│   │   ├── ModelBase.php
│   │   └── Database.php
│   │
│   ├── shared/                      # Código compartido
│   │   ├── helpers.php
│   │   ├── validators.php
│   │   ├── Auth.php
│   │   └── Bitacora.php
│   │
│   ├── modules/                     # Módulos de negocio
│   │   ├── afiliados/
│   │   │   ├── Controllers/
│   │   │   ├── Models/
│   │   │   ├── Views/
│   │   │   └── routes.php
│   │   ├── usuarios/
│   │   ├── bitacora/
│   │   └── reportes/
│   │
│   ├── migrations/                  # Scripts SQL
│   │   ├── 001_create_users.sql
│   │   ├── 002_create_roles.sql
│   │   └── 003_create_afiliados.sql
│   │
│   ├── seeds/                       # Datos iniciales
│   └── config/                      # Configuración
│       ├── config.php
│       └── database.php
│
├── docs/                            # Documentación
├── composer.json
├── .gitignore
└── README.md
```

### 3.2 Reglas de Organización

#### Hacer

- Usar `public/templates/base.html.php` para todas las vistas
- Crear módulos completos en `app/modules/{nombre}/`
- Usar `app/shared/` para funciones comunes
- Numerar migraciones secuencialmente

#### No Hacer

- NO duplicar la plantilla base por módulo
- NO poner lógica de negocio en `Views/`
- NO exponer archivos fuera de `public/`
- NO modificar `app/core/` sin consenso

---

## 4. Convenciones y Estándares

### 4.1 Nomenclatura

| Elemento | Convención | Ejemplo |
|----------|------------|---------|
| Clases | PascalCase | `AfiliadoController` |
| Métodos | camelCase | `obtenerTodos()` |
| Variables | camelCase | `$nombreCompleto` |
| Tablas DB | snake_case | `afiliados` |
| Columnas DB | snake_case | `fecha_nacimiento` |
| Archivos de vistas | snake_case.php | `list.php`, `form.php` |

### 4.2 Estructura de Controllers
```php
class AfiliadosController extends ControllerBase
{
    public function index()      // Listar todos
    public function create()     // Mostrar formulario crear
    public function store()      // Procesar crear (POST)
    public function show($id)    // Mostrar uno
    public function edit($id)    // Mostrar formulario editar
    public function update($id)  // Procesar editar (POST)
    public function delete($id)  // Eliminar
}
```

### 4.3 Estructura de Models
```php
class Afiliado extends ModelBase
{
    protected $table = 'afiliados';
    
    public function all()             // SELECT *
    public function find($id)         // SELECT WHERE id
    public function create($data)     // INSERT
    public function update($id, $data) // UPDATE
    public function delete($id)       // DELETE
}
```

### 4.4 Rutas
```php
// app/modules/afiliados/routes.php
$router->get('/afiliados', 'AfiliadosController@index');
$router->post('/afiliados', 'AfiliadosController@store');
$router->get('/afiliados/{id}/edit', 'AfiliadosController@edit');
```

---

## 5. Anatomía de un Módulo

Cada módulo debe tener esta estructura:
```
app/modules/afiliados/
├── Controllers/
│   └── AfiliadosController.php
├── Models/
│   └── Afiliado.php
├── Views/
│   ├── list.php              # Lista/tabla
│   ├── form.php              # Formulario crear/editar
│   └── show.php              # Detalle individual
└── routes.php                # Definición de rutas
```

### Responsabilidades

- **routes.php:** Solo registra rutas, NO lógica
- **Controllers/:** Valida datos, llama Models, renderiza Views
- **Models/:** Acceso a base de datos con PDO (prepared statements)
- **Views/:** Solo HTML + PHP mínimo (echo, foreach, if)

### Reglas de las Vistas
```php
<?php
// Siempre usar plantilla base
$title = "Título de la página";
ob_start();
?>

<!-- HTML de tu módulo aquí -->

<?php
$content = ob_get_clean();
require __DIR__ . '/../../../../public/templates/base.html.php';
?>
```

---

## 6. Modelo de Trabajo con Branches

### 6.1 Estructura de Branches (FIJA)
```
main          ← Rama estable (solo código aprobado)
julian        ← Rama personal de Julián
joel          ← Rama personal de Joel
derlis        ← Rama personal de Derlis
jorge         ← Rama personal de Jorge
```

### 6.2 Reglas Absolutas

- Existen EXACTAMENTE 5 branches
- NO se crean branches `feature/*`, `hotfix/*`, `develop`
- Cada persona trabaja SOLO en su rama personal
- Integración a `main` mediante Pull Request
- Ramas personales son **permanentes** (no se borran)

### 6.3 Flujo Diario

#### Al Iniciar el Día
```bash
git checkout julian           # Tu rama personal
git pull origin main          # Traer cambios de main
```

#### Durante el Desarrollo
```bash
git add .
git commit -m "feat(afiliados): descripción"
git push origin julian
```

#### Al Terminar Historia de Usuario
```bash
git push origin julian
# Abrir Pull Request en GitHub: julian → main
```

#### Después de Merge a Main
```bash
# TODOS ejecutan esto
git checkout main
git pull origin main
git checkout julian           # Tu rama
git merge main
git push origin julian
```

---

## 7. Gestión de Historias de Usuario

### 7.1 Proceso por Historia

1. **Crear Issue** en GitHub con criterios de aceptación (Ya estan en proceso de crearse)
2. **Desarrollar** en tu rama personal
3. **Commits frecuentes** con mensajes descriptivos
4. **Probar localmente** todas las funcionalidades
5. **Documentar** pasos para probar y capturas
6. **Push** a tu rama personal
7. **Abrir Pull Request** a main

### 7.2 Plantilla de Issue
Ya los issues estan creados por el proyecto, cada uno dependiendo del modulo que tenga lo va cerrando.

```markdown
**Historia:** HU-XX - [Título]

**Como** [rol]  
**Quiero** [funcionalidad]  
**Para** [beneficio]

**Criterios de Aceptación:**
- [ ] Criterio 1
- [ ] Criterio 2

**Módulo:** [nombre]  
**Asignado a:** @usuario
```

### 7.3 Convención de Commits
```bash
feat(modulo): descripción corta
fix(modulo): corrección de bug
docs: actualización de documentación
refactor(modulo): mejora de código
```

---

## 8. Proceso de Integración a Main

### 8.1 Antes de Abrir Pull Request

**Verificar:**
- Código funciona sin errores
- Probado manualmente todos los casos
- Migraciones incluidas (si aplica)
- Sigue convenciones del proyecto
- No hay código de prueba (console.log, var_dump)
- Registra en bitácora (crear, editar, eliminar)

### 8.2 Estructura del Pull Request
```markdown
## Descripción
[Explicar qué se implementó]

## Issue Relacionado
Closes #XX

## Migraciones
- [ ] Este PR incluye migraciones
- Archivo: `app/migrations/XXX_nombre.sql`
- Comando: `mysql -u root -p sga_sebana < app/migrations/XXX_nombre.sql`

## Cómo Probar
1. Aplicar migraciones
2. Ir a [URL]
3. Probar [funcionalidad]

## Capturas de Pantalla
[Adjuntar imágenes]
```

### 8.3 Revisión (Code Review)

**Mínimo 1 persona debe revisar:**
- Funcionalidad correcta
- Usa plantilla base
- No hay lógica en Views
- Prepared statements en Models
- Validaciones server-side
- Protección CSRF
- Registra en bitácora

### 8.4 Después del Merge

**TODO el equipo sincroniza:**
```bash
git checkout main
git pull origin main
git checkout [tu-rama]
git merge main
git push origin [tu-rama]
```

---

## 9. Resolución de Conflictos

### 9.1 Prevención

- Sincronizar con main diariamente
- No modificar plantilla base sin coordinación
- Commits pequeños y frecuentes
- Comunicar cambios en archivos compartidos

### 9.2 Detectar Conflicto
```bash
git merge main
# CONFLICT (content): Merge conflict in archivo.php
```

### 9.3 Resolver Paso a Paso
```bash
# 1. Ver archivos en conflicto
git status

# 2. Abrir archivo y buscar:
<<<<<<< HEAD
Tu versión
=======
Versión de main
>>>>>>> main

# 3. Editar archivo, eliminar marcadores, decidir versión final

# 4. Probar que funciona

# 5. Marcar como resuelto
git add archivo.php
git commit -m "fix: resolver conflicto en archivo.php"
git push origin [tu-rama]
```

### 9.4 Pedir Ayuda

Si el conflicto es complejo:
- NO adivinar
- Tomar screenshot
- Contactar al equipo
- Opción: `git merge --abort` y coordinar

---

## Respuestas a Preguntas Clave

### ¿Mantener estructura del template?

**SÍ.** Usar una plantilla base en `public/templates/base.html.php` que todos los módulos reutilizan. **NO duplicar** por módulo.

### ¿Carpeta común o carpeta por módulo?

**Mixto:**
- Cada módulo tiene su carpeta: `app/modules/{modulo}/`
- Archivos comunes en: `app/shared/` y `public/templates/`

### ¿Solo UI o con backend real?

**Backend real desde el inicio:**
- Fase 1: Prototipos UI rápidos
- Fase 2 (prioritario): Implementar backend funcional
  - Autenticación
  - CRUD de afiliados
  - Bitácora

**Ventajas:** Detectar problemas reales, evitar retrabajo, pruebas end-to-end

---

## Estado Inicial del Proyecto

Para comenzar a trabajar, el repositorio DEBE contener:

### Archivos Obligatorios

- `public/index.php` (front controller básico)
- `public/.htaccess` (reescritura URLs)
- `public/templates/base.html.php` (plantilla base)
- `app/core/Router.php`
- `app/core/Database.php`
- `app/core/ControllerBase.php`
- `app/core/ModelBase.php`
- `app/migrations/001_create_users.sql`
- `app/migrations/002_create_roles.sql`
- Un módulo de ejemplo completo

### Branches Creados
```bash
main, julian, joel, derlis, jorge
```

---

**Fin del documento**
