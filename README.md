# SIG_TDC — Sistema de Información Gerencial

Sistema administrativo web para consulta de datos de ventas, clientes, proveedores, productos y sucursales, con módulo de gestión de usuarios, roles y permisos.

## Stack tecnológico

| Capa | Tecnología |
|---|---|
| Framework PHP | Laravel 9 |
| Autenticación | Laravel Jetstream 2 + Fortify + Sanctum |
| Autorización | Spatie Laravel Permission 5 |
| Frontend SPA | Inertia.js 0.5 + Vue 3.2 |
| Estilos | Tailwind CSS 2 |
| Gráficos | Highcharts 10 |
| Exportación PDF | html2pdf.js |
| Base de datos | MySQL 8.0.35 |
| Contenedores | Docker (PHP 8.1 + Apache) |

## Módulos implementados

### Autenticación y perfil
- Login por correo electrónico con bcrypt
- Autenticación de dos factores (2FA via TOTP)
- Gestión de sesiones activas del navegador
- Foto de perfil (base64, PNG/JPG)
- Cambio de contraseña y correo
- Tokens de API via Sanctum

### Gestión de usuarios
- Listado con búsqueda y filtros
- Creación, edición, eliminación de usuarios
- Asignación y revocación de roles por usuario
- Asignación y revocación de permisos directos por usuario
- Vista de detalle con roles y permisos activos

### Roles y permisos
- CRUD completo de roles
- Asignación de permisos a roles
- Vista de listado de permisos (solo lectura)
- Control de acceso en frontend via Vue Gates

### Consultas de datos de negocio (solo lectura)
- **Clientes** — tabla `sig_clientes`
- **Proveedores** — tabla `sig_proveedores`
- **Productos** — tabla `sig_productos`
- **Sucursales y vendedores** — tablas `sig_sucursales` / `sig_vendedores`

### Informes gerenciales
- Informe de ventas por vendedor (con gráfico de área Highcharts — tendencia 6 meses)
- Informe de ventas por sucursal
- Filtrado por rango de fechas
- Exportación a PDF desde el cliente (html2pdf.js)

### UI
- Modo claro / oscuro
- Sidebar responsive (desktop y mobile)
- Mensajes flash y validación de formularios
- Paginación

## Arquitectura

```
Navegador (Vue 3 + Inertia.js)
        │  Peticiones HTTP (no API JSON — Inertia)
        ▼
Laravel 9 (MVC)
  ├── routes/web.php                    ← rutas públicas y auth
  ├── routes/laradash/otros.php         ← rutas del sistema (auth requerida)
  ├── app/Http/Controllers/Laradash/    ← 8 controladores de negocio
  ├── app/Models/User.php               ← modelo con Spatie HasRoles
  └── app/Policies/UserPolicy.php
        │
        ▼
MySQL 8.0.35 (base: laradash)
  ├── Tablas propias: users, roles, permissions, sessions, personal_access_tokens, ...
  └── Tablas de negocio (externas): sig_clientes, sig_ventas, sig_sucursales, ...
```

## Controladores

| Controlador | Función |
|---|---|
| `UsuariosController` | CRUD de usuarios, roles, permisos, foto de perfil |
| `RolesController` | CRUD de roles con asignación de permisos |
| `PermisosController` | Listado de permisos (solo lectura) |
| `InformesController` | Informes de ventas con datos para Highcharts |
| `ClientesController` | Consulta de clientes |
| `ProveedoresController` | Consulta de proveedores |
| `ProductosController` | Consulta de productos |
| `SucursalesController` | Consulta de sucursales y vendedores |

## Estructura de directorios

```
app/
├── Actions/Fortify/          ← acciones de autenticación
├── Http/Controllers/Laradash/ ← controladores de negocio
├── Http/Requests/UserRequest.php
├── Models/User.php
└── Policies/UserPolicy.php

resources/js/
├── Pages/                    ← páginas Inertia (Vue SPA)
│   ├── Auth/                 ← login, registro, 2FA, etc.
│   ├── Profile/              ← perfil de usuario
│   ├── Otros/                ← admin: usuarios, roles, clientes, etc.
│   └── Informes/             ← vistas de reportes
├── Components/               ← Header, Sidebar, DarkMode, Pagination, Table
├── Layouts/Laradash.vue      ← layout principal
└── Plugins/Permissions.js    ← plugin Vue para control de acceso

routes/
├── web.php
└── laradash/otros.php

database/migrations/          ← 7 migraciones Laravel/Spatie
docs/                         ← requisitos, arquitectura y diseño del sistema
```

## Configuración del entorno

Copiar `.env.example` a `.env` y ajustar:

```env
APP_NAME=SIG_TDC
APP_URL=http://localhost:9252

DB_HOST=sig_tdc_db
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=
```

## Instalación (Docker)

```bash
# Levantar contenedores
docker-compose up

# Instalar dependencias PHP
docker exec -it sig_tdc bash
composer install

# Ejecutar migraciones y seeders
php artisan migrate --seed

# Compilar assets frontend
npm install
npm run dev       # desarrollo con hot reload
npm run build     # producción
```

## Comandos útiles

```bash
# Ejecutar tests
php artisan test

# Limpiar caché de permisos (Spatie)
php artisan permission:cache-reset

# Limpiar caché de configuración
php artisan config:clear && php artisan cache:clear

# Generar clave de aplicación
php artisan key:generate
```

## Documentación interna

| Documento | Ubicación |
|---|---|
| Requisitos del Sistema | `docs/01_Requisitos_del_Sistema.md` |
| Arquitectura del Sistema | `docs/02_Arquitectura_del_Sistema.md` |
| Diseño del Sistema | `docs/03_Diseno_del_Sistema.md` |
| Manual de Desarrollador | `docs/Manual de Desarrollador.docx` |
| Manual de Usuario Administrador | `docs/Manual de usuario Administrador.docx` |
| Manual de Usuario Final | `docs/Manual de usuario.docx` |

## Seguridad

- Contraseñas: hashing bcrypt
- CSRF: tokens en todos los formularios (Blade/Inertia)
- Roles y permisos: Spatie Permission + Gates + Policies
- 2FA disponible para todos los usuarios
- API tokens via Laravel Sanctum

## Licencia

Basado en [LaraDash](https://github.com/GenaroHV/LaraDash) de Genaro Hernández (MIT).
