# SDD — Documento de Diseño del Sistema
## SIG-TDC · Sistema de Información Gerencial

**Versión:** 1.0 (generado por ingeniería inversa)
**Fecha:** 2026-03-12
**Estado:** Baseline

---

## 1. Introducción

Este documento describe el diseño detallado de los módulos del SIG-TDC, incluyendo la estructura de clases, flujos de datos, contratos de interfaz entre capas y decisiones de diseño a nivel de código.

---

## 2. Módulos del Sistema

| # | Módulo | Ruta | Controlador |
|---|---|---|---|
| 1 | Autenticación | `/login`, `/register` | Jetstream/Fortify (interno) |
| 2 | Dashboard | `/dashboard` | (inline en web.php) |
| 3 | Usuarios | `/usuarios`, `/usuario/perfil/{id}` | UsuariosController |
| 4 | Roles | `/roles` | RolesController |
| 5 | Permisos | `/permisos` | PermisosController |
| 6 | Clientes | `/clientes` | ClientesController |
| 7 | Proveedores | `/proveedores` | ProveedoresController |
| 8 | Productos | `/productos` | ProductosController |
| 9 | Sucursales | `/sucursales` | SucursalesController |
| 10 | Informes | `/informes` | InformesController |

---

## 3. Diseño Detallado por Módulo

### 3.1 Módulo de Autenticación

**Responsabilidad:** Gestionar el ciclo de vida de la sesión del usuario.

**Componentes involucrados:**
- `app/Http/Middleware/Authenticate.php` — Redirige a login si no autenticado
- `app/Providers/RouteServiceProvider.php` — Define `HOME = '/dashboard'`
- `config/fortify.php` — Configura acciones de autenticación
- `config/jetstream.php` — Configura stack Inertia y features

**Features habilitadas:**
```php
// config/jetstream.php
Features::profilePhotos()       // Subida de foto de perfil
Features::accountDeletion()     // Eliminación de cuenta propia
```

**Features de Fortify habilitadas:**
```php
Features::registration()               // Registro de nuevos usuarios
Features::resetPasswords()             // Recuperación de contraseña
Features::updateProfileInformation()   // Actualización de perfil
Features::updatePasswords()            // Cambio de contraseña
```

**Flujo de login:**
```
POST /login
  → Fortify LoginAction
  → Valida email + password contra tabla users
  → Regenera session ID
  → Redirige a /dashboard
```

---

### 3.2 Módulo de Usuarios

#### Controlador: `UsuariosController`

**Namespace:** `App\Http\Controllers\Laradash`

**Dependencias:**
- `App\Models\User`
- `App\Http\Requests\UserRequest`
- `Illuminate\Support\Facades\Auth`
- `Illuminate\Support\Facades\Storage`
- `Inertia\Inertia`

**Tabla de métodos:**

| Método HTTP | URI | Método PHP | Descripción |
|---|---|---|---|
| GET | /usuarios | `index()` | Listar usuarios paginados |
| POST | /usuarios | `store()` | Crear nuevo usuario |
| DELETE | /usuarios/{id} | `destroy()` | Eliminar usuario |
| GET | /usuario/perfil/{id} | `miPerfil()` | Ver perfil de usuario |
| POST | /usuario/perfil | `actualizarPerfil()` | Actualizar nombre, email, foto |
| POST | /usuario/perfil/foto | `eliminarFoto()` | Eliminar foto de perfil |
| POST | /usuario/perfil/roles | `actualizarRoles()` | Actualizar roles del usuario |
| POST | /usuario/perfil/permisos | `actualizarPermisos()` | Actualizar permisos directos |
| POST | /usuario/perfil/password | `actualizarPassword()` | Cambiar contraseña |

**Diseño del método `index()`:**

```php
public function index(Request $request)
{
    $this->authorize('view', User::class);

    $usuarios  = $this->realizarBusqueda($request->buscar);
    $usuarioPrincipal = Auth::user()->load(['roles', 'permissions']);

    return Inertia::render('Otros/Usuarios/Usuarios', [
        'usuarios' => $usuarios,
        'filtro'   => $request->buscar
    ]);
}

private function realizarBusqueda($key)
{
    if ($key) {
        return User::filtro($key)->paginate(10);
    }
    return User::paginate(10);
}
```

