<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Roles
        $superAdminRole   = Role::create(['name' => 'super admin']);
        $gerenciasRole    = Role::create(['name' => 'gerencias']);
        $adminRole        = Role::create(['name' => 'administracion']);
        Role::create(['name' => 'invitado']);

        // Permissions
        $pVerRole     = Permission::create(['name' => 'ver:role']);
        $pCrearRole   = Permission::create(['name' => 'crear:role']);
        $pEditarRole  = Permission::create(['name' => 'editar:role']);
        $pElimRole    = Permission::create(['name' => 'eliminar:role']);
        $pVerPerm     = Permission::create(['name' => 'ver:permiso']);
        $pVerUsr      = Permission::create(['name' => 'ver:usuario']);
        $pCrearUsr    = Permission::create(['name' => 'crear:usuario']);
        $pEditarUsr   = Permission::create(['name' => 'editar:usuario']);
        $pElimUsr     = Permission::create(['name' => 'eliminar:usuario']);

        // Asignar todos los permisos al rol super admin
        $superAdminRole->givePermissionTo([
            $pVerRole, $pCrearRole, $pEditarRole, $pElimRole,
            $pVerPerm,
            $pVerUsr, $pCrearUsr, $pEditarUsr, $pElimUsr,
        ]);

        // super@tdc.com — acceso total
        $user = new User;
        $user->name = 'Heinrich Sanchez';
        $user->email = 'super@tdc.com';
        $user->password = bcrypt('admin123');
        $user->email_verified_at = now();
        $user->save();
        $user->assignRole($superAdminRole);

        // gerencia@tdc.com — módulos de negocio + informes (sin gestión de usuarios/roles)
        $user = new User;
        $user->name = 'Admin';
        $user->email = 'gerencia@tdc.com';
        $user->password = bcrypt('admin123');
        $user->email_verified_at = now();
        $user->save();
        $user->assignRole($gerenciasRole);

        // invitado@tdc.com — módulos básicos (clientes, proveedores, productos, sucursales)
        $user = new User;
        $user->name = 'Invitado';
        $user->email = 'invitado@tdc.com';
        $user->password = bcrypt('admin123');
        $user->email_verified_at = now();
        $user->save();
        $user->assignRole($adminRole);
    }
}
