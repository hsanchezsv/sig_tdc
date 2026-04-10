# SAD — Documento de Arquitectura del Sistema
## SIG-TDC · Sistema de Información Gerencial

**Versión:** 1.0 (generado por ingeniería inversa)
**Fecha:** 2026-03-12
**Estado:** Baseline

---

## 1. Visión General de la Arquitectura

SIG-TDC sigue una arquitectura **Monolítica Web Full-Stack** con renderizado del lado del servidor usando **Inertia.js** como puente entre el backend Laravel y el frontend Vue 3, eliminando la necesidad de una API REST separada.

### Estilo Arquitectónico

- **Patrón:** MVC (Model-View-Controller) del lado del servidor
- **Frontend:** SPA implícita (Single Page Application) con Inertia.js
- **Comunicación:** HTTP síncronas con responses Inertia (JSON o HTML según contexto)
- **Infraestructura:** Contenedores Docker con red bridge dedicada

---

## 2. Diagrama de Contexto del Sistema

```
┌─────────────────────────────────────────────────────┐
│                    USUARIO FINAL                    │
│                  (Navegador Web)                    │
└──────────────────────────┬──────────────────────────┘
                           │ HTTPS / HTTP
                           ▼
┌─────────────────────────────────────────────────────┐
│              CONTENEDOR: sig_tdc                    │
│         PHP 8.1 + Apache 2 + Laravel 9              │
│  ┌───────────────────────────────────────────────┐  │
│  │           Laravel Application                 │  │
│  │  Routes → Middleware → Controller → Model     │  │
│  │              ↕                                │  │
│  │         Inertia.js Adapter                    │  │
│  │              ↕                                │  │
│  │       Vue 3 + Tailwind (compilado)            │  │
│  └───────────────────────────────────────────────┘  │
│  Puerto expuesto: 9252:80                           │
└──────────────────────────┬──────────────────────────┘
                           │ TCP 3306 (red interna)
                           ▼
┌─────────────────────────────────────────────────────┐
│             CONTENEDOR: sig_tdc_db                  │
│                MySQL 8.0.35                         │
│             Base de datos: laradash                 │
│  Puerto expuesto: 34071:3306 (solo local)           │
└─────────────────────────────────────────────────────┘
```

---

## 3. Arquitectura de Capas

```
┌──────────────────────────────────────────────────────────┐
│  CAPA DE PRESENTACIÓN (Frontend)                         │
│  Vue 3 Components + Tailwind CSS + Inertia.js            │
│  resources/js/Pages/**/*.vue                             │
│  resources/js/Components/**/*.vue                        │
│  resources/js/Layouts/Laradash.vue                       │
├──────────────────────────────────────────────────────────┤
│  CAPA DE APLICACIÓN (Controladores + Middleware)         │
│  app/Http/Controllers/Laradash/*.php                     │
│  app/Http/Middleware/HandleInertiaRequests.php           │
│  app/Http/Requests/UserRequest.php                       │
├──────────────────────────────────────────────────────────┤
│  CAPA DE DOMINIO (Modelos + Policies)                    │
│  app/Models/User.php                                     │
│  app/Policies/UserPolicy.php                             │
│  Spatie Permission (Role, Permission)                    │
├──────────────────────────────────────────────────────────┤
│  CAPA DE INFRAESTRUCTURA (BD + Storage)                  │
│  database/migrations/*.php                               │
│  database/init_scripts/laradash_init.sql                 │
│  storage/ (archivos, fotos de perfil)                    │
│  MySQL 8.0.35 (tablas laradash + sig_*)                  │
└──────────────────────────────────────────────────────────┘
```

---

## 4. Stack Tecnológico

### Backend

| Tecnología | Versión | Rol |
|---|---|---|
| PHP | 8.1 | Lenguaje del servidor |
| Laravel | 9.x | Framework MVC |
| Laravel Jetstream | 2.3 | Auth + Profile + 2FA |
| Laravel Sanctum | 2.6 | Tokens API |
| Laravel Fortify | — | Auth actions (registro, login, 2FA) |
| Spatie Permission | 5.1 | Sistema RBAC |
| Inertia.js (Laravel) | 0.5.4 | Adaptador SPA sin API |
| Tightenco Ziggy | 1.0 | Named routes en JS |
| Apache 2 | — | Web server |

### Frontend

| Tecnología | Versión | Rol |
|---|---|---|
| Vue 3 | 3.2 | Framework reactivo |
| Inertia.js (Vue) | 0.5.1 | Navegación SPA |
| Tailwind CSS | 2.x (JIT) | Framework CSS utility-first |
| Laravel Mix | 6.x | Compilación de assets (Webpack) |
| Highcharts | 10.1 | Gráficos interactivos |
| highcharts-vue | 1.4 | Wrapper Vue para Highcharts |
| vue-gates | 2.1 | Directivas de permiso en Vue |
| html2pdf.js | 0.10.1 | Exportación PDF cliente |
| v-calendar | 3.0.0-alpha | Selector de fechas |
| @vuepic/vue-datepicker | 3.2 | Selector de fechas alternativo |
| axios | 0.21 | HTTP client |