**Diseño del método `actualizarPerfil()`:**

```php
// Lógica de foto de perfil:
if ($request->profile_photo_path) {
    // 1. Eliminar foto anterior si existe
    if ($user->profile_photo_path) {
        Storage::disk('public')->delete($user->profile_photo_path);
    }
    // 2. Decodificar base64
    $image = $request->profile_photo_path;
    // 3. Generar nombre aleatorio
    $imageName = Str::random(40) . '.jpg';
    // 4. Guardar en storage/app/public/profile-photos/
    Storage::disk('public')->put('profile-photos/' . $imageName, base64_decode($image));
    $user->profile_photo_path = 'profile-photos/' . $imageName;
}
```

**Diseño del método `actualizarRoles()`:**

```php
public function actualizarRoles(Request $request)
{
    $usuario = User::find($request->usuario);

    // 1. Quitar todos los roles actuales
    $usuario->roles()->detach();

    // 2. Asignar roles seleccionados
    foreach ($request->roles as $rol) {
        if ($rol['checked']) {
            $usuario->assignRole($rol['name']);
        }
    }

    return redirect()->back()->with('success', 'Roles actualizados.');
}
```

---

#### Form Request: `UserRequest`

```php
namespace App\Http\Requests;

class UserRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'name'               => 'required',
            'email'              => 'required|email:rfc,dns|unique:users',
            'profile_photo_path' => 'mimes:png,jpg',
            'password'           => 'required|min:8',
        ];
    }

    public function attributes()
    {
        return [
            'name'               => 'nombres',
            'email'              => 'correo',
            'password'           => 'contraseña',
            'profile_photo_path' => 'fotografía',
        ];
    }
}
```

**Nota de diseño:** La validación `unique:users` en email puede causar problemas en la edición de perfil (el email actual ya existe). Este es un issue conocido que requiere usar la regla `Rule::unique('users')->ignore($id)`.

---

#### Policy: `UserPolicy`

```php
namespace App\Policies;

class UserPolicy
{
    public function view(User $user):   bool { return $user->hasPermissionTo('ver:usuario'); }
    public function create(User $user): bool { return $user->hasPermissionTo('crear:usuario'); }
    public function update(User $user): bool { return $user->hasPermissionTo('editar:usuario'); }
    public function delete(User $user): bool { return $user->hasPermissionTo('eliminar:usuario'); }
}
```

**Bypass de Super Admin:**
```php
// AuthServiceProvider.php
Gate::before(function ($user, $ability) {
    if ($user->hasRole('super admin')) {
        return true;  // Acceso total, omite todas las policies
    }
});
```

---

#### Modelo: `User`

```php
namespace App\Models;

class User extends Authenticatable
{
    use HasApiTokens;       // Sanctum: tokens API
    use HasFactory;         // Seeder/testing
    use HasProfilePhoto;    // Jetstream: gestión de foto
    use Notifiable;         // Emails, notificaciones
    use TwoFactorAuthenticatable; // Jetstream: 2FA
    use HasRoles;           // Spatie: roles y permisos

    protected $fillable = ['name', 'email', 'password'];

    protected $hidden = [
        'password', 'remember_token',
        'two_factor_recovery_codes', 'two_factor_secret'
    ];

    protected $casts = ['email_verified_at' => 'datetime'];

    protected $appends = ['profile_photo_url'];

    // Eager loading automático
    protected $with = ['roles', 'permissions'];

    // Scope de búsqueda múltiple
    public function scopeFiltros($query, array $filtros)
    {
        // Filtra por name y/o email desde array de filtros
    }

    // Scope de búsqueda simple (usado en producción)
    public function scopeFiltro($query, $key)
    {
        return $query->where('name', 'LIKE', "%{$key}%")
                     ->orWhere('email', 'LIKE', "%{$key}%");
    }
}
```

---

### 3.3 Módulo de Roles

#### Controlador: `RolesController`

**Dependencias:**
- `Spatie\Permission\Models\Role`
- `Spatie\Permission\Models\Permission`
- `Inertia\Inertia`

**Tabla de métodos:**

| Método HTTP | URI | Método PHP | Descripción |
|---|---|---|---|
| GET | /roles | `index()` | Listar roles con permisos |
| POST | /roles | `store()` | Crear rol |
| PUT | /roles/{id} | `update()` | Editar rol y sus permisos |
| DELETE | /roles/{id} | `destroy()` | Eliminar rol |

