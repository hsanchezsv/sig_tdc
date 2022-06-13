<template>
    <nav class="mt-6">
        <div v-role="'super admin'" class="flex flex-col w-full">
            <Link :href="route('dashboard')" class="w-full justify-start"
                :class="$page.url === '/dashboard' ? 'linkClassActive' : 'linkClass'">
                <span class="text-left">
                    <IconItem/>
                </span>
                <span class="mx-4 text-xs font-normal">
                    Dashboard
                </span>
            </Link>
        
            <p class="p-3 text-xs uppercase text-gray-400">MODULOS</p>
            <Link :href="route('usuarios.index')" class="w-full justify-start"
                :class="$page.url.startsWith('/usuarios') ? 'linkClassActive' : 'linkClass'">
                <span class="text-left">
                    <IconItem/>
                </span>
                <span class="mx-4 text-xs font-normal">
                    Usuarios
                </span>
            </Link>

            <Link :href="route('roles.index')" class="w-full justify-start"
                :class="$page.url === '/roles' ? 'linkClassActive' : 'linkClass'">
                <span class="text-left">
                    <IconItem/>
                </span>
                <span class="mx-4 text-xs font-normal">
                    Roles
                </span>
            </Link>

            <Link :href="route('sucursales.index')" class="w-full justify-start"
                :class="$page.url === '/sucursales' ? 'linkClassActive' : 'linkClass'">
                <span class="text-left">
                    <IconItem/>
                </span>
                <span class="mx-4 text-xs font-normal">
                    Sucursales/Vendedores
                </span>
            </Link>

            <Link :href="route('proveedores.index')" class="w-full justify-start"
                :class="$page.url === '/proveedores' ? 'linkClassActive' : 'linkClass'">
                <span class="text-left">
                    <IconItem/>
                </span>
                <span class="mx-4 text-xs font-normal">
                    Proveedores
                </span>
            </Link>

            <Link :href="route('productos.index')" class="w-full justify-start"
                :class="$page.url === '/productos' ? 'linkClassActive' : 'linkClass'">
                <span class="text-left">
                    <IconItem/>
                </span>
                <span class="mx-4 text-xs font-normal">
                    Productos
                </span>
            </Link>

            <Link :href="route('clientes.index')" class="w-full justify-start"
                :class="$page.url === '/clientes' ? 'linkClassActive' : 'linkClass'">
                <span class="text-left">
                    <IconItem/>
                </span>
                <span class="mx-4 text-xs font-normal">
                    Clientes
                </span>
            </Link>

            <Link :href="route('informes.index')" class="w-full justify-start"
                :class="$page.url === '/informes' ? 'linkClassActive' : 'linkClass'">
                <span class="text-left">
                    <IconItem/>
                </span>
                <span class="mx-4 text-xs font-normal">
                    Informes Gerenciales
                </span>
            </Link>
        </div>




        <div v-role="'gerencias'" class="flex flex-col w-full">
            <Link :href="route('dashboard')" class="w-full justify-start"
                :class="$page.url === '/dashboard' ? 'linkClassActive' : 'linkClass'">
                <span class="text-left">
                    <IconItem/>
                </span>
                <span class="mx-4 text-xs font-normal">
                    Dashboard
                </span>
            </Link>
        
            <p class="p-3 text-xs uppercase text-gray-400">MODULOS</p>


            <Link :href="route('sucursales.index')" class="w-full justify-start"
                :class="$page.url === '/sucursales' ? 'linkClassActive' : 'linkClass'">
                <span class="text-left">
                    <IconItem/>
                </span>
                <span class="mx-4 text-xs font-normal">
                    Sucursales/Vendedores
                </span>
            </Link>

            <Link :href="route('proveedores.index')" class="w-full justify-start"
                :class="$page.url === '/proveedores' ? 'linkClassActive' : 'linkClass'">
                <span class="text-left">
                    <IconItem/>
                </span>
                <span class="mx-4 text-xs font-normal">
                    Proveedores
                </span>
            </Link>

            <Link :href="route('productos.index')" class="w-full justify-start"
                :class="$page.url === '/productos' ? 'linkClassActive' : 'linkClass'">
                <span class="text-left">
                    <IconItem/>
                </span>
                <span class="mx-4 text-xs font-normal">
                    Productos
                </span>
            </Link>

            <Link :href="route('clientes.index')" class="w-full justify-start"
                :class="$page.url === '/clientes' ? 'linkClassActive' : 'linkClass'">
                <span class="text-left">
                    <IconItem/>
                </span>
                <span class="mx-4 text-xs font-normal">
                    Clientes
                </span>
            </Link>

            <Link :href="route('informes.index')" class="w-full justify-start"
                :class="$page.url === '/informes' ? 'linkClassActive' : 'linkClass'">
                <span class="text-left">
                    <IconItem/>
                </span>
                <span class="mx-4 text-xs font-normal">
                    Informes Gerenciales
                </span>
            </Link>
        </div>




        <div v-role="'administracion'" class="flex flex-col w-full">
            <Link :href="route('dashboard')" class="w-full justify-start"
                :class="$page.url === '/dashboard' ? 'linkClassActive' : 'linkClass'">
                <span class="text-left">
                    <IconItem/>
                </span>
                <span class="mx-4 text-xs font-normal">
                    Dashboard
                </span>
            </Link>
        
            <p class="p-3 text-xs uppercase text-gray-400">MODULOS</p>


            <Link :href="route('sucursales.index')" class="w-full justify-start"
                :class="$page.url === '/sucursales' ? 'linkClassActive' : 'linkClass'">
                <span class="text-left">
                    <IconItem/>
                </span>
                <span class="mx-4 text-xs font-normal">
                    Sucursales/Vendedores
                </span>
            </Link>

            <Link :href="route('proveedores.index')" class="w-full justify-start"
                :class="$page.url === '/proveedores' ? 'linkClassActive' : 'linkClass'">
                <span class="text-left">
                    <IconItem/>
                </span>
                <span class="mx-4 text-xs font-normal">
                    Proveedores
                </span>
            </Link>

            <Link :href="route('productos.index')" class="w-full justify-start"
                :class="$page.url === '/productos' ? 'linkClassActive' : 'linkClass'">
                <span class="text-left">
                    <IconItem/>
                </span>
                <span class="mx-4 text-xs font-normal">
                    Productos
                </span>
            </Link>

            <Link :href="route('clientes.index')" class="w-full justify-start"
                :class="$page.url === '/clientes' ? 'linkClassActive' : 'linkClass'">
                <span class="text-left">
                    <IconItem/>
                </span>
                <span class="mx-4 text-xs font-normal">
                    Clientes
                </span>
            </Link>
        </div>
    </nav>
</template>

<script setup>
import { Link } from '@inertiajs/inertia-vue3';
import IconItem from '@/Icons/Item'
</script>

