# SRS — Especificación de Requisitos del Sistema
## SIG-TDC · Sistema de Información Gerencial

**Versión:** 1.0 (generado por ingeniería inversa)
**Fecha:** 2026-03-12
**Estado:** Baseline

---

## 1. Introducción

### 1.1 Propósito

Este documento describe los requisitos funcionales y no funcionales del **SIG-TDC** (Sistema de Información Gerencial – TDC), un dashboard administrativo web diseñado para gestionar operaciones comerciales internas: ventas, clientes, proveedores, productos, sucursales y reportes gerenciales.

### 1.2 Alcance

El sistema SIG-TDC cubre los siguientes módulos:

- Autenticación y control de acceso basado en roles (RBAC)
- Gestión de usuarios, roles y permisos
- Consulta de clientes, proveedores, productos y sucursales
- Generación de informes gerenciales con gráficos y exportación PDF
- Administración del perfil de usuario

### 1.3 Definiciones y Acrónimos

| Término | Definición |
|---|---|
| SIG-TDC | Sistema de Información Gerencial - TDC |
| RBAC | Role-Based Access Control (Control de Acceso Basado en Roles) |
| Inertia.js | Adaptador que conecta Laravel con Vue 3 sin API REST separada |
| Spatie Permission | Librería PHP para gestión de roles y permisos en Laravel |
| JWT / Sanctum | Sistema de tokens API para autenticación |

### 1.4 Referencias

- Laravel 9.x Documentation
- Spatie Laravel Permission v5
- Laravel Jetstream v2
- Inertia.js v0.10

---

## 2. Descripción General del Sistema

### 2.1 Contexto del Sistema

SIG-TDC es una aplicación web de tipo **SPA (Single Page Application)** basada en el stack **Laravel 9 + Inertia.js + Vue 3**. Se despliega en un contenedor Docker y accede a una base de datos MySQL compartida que contiene tanto las tablas del framework (autenticación, permisos) como tablas de negocio heredadas (`sig_*`).

```
[Navegador] ──HTTP─→ [Apache + PHP Laravel] ──MySQL─→ [Base de Datos laradash]
                             │
                        [Inertia.js]
                             │
                        [Vue 3 SPA]
```

### 2.2 Usuarios del Sistema

| Rol | Descripción |
|---|---|
| **Super Admin** | Acceso total al sistema. Puede gestionar usuarios, roles, permisos y ver todos los módulos. |
| **Invitado** | Acceso de solo lectura a módulos autorizados. |
| *(Roles adicionales)* | Configurables dinámicamente mediante el módulo de roles. |

### 2.3 Supuestos y Dependencias

- La base de datos contiene tablas `sig_*` pre-existentes con datos de negocio.
- El servidor dispone de Docker y Docker Compose.
- Los clientes acceden desde navegadores modernos (Chrome, Firefox, Edge).

---

## 3. Requisitos Funcionales

### RF-01: Autenticación de Usuarios

**Prioridad:** Alta
**Descripción:** El sistema debe permitir a los usuarios iniciar y cerrar sesión mediante correo electrónico y contraseña.

**Criterios de aceptación:**
- El usuario ingresa email y contraseña válidos y accede al dashboard.
- Credenciales inválidas muestran mensaje de error sin revelar cuál campo es incorrecto.
- La sesión persiste usando cookies seguras (session driver: file).
- El sistema soporta verificación de correo electrónico (email_verified_at).
- Soporte opcional para autenticación de dos factores (2FA) mediante TOTP.

---

### RF-02: Gestión de Usuarios

**Prioridad:** Alta
**Actor:** Super Admin, usuarios con permisos de gestión

**RF-02.1 Listar Usuarios**
- Muestra lista paginada de usuarios (10 por página).
- Permite buscar por nombre o correo electrónico.
- Requiere permiso `ver:usuario`.

**RF-02.2 Crear Usuario**
- Formulario con campos: nombre, correo, contraseña.
- Validaciones: nombre obligatorio, correo válido y único, contraseña mínimo 8 caracteres.
- Requiere permiso `crear:usuario`.

**RF-02.3 Ver Perfil de Usuario**
- Muestra datos del usuario: nombre, email, foto de perfil, roles asignados, permisos directos.
- Requiere permiso `ver:usuario`.

**RF-02.4 Actualizar Perfil**
- Permite editar nombre y correo.
- Permite cargar o eliminar foto de perfil (formatos PNG, JPG).
- La foto se almacena en `storage/profile-photos/` con nombre aleatorio.
- Requiere permiso `editar:usuario`.