**Diseño del método `update()`:**

```php
public function update(Request $request, Role $rol)
{
    // 1. Extraer datos del request
    $id     = $request->params['id'];
    $name   = $request->params['name'];
    $permisos = $request->permisos; // array [{name, checked}]

    // 2. Buscar y actualizar rol
    $rol = Role::find($id);
    $rol->name = $name;
    $rol->save();

    // 3. Reasignar permisos (limpiar y reasignar)
    $rol->permissions()->detach();
    foreach ($permisos as $permiso) {
        if ($permiso['checked']) {
            $rol->givePermissionTo($permiso['name']);
        }
    }

    return redirect()->back()->with('success', 'Rol actualizado.');
}
```

---

### 3.4 Módulo de Informes

#### Controlador: `InformesController`

Este es el módulo más complejo del sistema. Genera reportes gerenciales con datos agregados de ventas.

**Diseño del método `view_informe()`:**

```php
public function view_informe(Request $request)
{
    $fechaInicio = $request->fecha_inicio;
    $fechaFin    = $request->fecha_fin;
    $tipoReporte = $request->tipo_reporte; // 1=Vendedor, 2=Sucursal

    // Query 1: Ventas por vendedor en el período
    $vendedores = DB::select("
        SELECT
            v.id_vendedor,
            v.nombre_vendedor,
            s.nombre AS sucursal,
            p.nombre AS pais,
            SUM(f.monto_facturado) AS total_facturado,
            COUNT(f.id_venta)      AS total_ventas
        FROM sig_ventas f
        INNER JOIN sig_vendedores v ON f.id_vendedor = v.id_vendedor
        INNER JOIN sig_sucursales s ON v.id_sucursal = s.id_sucursal
        INNER JOIN sig_paises     p ON v.id_pais     = p.id_pais
        WHERE f.fecha BETWEEN ? AND ?
        GROUP BY v.id_vendedor
        ORDER BY total_facturado DESC
    ", [$fechaInicio, $fechaFin]);

    // Query 2: Totales del período
    $totales = DB::select("
        SELECT
            SUM(monto_facturado) AS total,
            COUNT(id_venta)      AS transacciones,
            AVG(monto_facturado) AS promedio
        FROM sig_ventas
        WHERE fecha BETWEEN ? AND ?
    ", [$fechaInicio, $fechaFin]);

    // Query 3: Meses disponibles (para selector)
    $meses = DB::select("
        SELECT DISTINCT MONTHNAME(fecha) AS mes, MONTH(fecha) AS num_mes
        FROM sig_ventas
        ORDER BY num_mes DESC
        LIMIT 5
    ");

    // Query 4: Últimos 6 meses (para gráfico)
    $ultimos6Meses = DB::select("
        SELECT
            MONTHNAME(fecha)     AS mes,
            MONTH(fecha)         AS num_mes,
            SUM(monto_facturado) AS total
        FROM sig_ventas
        GROUP BY MONTH(fecha)
        ORDER BY num_mes DESC
        LIMIT 6
    ");

    // Switch según tipo de reporte
    switch ($tipoReporte) {
        case 1:
            return Inertia::render('Informes/InformeVentas', [
                'vendedores'  => $vendedores,
                'totales'     => $totales,
                'meses'       => $meses,
                'grafico'     => $ultimos6Meses,
                'fechaInicio' => $fechaInicio,
                'fechaFin'    => $fechaFin,
            ]);

        case 2:
            // Query 5: Ventas por sucursal
            $sucursales = DB::select("
                SELECT
                    s.nombre        AS sucursal,
                    SUM(f.monto_facturado) AS total,
                    COUNT(f.id_venta)      AS ventas
                FROM sig_ventas f
                INNER JOIN sig_vendedores v ON f.id_vendedor = v.id_vendedor
                INNER JOIN sig_sucursales s ON v.id_sucursal = s.id_sucursal
                WHERE f.fecha BETWEEN ? AND ?
                GROUP BY s.id_sucursal
            ", [$fechaInicio, $fechaFin]);

            return Inertia::render('Informes/InformeSucursal', [
                'sucursales'  => $sucursales,
                'totales'     => $totales,
                'fechaInicio' => $fechaInicio,
                'fechaFin'    => $fechaFin,
            ]);
    }
}
```