### Base de Datos

| Tecnología | Versión | Rol |
|---|---|---|
| MySQL | 8.0.35 | Base de datos principal |
| Character set | utf8mb4 | Soporte Unicode completo |
| Collation | utf8mb4_unicode_ci | Orden insensible a mayúsculas |

### Infraestructura

| Tecnología | Versión | Rol |
|---|---|---|
| Docker | — | Contenedorización |
| Docker Compose | — | Orquestación multi-contenedor |
| php:8.1-apache | — | Imagen base |
| mysql:8.0.35 | — | Imagen base BD |
| Node.js | 18.x | Compilación de assets |
| Composer | latest | Gestor dependencias PHP |

---

## 5. Arquitectura de Contenedores Docker

### Red Docker

```
sig_tdc_network (bridge driver)
├── sig_tdc_db   (MySQL)
└── sig_tdc      (App PHP)
```

### Contenedor: `sig_tdc`

```yaml
Build:    Dockerfile_sig_tdc (php:8.1-apache base)
Puertos:  9252:80 (host:contenedor)
Volumes:
  - ./repos/sig_tdc       → /var/www/html        (código fuente)
  - ./config/sig_tdc      → /config              (configuración)
  - ./config/sig_tdc/entrypoint.sh → /entrypoint.sh
  - ./logs/sig_tdc        → /var/log/apache2     (logs)
Restart:  always
```

### Contenedor: `sig_tdc_db`

```yaml
Image:    mysql:8.0.35
Puertos:  34071:3306 (acceso externo desde host)
Volumes:
  - ./MysqlData/sig_tdc_db → /var/lib/mysql      (datos persistentes)
  - ./database/init_scripts/laradash_init.sql → init.d (inicialización)
Env:
  MYSQL_DATABASE:      laradash
  MYSQL_ROOT_PASSWORD: laradash_root
  TZ:                  America/El_Salvador
Restart:  always
```

### Proceso de Arranque (Entrypoint)

```bash
1. cp /config/.env /var/www/html/.env
2. composer install (solo si no existe vendor/)
3. php artisan key:generate --force
4. chmod -R 775 storage bootstrap/cache
5. npm install (solo si no existe node_modules/)
6. npm run production
7. apache2ctl -D FOREGROUND
```

---

## 6. Arquitectura de la Aplicación Laravel

### Estructura de Directorios Clave

```
repos/sig_tdc/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Laradash/          ← Controladores del negocio
│   │   │       ├── ClientesController.php
│   │   │       ├── InformesController.php
│   │   │       ├── PermisosController.php
│   │   │       ├── ProductosController.php
│   │   │       ├── ProveedoresController.php
│   │   │       ├── RolesController.php
│   │   │       ├── SucursalesController.php
│   │   │       └── UsuariosController.php
│   │   ├── Middleware/
│   │   │   └── HandleInertiaRequests.php  ← Props compartidas
│   │   └── Requests/
│   │       └── UserRequest.php            ← Validación usuarios
│   ├── Models/
│   │   └── User.php               ← Modelo principal con RBAC
│   ├── Policies/
│   │   └── UserPolicy.php         ← Autorización por permiso
│   └── Providers/
│       └── AuthServiceProvider.php ← Registro de policies + Gate::before
├── config/
│   ├── permission.php             ← Config Spatie
│   ├── jetstream.php              ← Stack Inertia + features
│   └── fortify.php                ← Auth actions
├── database/
│   ├── migrations/                ← 6 migraciones
│   ├── seeders/                   ← UserSeeder + DatabaseSeeder
│   └── init_scripts/
│       └── laradash_init.sql      ← Dump completo de inicialización
├── resources/
│   ├── js/
│   │   ├── Pages/                 ← Vistas Vue (Inertia)
│   │   │   ├── Informes/
│   │   │   ├── Otros/
│   │   │   │   └── Usuarios/
│   │   │   ├── Dashboard.vue
│   │   │   └── Welcome.vue
│   │   ├── Components/            ← Componentes reutilizables
│   │   ├── Jetstream/             ← Componentes de Jetstream
│   │   ├── Layouts/
│   │   │   └── Laradash.vue       ← Layout principal
│   │   ├── Plugins/
│   │   │   └── Permissions.js     ← Plugin vue-gates
│   │   └── app.js                 ← Entry point Vue
│   └── css/
│       └── app.css                ← Tailwind directives
├── routes/
│   ├── web.php                    ← Rutas principales
│   ├── api.php                    ← Ruta /api/user
│   └── laradash/
│       └── otros.php              ← Rutas del módulo
├── public/
│   ├── js/app.js                  ← Bundle compilado
│   └── css/app.css                ← Estilos compilados
├── storage/
│   └── app/public/
│       └── profile-photos/        ← Fotos de perfil
├── Dockerfile_sig_tdc             ← (raíz del entorno)
├── docker-compose.sig_tdc.yml     ← (raíz del entorno)
└── webpack.mix.js                 ← Compilación de assets
```