**RF-02.5 Actualizar Contraseña**
- Permite cambiar contraseña de cualquier usuario.
- Valida que la nueva contraseña y confirmación coincidan.
- Requiere permiso `editar:usuario`.

**RF-02.6 Asignar Roles a Usuario**
- Permite asignar o quitar roles a un usuario.
- La asignación reemplaza todos los roles actuales.
- Requiere permiso `editar:usuario`.

**RF-02.7 Asignar Permisos Directos**
- Permite asignar permisos individuales directamente a un usuario (sin necesidad de rol).
- Requiere permiso `editar:usuario`.

**RF-02.8 Eliminar Usuario**
- Elimina el usuario del sistema de forma permanente.
- Requiere permiso `eliminar:usuario`.

---

### RF-03: Gestión de Roles

**Prioridad:** Alta
**Actor:** Super Admin, usuarios con permisos de roles

**RF-03.1 Listar Roles**
- Muestra todos los roles con sus permisos asociados.
- Requiere permiso `ver:role`.

**RF-03.2 Crear Rol**
- Permite crear un nuevo rol con nombre único.
- Permite seleccionar permisos a asignar al momento de la creación.
- Requiere permiso `crear:role`.

**RF-03.3 Editar Rol**
- Permite cambiar el nombre del rol.
- Permite reasignar permisos (reemplaza todos los permisos actuales).
- Requiere permiso `editar:role`.

**RF-03.4 Eliminar Rol**
- Elimina el rol del sistema.
- Requiere permiso `eliminar:role`.

---

### RF-04: Gestión de Permisos

**Prioridad:** Media
**Actor:** Super Admin, usuarios con permiso `ver:permiso`

**RF-04.1 Listar Permisos**
- Muestra todos los permisos del sistema con su ID, nombre y guardian.
- Solo lectura (no se crean ni eliminan desde la interfaz).

**Permisos del sistema:**

| Permiso | Descripción |
|---|---|
| `ver:role` | Ver listado de roles |
| `crear:role` | Crear roles |
| `editar:role` | Editar roles |
| `eliminar:role` | Eliminar roles |
| `ver:permiso` | Ver listado de permisos |
| `ver:usuario` | Ver usuarios y perfiles |
| `crear:usuario` | Crear nuevos usuarios |
| `editar:usuario` | Editar usuarios y sus roles/permisos |
| `eliminar:usuario` | Eliminar usuarios |

---

### RF-05: Consulta de Clientes

**Prioridad:** Media
**Actor:** Usuarios autenticados

- Muestra listado de clientes de la tabla `sig_clientes`.
- Columnas: ID, Nombre, País, NIT, Dirección, Nombre Contacto, Teléfono, Fecha de Ingreso.
- Solo lectura (sin CRUD).

---

### RF-06: Consulta de Proveedores

**Prioridad:** Media
**Actor:** Usuarios autenticados

- Muestra listado de proveedores de la tabla `sig_proveedores`.
- Columnas: ID, Nombre, País, Dirección, Teléfono, Nombre Contacto.
- Solo lectura.

---

### RF-07: Consulta de Productos

**Prioridad:** Media
**Actor:** Usuarios autenticados

- Muestra listado de productos de la tabla `sig_productos`.
- Columnas: ID, Código, Nombre, Proveedor, Precio Unitario, País, Fecha de Compra, Número de Lote.
- Solo lectura.

---

### RF-08: Consulta de Sucursales y Vendedores

**Prioridad:** Media
**Actor:** Usuarios autenticados

- Muestra listado de sucursales con su país asociado.
- Muestra listado de vendedores con: código, nombre, fecha de ingreso, documento, país y sucursal.
- Solo lectura.

---

### RF-09: Informes Gerenciales

**Prioridad:** Alta
**Actor:** Gerentes, Super Admin

**RF-09.1 Selección de Informe**
- El usuario selecciona tipo de reporte: Informe de Ventas (por vendedor) o Informe por Sucursal.
- El usuario selecciona rango de fechas (fecha inicio – fecha fin).

**RF-09.2 Informe de Ventas por Vendedor**
- Muestra: total facturado en período, promedio de ventas, total de transacciones.
- Tabla de vendedores con: nombre, sucursal, país, monto facturado, número de ventas.
- Gráfico de área (Highcharts) con ventas de los últimos 6 meses.

**RF-09.3 Informe por Sucursal**
- Muestra: ventas agrupadas por sucursal.
- Incluye monto total, conteo de ventas por sucursal.

**RF-09.4 Exportación a PDF**
- El usuario puede exportar el informe visualizado a PDF.
- La exportación se realiza del lado del cliente con `html2pdf.js`.

---

### RF-10: Perfil de Usuario Propio