---

### 3.5 Módulos de Consulta (Clientes, Proveedores, Productos, Sucursales)

Estos módulos siguen el mismo patrón de diseño: **solo lectura con JOIN a tablas `sig_*`**.

#### Patrón de Diseño Compartido

```php
public function index()
{
    $data = DB::select("
        SELECT [columnas] FROM [tabla_principal]
        INNER JOIN sig_paises ON ...
        [INNER JOIN adicionales]
    ");

    return Inertia::render('[Vista]', ['[variable]' => $data]);
}
```

#### Consultas SQL por Módulo

**Clientes:**
```sql
SELECT cl.id_cliente, cl.nombre_cliente, p.nombre AS pais,
       cl.nit, cl.direccion, cl.contacto_nombre, cl.telefono, cl.fecha_ingreso
FROM sig_clientes cl
INNER JOIN sig_paises p ON cl.id_pais = p.id_pais
```

**Proveedores:**
```sql
SELECT pr.id_proveedor, pr.nombre_proveedor, p.nombre AS pais,
       pr.direccion, pr.telefono, pr.nombre_contacto
FROM sig_proveedores pr
INNER JOIN sig_paises p ON pr.id_pais = p.id_pais
```

**Productos:**
```sql
SELECT p.id_producto, p.codigo_producto, p.nombre_producto,
       pv.nombre_proveedor, p.precio_unidad, pa.nombre AS pais,
       p.fecha_compra, p.lote_numero
FROM sig_productos p
INNER JOIN sig_proveedores pv ON p.id_proveedor = pv.id_proveedor
INNER JOIN sig_paises pa ON p.id_pais = pa.id_pais
```

**Sucursales:**
```sql
-- Sucursales
SELECT s.id_sucursal, s.codigo, s.nombre, p.nombre AS pais
FROM sig_sucursales s
INNER JOIN sig_paises p ON s.id_pais = p.id_pais

-- Vendedores
SELECT v.id_vendedor, v.codigo_vendedor, v.nombre_vendedor,
       v.fecha_ingreso, v.numero_documento,
       p.nombre AS pais, s.nombre AS sucursal
FROM sig_vendedores v
INNER JOIN sig_sucursales s ON v.id_sucursal = s.id_sucursal
INNER JOIN sig_paises p ON v.id_pais = p.id_pais
```

---

## 4. Diseño del Middleware de Inertia

### `HandleInertiaRequests`

Este middleware es el punto central de comunicación entre backend y frontend. Se ejecuta en cada request del grupo `web`.

```php
public function share(Request $request): array
{
    return array_merge(parent::share($request), [
        // Mensajes flash para notificaciones
        'flash' => [
            'success' => fn () => $request->session()->get('success'),
            'error'   => fn () => $request->session()->get('error'),
        ],
        // Datos de autorización para vue-gates
        'auth' => [
            'roles'    => fn () => $request->user()
                ? $request->user()->getRoleNames()
                : [],
            'permisos' => fn () => $request->user()
                ? $request->user()->getAllPermissions()->pluck('name')
                : [],
        ],
    ]);
}
```

**Impacto:** Estos datos están disponibles en TODOS los componentes Vue como `usePage().props.value.flash` y `usePage().props.value.auth`.

---

## 5. Diseño del Frontend

### 5.1 Entry Point: `app.js`

```javascript
import { createApp, h } from 'vue'
import { createInertiaApp }    from '@inertiajs/inertia-vue3'
import { InertiaProgress }     from '@inertiajs/progress'
import VueGates                from 'vue-gates'
import Permissions             from './Plugins/Permissions'
import HighchartsVue           from 'highcharts-vue'
import VCalendar               from 'v-calendar'

InertiaProgress.init({ color: '#2563EB' }) // Barra de progreso azul

createInertiaApp({
    resolve: name => require(`./Pages/${name}.vue`),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(VueGates)
            .use(Permissions)
            .use(HighchartsVue)
            .use(VCalendar)
            .mount(el)
    },
})
```

### 5.2 Plugin de Permisos: `Permissions.js`