---

## 7. Flujo de una Petición HTTP

```
Navegador
    │
    │  GET /usuarios
    ▼
Apache (mod_rewrite)
    │  redirige todo a /public/index.php
    ▼
Laravel Bootstrap (index.php)
    │
    ▼
Kernel HTTP
    │  Middleware global: TrustProxies, CORS, ValidatePostSize
    │  Middleware web: EncryptCookies, StartSession, CSRF, SubstituteBindings
    │                  HandleInertiaRequests → comparte flash + auth
    ▼
Router (routes/web.php + routes/laradash/otros.php)
    │  Middleware: auth:sanctum, verified
    ▼
UsuariosController@index()
    │  $this->authorize('view', User::class)  → UserPolicy::view()
    │  User::filtro($buscar)->paginate(10)
    ▼
Inertia::render('Otros/Usuarios/Usuarios', [...datos...])
    │
    ▼
  Si primera visita (full page):   Si navegación SPA:
  Renderiza app.blade.php          Response JSON con datos
  con props JSON embebidas         Inertia actualiza el componente
    │                                      │
    └──────────────────┬───────────────────┘
                       ▼
              Vue 3 renderiza Usuarios.vue
              con los datos recibidos
```

---

## 8. Arquitectura de Seguridad

### Capas de Seguridad

```
┌─────────────────────────────────────────────────────────┐
│  1. Autenticación (Jetstream/Fortify)                   │
│     Email + Password + 2FA opcional                     │
├─────────────────────────────────────────────────────────┤
│  2. Sesión (Laravel Session + CSRF)                     │
│     Session driver: file, lifetime: 120min              │
│     CSRF token en todos los formularios                 │
├─────────────────────────────────────────────────────────┤
│  3. Middleware Auth                                     │
│     auth:sanctum → verifica sesión activa               │
│     verified → verifica email confirmado                │
├─────────────────────────────────────────────────────────┤
│  4. Authorization (Policies + Spatie Permission)        │
│     UserPolicy: view/create/update/delete               │
│     Gate::before() → Super Admin bypassa todo           │
├─────────────────────────────────────────────────────────┤
│  5. Validación (Form Requests)                          │
│     UserRequest: name, email, password, foto            │
├─────────────────────────────────────────────────────────┤
│  6. Frontend (vue-gates)                                │
│     Directivas v-role, v-permission en componentes Vue  │
│     (Seguridad visual - NO reemplaza al backend)        │
└─────────────────────────────────────────────────────────┘
```

### Roles y Permisos Iniciales

```
super admin
├── ver:role
├── crear:role
├── editar:role
├── eliminar:role
├── ver:permiso
├── ver:usuario
├── crear:usuario
├── editar:usuario
└── eliminar:usuario

invitado
└── (ningún permiso asignado por defecto)
```

**Nota:** El rol `super admin` recibe acceso total mediante `Gate::before()` en `AuthServiceProvider`, independientemente de los permisos asignados.

---

## 9. Modelo de Base de Datos

### Diagrama Entidad-Relación (Tablas Laravel)

```
users
├── id (PK)
├── name
├── email (UNIQUE)
├── email_verified_at
├── password
├── profile_photo_path
├── current_team_id
├── two_factor_secret
├── two_factor_recovery_codes
├── remember_token
├── created_at
└── updated_at
       │
       │ model_has_roles (polimórfico)
       ├──────────────────────────────→ roles
       │                                  ├── id (PK)
       │                                  ├── name
       │                                  └── guard_name
       │                                       │
       │                                       │ role_has_permissions
       │                                       └────────────────────→ permissions
       │                                                                ├── id (PK)
       │ model_has_permissions (polimórfico)                           ├── name
       └──────────────────────────────→ permissions                   └── guard_name

sessions
├── id (PK)
├── user_id (FK → users.id, nullable)
├── ip_address
├── user_agent
├── payload
└── last_activity

personal_access_tokens
├── id (PK)
├── tokenable_type + tokenable_id (morphs)
├── name
├── token (UNIQUE)
├── abilities
└── last_used_at
```

### Tablas de Negocio (Heredadas - Solo Lectura)