**Prioridad:** Media
**Actor:** Cualquier usuario autenticado

- El usuario autenticado puede ver y editar su propio perfil.
- Incluye: nombre, correo, foto de perfil, cambio de contraseña.

---

## 4. Requisitos No Funcionales

### RNF-01: Seguridad

- Todas las contraseñas se almacenan con `bcrypt` (nunca en texto plano).
- El sistema utiliza CSRF tokens en todos los formularios.
- Las sesiones expiran tras 120 minutos de inactividad.
- Los tokens de API se gestionan con Laravel Sanctum.
- El sistema implementa política de roles: el Super Admin tiene acceso a todo mediante `Gate::before()`.
- Las rutas protegidas requieren middleware `auth:sanctum` y `verified`.

### RNF-02: Rendimiento

- El listado de usuarios debe responder en menos de 2 segundos para hasta 10,000 registros.
- La paginación limita los resultados a 10 registros por consulta.
- Los assets (JS/CSS) se compilan y minimizan para producción con Laravel Mix.
- Tailwind CSS usa modo JIT para generar solo el CSS utilizado.

### RNF-03: Usabilidad

- La interfaz es responsive: adaptada para desktop, tablet y móvil.
- Soporta modo oscuro (dark mode) mediante clase CSS en el `<html>`.
- Mensajes flash de éxito y error se muestran tras cada operación.
- Los formularios validan en el servidor con mensajes de error descriptivos en español.

### RNF-04: Mantenibilidad

- Código organizado siguiendo las convenciones de Laravel (MVC + Form Requests + Policies).
- Frontend modularizado en componentes Vue reutilizables.
- Configuración separada del código fuente mediante archivos `.env`.

### RNF-05: Compatibilidad

- Navegadores modernos: Chrome 90+, Firefox 88+, Edge 90+, Safari 14+.
- PHP 8.0 o superior.
- Node.js 18.x para compilación de assets.
- MySQL 8.0.35.

### RNF-06: Disponibilidad

- El sistema se despliega en Docker con `restart: always`, garantizando reinicio automático ante fallos.
- Dependencia de `sig_tdc_db` declarada explícitamente (`depends_on`).

---

## 5. Restricciones del Sistema

- El sistema no gestiona (crea/edita/elimina) las tablas `sig_*` de negocio; solo las consulta.
- El módulo de permisos es de solo lectura; los permisos se crean únicamente mediante seeders.
- La exportación PDF se realiza en el cliente (navegador), no en el servidor.
- El sistema no cuenta con envío real de correos en el ambiente de desarrollo (usa Mailhog).

---

## 6. Casos de Uso Principales

### CU-01: Iniciar Sesión

```
Actor: Usuario no autenticado
Precondición: El usuario tiene una cuenta registrada
Flujo principal:
  1. Usuario accede a la URL raíz (/)
  2. Sistema muestra formulario de login
  3. Usuario ingresa email y contraseña
  4. Sistema valida credenciales
  5. Sistema redirige al Dashboard
Flujo alternativo:
  4a. Credenciales inválidas → muestra error, permanece en login
```

### CU-02: Crear Usuario

```
Actor: Super Admin o usuario con permiso crear:usuario
Precondición: Usuario autenticado con el permiso requerido
Flujo principal:
  1. Accede a /usuarios
  2. Hace clic en "Nuevo Usuario"
  3. Completa el formulario (nombre, email, contraseña)
  4. Sistema valida datos (nombre requerido, email único válido, contraseña ≥8)
  5. Sistema crea el usuario
  6. Sistema redirige con mensaje de éxito
Flujo alternativo:
  4a. Validación falla → muestra errores en el formulario
```

### CU-03: Generar Informe de Ventas

```
Actor: Super Admin, Gerente
Precondición: Usuario autenticado
Flujo principal:
  1. Accede a /informes
  2. Selecciona tipo: "Informe de Ventas"
  3. Selecciona fecha inicio y fecha fin
  4. Hace clic en "Generar"
  5. Sistema consulta sig_ventas, sig_vendedores, sig_sucursales, sig_paises
  6. Sistema renderiza tabla de vendedores y gráfico de área
  7. Usuario puede exportar a PDF
```

### CU-04: Asignar Rol a Usuario

```
Actor: Super Admin o usuario con permiso editar:usuario
Precondición: El usuario objetivo existe
Flujo principal:
  1. Accede al perfil del usuario (/usuario/perfil/{id})
  2. En la sección de roles, marca/desmarca roles
  3. Hace clic en "Actualizar Roles"
  4. Sistema hace detach de todos los roles actuales
  5. Sistema asigna los roles seleccionados
  6. Redirige con mensaje de éxito
```