```javascript
import { usePage } from '@inertiajs/inertia-vue3'

export default {
    install(app) {
        app.mixin({
            mounted() {
                const auth = usePage().props.value.auth
                if (auth) {
                    this.$gates.setRoles(auth.roles)
                    this.$gates.setPermissions(auth.permisos)
                }
            }
        })
    }
}
```

### 5.3 Layout Principal: `Laradash.vue`

```
Laradash.vue
├── <Head> (título de la página)
├── <SidebarMobil> (visible en móvil, oculto en desktop)
├── <div class="lg:pl-64"> (margen para sidebar desktop)
│   ├── <Header> (barra superior con usuario, menú, dark mode)
│   └── <main>
│       └── <slot /> ← Aquí se renderiza el Page component
└── <SidebarDesktop> (fixed, visible solo en desktop)
```

### 5.4 Componente `Table.vue`

Tabla reutilizable con:
- Slot `#header` para definir columnas (`<th>`)
- Slot `#rows` para definir filas (`<tr>`)
- Slot `#pagination` para el componente `Pagination.vue`

```vue
<!-- Uso típico -->
<Table>
  <template #header>
    <th>Nombre</th>
    <th>Email</th>
  </template>
  <template #rows>
    <tr v-for="u in usuarios.data" :key="u.id">
      <td>{{ u.name }}</td>
      <td>{{ u.email }}</td>
    </tr>
  </template>
  <template #pagination>
    <Pagination :links="usuarios.links" />
  </template>
</Table>
```

### 5.5 Exportación PDF

```javascript
// En InformeVentas.vue
import html2pdf from 'html2pdf.js'

const exportarPDF = () => {
    const elemento = document.getElementById('informe-container')
    html2pdf()
        .set({
            margin: 1,
            filename: `informe_ventas_${fechaInicio}_${fechaFin}.pdf`,
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2 },
            jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
        })
        .from(elemento)
        .save()
}
```

### 5.6 Gráfico de Ventas (Highcharts)

```javascript
// Configuración del gráfico de área en InformeVentas.vue
const chartOptions = computed(() => ({
    chart: { type: 'area' },
    title: { text: 'Ventas Últimos 6 Meses' },
    xAxis: {
        categories: props.grafico.map(m => m.mes)
    },
    yAxis: {
        title: { text: 'Monto Facturado ($)' }
    },
    series: [{
        name: 'Ventas',
        data: props.grafico.map(m => parseFloat(m.total))
    }]
}))
```

---

## 6. Diseño de la Base de Datos

### 6.1 Inicialización

La base de datos se inicializa desde `/database/init_scripts/laradash_init.sql` al crear el contenedor por primera vez. Este dump contiene:

1. Estructura de todas las tablas (DDL)
2. Datos de ejemplo en las tablas `sig_*`
3. Datos iniciales de roles y permisos
4. Usuarios de prueba

### 6.2 Migraciones Laravel

Las migraciones gestionan únicamente las tablas del framework, **no las tablas de negocio `sig_*`**:

| Archivo de Migración | Tabla Creada | Propósito |
|---|---|---|
| `2014_10_12_000000_create_users_table` | `users` | Autenticación de usuarios |
| `2014_10_12_100000_create_password_resets_table` | `password_resets` | Recuperación de contraseña |
| `2014_10_12_200000_add_two_factor_columns_to_users_table` | `users` (alter) | Columnas 2FA de Jetstream |
| `2019_08_19_000000_create_failed_jobs_table` | `failed_jobs` | Queue jobs fallidos |
| `2019_12_14_000001_create_personal_access_tokens_table` | `personal_access_tokens` | Sanctum API tokens |
| `2021_09_03_071147_create_sessions_table` | `sessions` | Sesiones en BD |
| `2021_09_05_181617_create_permission_tables` | `permissions`, `roles`, `model_has_*`, `role_has_*` | RBAC Spatie |

### 6.3 Seeder

```php
// DatabaseSeeder.php
public function run()
{
    $this->call([UserSeeder::class]);   // Roles, permisos, usuarios admin
    User::factory(100)->create();       // 100 usuarios de prueba con Faker
}
```

**UserSeeder crea:**
- Rol `super admin` + Rol `invitado`
- 9 permisos (CRUD de roles, usuarios + ver permisos)
- Usuario admin: `admin@mail.com` / `12345678`
- Usuario invitado: `invitado@mail.com` / `12345678`