```
sig_clientes
├── id_cliente (PK)
├── nombre_cliente
├── id_pais (FK → sig_paises)
├── nit
├── direccion
├── contacto_nombre
├── telefono
└── fecha_ingreso

sig_proveedores
├── id_proveedor (PK)
├── nombre_proveedor
├── id_pais (FK → sig_paises)
├── direccion
├── telefono
└── nombre_contacto

sig_productos
├── id_producto (PK)
├── codigo_producto
├── nombre_producto
├── id_proveedor (FK → sig_proveedores)
├── precio_unidad
├── id_pais (FK → sig_paises)
├── fecha_compra
└── lote_numero

sig_sucursales
├── id_sucursal (PK)
├── codigo
├── nombre
└── id_pais (FK → sig_paises)

sig_vendedores
├── id_vendedor (PK)
├── codigo_vendedor
├── nombre_vendedor
├── fecha_ingreso
├── numero_documento
├── id_pais (FK → sig_paises)
└── id_sucursal (FK → sig_sucursales)

sig_ventas
├── id_venta (PK)
├── monto_facturado
├── id_vendedor (FK → sig_vendedores)
├── fecha (usada para filtrado por rango)
└── (otros campos implícitos)

sig_paises
├── id_pais (PK)
└── nombre
```

---

## 10. Arquitectura del Frontend

### Flujo de Renderizado con Inertia.js

```
Primera carga (full page):
  Laravel → Blade (app.blade.php) → HTML con JSON embebido
  → Vue monta la app → renderiza el Page component

Navegación subsecuente (SPA):
  Inertia intercepta el click/submit
  → XHR con header X-Inertia
  → Laravel responde JSON {component, props, url}
  → Vue intercambia el componente sin recargar la página
```

### Árbol de Componentes Principales

```
app.js (Entry Point)
└── Laradash.vue (Layout)
    ├── Header.vue
    │   └── DarkMode.vue
    ├── SidebarDesktop.vue  (pantallas grandes)
    ├── SidebarNormal.vue   (sidebar colapsado)
    ├── SidebarMobil.vue    (pantallas pequeñas)
    └── [Page Component]   (slot principal)
        ├── Dashboard.vue
        ├── Otros/Clientes.vue
        ├── Otros/Proveedores.vue
        ├── Otros/Productos.vue
        ├── Otros/Sucursales.vue
        │   └── SucursalesChart.vue
        ├── Otros/Permisos.vue
        ├── Otros/Roles.vue
        ├── Otros/InformesGerenciales.vue
        ├── Otros/Usuarios/Usuarios.vue
        │   └── Table.vue + Pagination.vue
        ├── Otros/Usuarios/VerUsuario.vue
        ├── Informes/InformeVentas.vue
        │   └── [Highcharts component]
        └── Informes/InformeSucursal.vue
```

### Sistema de Permisos en Frontend

```javascript
// app.js
app.use(VueGates)

// Permissions.js plugin
app.config.globalProperties.$gates.setRoles(auth.roles)
app.config.globalProperties.$gates.setPermissions(auth.permisos)

// En componentes Vue:
<div v-if="$gates.hasPermission('crear:usuario')">
  <button>Nuevo Usuario</button>
</div>
```

Los roles y permisos llegan al frontend a través de `HandleInertiaRequests::share()`:

```php
'auth' => [
    'roles'    => $user->getRoleNames(),
    'permisos' => $user->getAllPermissions()->pluck('name')
]
```

---

## 11. Compilación de Assets

```
webpack.mix.js
    │
    ├── resources/js/app.js     → public/js/app.js    (minificado en prod)
    └── resources/css/app.css   → public/css/app.css  (purged + minificado en prod)

Tailwind JIT: solo genera clases CSS que realmente se usan en los .vue/.blade.php
PostCSS: postcss-import + tailwindcss
```

---

## 12. Decisiones Arquitectónicas Clave

| Decisión | Opción Elegida | Alternativa Descartada | Razón |
|---|---|---|---|
| Frontend | Vue 3 + Inertia.js | Vue + REST API separada | Reduce complejidad: un solo proyecto, sin CORS, sin estado duplicado |
| Auth | Laravel Jetstream | Auth manual / Passport | Jetstream provee 2FA, profile photos, account deletion out-of-the-box |
| RBAC | Spatie Permission | Gates manuales | Librería madura, integración con modelos Eloquent, UI-friendly |
| Assets | Laravel Mix (Webpack) | Vite | Compatibilidad con laravel-mix v6 ya configurado en el proyecto |
| PDF Export | html2pdf.js (cliente) | wkhtmltopdf (servidor) | Sin dependencias de servidor adicionales |
| Gráficos | Highcharts | Chart.js / ApexCharts | Mayor variedad de tipos de gráfico para informes gerenciales |
