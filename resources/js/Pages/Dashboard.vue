<template>
    <LaraDashLayout>
        <Head title="Dashboard"/>
        <h2 class="my-3 text-2xl font-semibold text-gray-600 dark:text-gray-200">
            Dashboard
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
            <!-- Ventas del año -->
            <div class="bg-white dark:bg-gray-700 rounded-2xl shadow p-5 flex items-center gap-4">
                <div class="bg-blue-100 dark:bg-blue-900 rounded-xl p-3">
                    <svg class="w-7 h-7 text-blue-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-2.21 0-4 .895-4 2s1.79 2 4 2 4 .895 4 2-1.79 2-4 2m0-8v1m0 10v1M6 12H4m16 0h-2"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-400 dark:text-gray-300 uppercase tracking-wide">Ventas {{ currentYear }}</p>
                    <p class="text-2xl font-bold text-gray-700 dark:text-white">{{ formatMoney(totalVentas) }}</p>
                </div>
            </div>

            <!-- Ventas año anterior -->
            <div class="bg-white dark:bg-gray-700 rounded-2xl shadow p-5 flex items-center gap-4">
                <div class="bg-blue-50 dark:bg-blue-950 rounded-xl p-3">
                    <svg class="w-7 h-7 text-blue-300" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-2.21 0-4 .895-4 2s1.79 2 4 2 4 .895 4 2-1.79 2-4 2m0-8v1m0 10v1M6 12H4m16 0h-2"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-400 dark:text-gray-300 uppercase tracking-wide">Ventas {{ currentYear - 1 }}</p>
                    <p class="text-2xl font-bold text-gray-700 dark:text-white">{{ formatMoney(totalVentasAnterior) }}</p>
                </div>
            </div>

            <!-- Clientes -->
            <div class="bg-white dark:bg-gray-700 rounded-2xl shadow p-5 flex items-center gap-4">
                <div class="bg-green-100 dark:bg-green-900 rounded-xl p-3">
                    <svg class="w-7 h-7 text-green-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6 5.87a4 4 0 100-8 4 4 0 000 8zm6-4a4 4 0 10-8 0"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-400 dark:text-gray-300 uppercase tracking-wide">Clientes</p>
                    <p class="text-2xl font-bold text-gray-700 dark:text-white">{{ totalClientes.toLocaleString() }}</p>
                </div>
            </div>

            <!-- Vendedores -->
            <div class="bg-white dark:bg-gray-700 rounded-2xl shadow p-5 flex items-center gap-4">
                <div class="bg-purple-100 dark:bg-purple-900 rounded-xl p-3">
                    <svg class="w-7 h-7 text-purple-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-400 dark:text-gray-300 uppercase tracking-wide">Vendedores</p>
                    <p class="text-2xl font-bold text-gray-700 dark:text-white">{{ totalVendedores.toLocaleString() }}</p>
                </div>
            </div>

            <!-- Productos -->
            <div class="bg-white dark:bg-gray-700 rounded-2xl shadow p-5 flex items-center gap-4">
                <div class="bg-yellow-100 dark:bg-yellow-900 rounded-xl p-3">
                    <svg class="w-7 h-7 text-yellow-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-400 dark:text-gray-300 uppercase tracking-wide">Productos</p>
                    <p class="text-2xl font-bold text-gray-700 dark:text-white">{{ totalProductos.toLocaleString() }}</p>
                </div>
            </div>

            <!-- Sucursales -->
            <div class="bg-white dark:bg-gray-700 rounded-2xl shadow p-5 flex items-center gap-4">
                <div class="bg-red-100 dark:bg-red-900 rounded-xl p-3">
                    <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 00-1-1h-2a1 1 0 00-1 1v5m4 0H9"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-400 dark:text-gray-300 uppercase tracking-wide">Sucursales</p>
                    <p class="text-2xl font-bold text-gray-700 dark:text-white">{{ totalSucursales.toLocaleString() }}</p>
                </div>
            </div>

            <!-- Proveedores -->
            <div class="bg-white dark:bg-gray-700 rounded-2xl shadow p-5 flex items-center gap-4">
                <div class="bg-indigo-100 dark:bg-indigo-900 rounded-xl p-3">
                    <svg class="w-7 h-7 text-indigo-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-400 dark:text-gray-300 uppercase tracking-wide">Proveedores</p>
                    <p class="text-2xl font-bold text-gray-700 dark:text-white">{{ totalProveedores.toLocaleString() }}</p>
                </div>
            </div>
        </div>
    </LaraDashLayout>
</template>

<script setup>
import LaraDashLayout from '@/Layouts/Laradash'
import { Head } from '@inertiajs/inertia-vue3'

const props = defineProps({
    totalVentas:          Number,
    totalVentasAnterior:  Number,
    totalClientes:        Number,
    totalVendedores:  Number,
    totalProductos:   Number,
    totalSucursales:  Number,
    totalProveedores: Number,
})

const currentYear = new Date().getFullYear()

const formatMoney = (value) => {
    return new Intl.NumberFormat('es-GT', { style: 'currency', currency: 'USD' }).format(value ?? 0)
}
</script>