**Nota:** El ambiente Docker actual usa el dump SQL (no seeders). Los usuarios reales en la BD son diferentes a los del seeder.

---

## 7. Configuración del Servidor Web

### Apache Virtual Host

```apache
# virtualhosts/sig_tdc/000-default.conf
<VirtualHost *:80>
    DocumentRoot /var/www/html/public

    <Directory /var/www/html/public>
        Options Indexes FollowSymLinks
        AllowOverride All        ← Necesario para .htaccess de Laravel
        Require all granted
    </Directory>

    ErrorLog  /var/log/apache2/sig_tdc_error.log
    CustomLog /var/log/apache2/sig_tdc_access.log combined
</VirtualHost>
```

### `.htaccess` de Laravel (`public/.htaccess`)

```apache
RewriteEngine On
RewriteRule ^(.*)$ index.php [L]   ← Todo el tráfico va a index.php
```

El `DocumentRoot` apunta a `/var/www/html/public`, no a la raíz del proyecto. Esto protege los archivos PHP del acceso directo desde el navegador.

---

## 8. Inventario Completo de Archivos

### Backend PHP

| Archivo | Clase | Tipo |
|---|---|---|
| `app/Http/Controllers/Controller.php` | Controller | Base controller |
| `app/Http/Controllers/Laradash/ClientesController.php` | ClientesController | Controller |
| `app/Http/Controllers/Laradash/InformesController.php` | InformesController | Controller |
| `app/Http/Controllers/Laradash/PermisosController.php` | PermisosController | Controller |
| `app/Http/Controllers/Laradash/ProductosController.php` | ProductosController | Controller |
| `app/Http/Controllers/Laradash/ProveedoresController.php` | ProveedoresController | Controller |
| `app/Http/Controllers/Laradash/RolesController.php` | RolesController | Controller |
| `app/Http/Controllers/Laradash/SucursalesController.php` | SucursalesController | Controller |
| `app/Http/Controllers/Laradash/UsuariosController.php` | UsuariosController | Controller |
| `app/Http/Middleware/HandleInertiaRequests.php` | HandleInertiaRequests | Middleware |
| `app/Http/Requests/UserRequest.php` | UserRequest | Form Request |
| `app/Http/Kernel.php` | Kernel | HTTP Kernel |
| `app/Models/User.php` | User | Model |
| `app/Policies/UserPolicy.php` | UserPolicy | Policy |
| `app/Providers/AuthServiceProvider.php` | AuthServiceProvider | Provider |
| `app/Providers/AppServiceProvider.php` | AppServiceProvider | Provider |
| `app/Providers/RouteServiceProvider.php` | RouteServiceProvider | Provider |

### Frontend Vue

| Archivo | Tipo |
|---|---|
| `resources/js/app.js` | Entry point |
| `resources/js/Plugins/Permissions.js` | Plugin Vue |
| `resources/js/Layouts/Laradash.vue` | Layout principal |
| `resources/js/Components/Table.vue` | Componente tabla |
| `resources/js/Components/Pagination.vue` | Paginación |
| `resources/js/Components/Header.vue` | Header |
| `resources/js/Components/SidebarDesktop.vue` | Sidebar desktop |
| `resources/js/Components/SidebarNormal.vue` | Sidebar colapsado |
| `resources/js/Components/SidebarMobil.vue` | Sidebar móvil |
| `resources/js/Components/DarkMode.vue` | Toggle dark mode |
| `resources/js/Components/GitHub.vue` | Enlace GitHub |
| `resources/js/Components/SucursalesChart.vue` | Gráfico sucursales |
| `resources/js/Pages/Welcome.vue` | Página bienvenida |
| `resources/js/Pages/Dashboard.vue` | Dashboard principal |
| `resources/js/Pages/Demo.vue` | Página demo |
| `resources/js/Pages/Otros/Clientes.vue` | Lista clientes |
| `resources/js/Pages/Otros/Proveedores.vue` | Lista proveedores |
| `resources/js/Pages/Otros/Productos.vue` | Lista productos |
| `resources/js/Pages/Otros/Sucursales.vue` | Sucursales y vendedores |
| `resources/js/Pages/Otros/Permisos.vue` | Lista permisos |
| `resources/js/Pages/Otros/Roles.vue` | CRUD roles |
| `resources/js/Pages/Otros/InformesGerenciales.vue` | Selector de informes |
| `resources/js/Pages/Otros/Usuarios/Usuarios.vue` | CRUD usuarios |
| `resources/js/Pages/Otros/Usuarios/VerUsuario.vue` | Perfil de usuario |
| `resources/js/Pages/Informes/InformeVentas.vue` | Informe por vendedor |
| `resources/js/Pages/Informes/InformeSucursal.vue` | Informe por sucursal |

### Rutas

| Archivo | Contenido |
|---|---|
| `routes/web.php` | Rutas raíz, demo y dashboard |
| `routes/api.php` | GET /api/user |
| `routes/laradash/otros.php` | Rutas de todos los módulos del sistema |

### Base de Datos

| Archivo | Propósito |
|---|---|
| `database/migrations/*.php` | 7 migraciones de framework |
| `database/seeders/DatabaseSeeder.php` | Seeder principal |
| `database/seeders/UserSeeder.php` | Roles, permisos y usuarios |
| `database/factories/UserFactory.php` | Fábrica de usuarios Faker |
| `database/init_scripts/laradash_init.sql` | Dump completo de la BD |

---

## 9. Variables de Entorno

| Variable | Valor en Docker | Descripción |
|---|---|---|
| `APP_NAME` | SIG_TDC | Nombre de la aplicación |
| `APP_ENV` | local | Ambiente (local/production) |
| `APP_KEY` | base64:... | Clave de cifrado (generada en entrypoint) |
| `APP_DEBUG` | true | Mostrar errores detallados |
| `APP_URL` | http://localhost:9252 | URL base |
| `DB_CONNECTION` | mysql | Driver de BD |
| `DB_HOST` | sig_tdc_db | Hostname del contenedor MySQL |
| `DB_PORT` | 3306 | Puerto MySQL interno |
| `DB_DATABASE` | laradash | Nombre de la base de datos |
| `DB_USERNAME` | root | Usuario MySQL |
| `DB_PASSWORD` | laradash_root | Contraseña MySQL |
| `CACHE_DRIVER` | file | Driver de caché |
| `SESSION_DRIVER` | file | Driver de sesión |
| `SESSION_LIFETIME` | 120 | Minutos de expiración de sesión |
| `QUEUE_CONNECTION` | sync | Cola síncrona (sin workers) |
| `FILESYSTEM_DISK` | local | Disco para Storage |
| `MAIL_MAILER` | smtp | Enviador de correos |
| `MAIL_HOST` | mailhog | Host SMTP (solo dev) |

---

## 10. Usuarios del Sistema (Estado Actual en BD)

Los usuarios en la base de datos productiva (cargada desde `laradash_init.sql`) son:

| ID | Nombre | Email | Rol |
|---|---|---|---|
| 1 | Heinrich Sanchez | super@tdc.com | super admin |
| 2 | Admin | gerencia@tdc.com | (verificar) |
| 3 | Invitado | invitado@tdc.com | invitado |
| 4 | Soporte Tecnico | soporte@tdc.com | (verificar) |
| 5 | Marketing | marketing@tdc.com | (verificar) |
| 6 | David Rodriguez | david@tdc.com | (verificar) |

**Contraseña actualizada:** `super@tdc.com` → `12345678`

---

## 11. Issues de Diseño Identificados

| # | Descripción | Severidad | Módulo |
|---|---|---|---|
| I-01 | `UserRequest` usa `unique:users` sin excluir el usuario actual en edición | Media | Usuarios |
| I-02 | Queries SQL directos en controladores sin uso de Eloquent/Repository | Baja | Informes, Sucursales, etc. |
| I-03 | La foto de perfil se recibe como base64 en el request; archivos grandes pueden exceder límites PHP | Media | Usuarios |
| I-04 | No existe autorización en los módulos de consulta (Clientes, Proveedores, etc.) | Media | Todos los de solo lectura |
| I-05 | La exportación PDF en cliente puede fallar en contenido complejo o muy extenso | Baja | Informes |
| I-06 | `Gate::before()` para super admin retorna `true` (debería retornar `null` para no-super-admin) | Baja | AuthServiceProvider |
