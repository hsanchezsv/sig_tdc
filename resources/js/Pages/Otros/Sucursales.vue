<template>
    <LaraDashLayout>
        <Head title="Sucursales y Vendedores"/>
        <div class="flex justify-between mb-2">
            <h2 class="my-3 text-2xl font-semibold text-gray-600 dark:text-gray-200">
                Sucursales
            </h2>
        </div>
        <LaraDashTable class="mb-6">
            <template #col>
                <th class="px-4 py-3 text-xs">ID</th>
                <th class="px-4 py-3 text-xs">Codigo</th>
                <th class="px-4 py-3 text-xs">Nombre</th>
                <th class="px-4 py-3 text-xs">Pais</th>
            </template>
            <template #row>
                <tr class="text-gray-500 hover:bg-gray-200 dark:hover:bg-gray-600 dark:text-gray-300" v-for="p in sucursales" :key="p.id">
                    <td class="px-4 py-1 text-xs">{{ p.id_sucursal }}</td>
                    <td class="px-4 py-1 text-xs">{{ p.codigo }}</td>
                    <td class="px-4 py-1 text-xs">{{ p.nombre_sucursal }}</td>
                    <td class="px-4 py-1 text-xs">{{ p.pais }}</td>
                </tr>
            </template>
        </LaraDashTable>

        <div class="flex justify-between mb-2">
            <h2 class="my-3 text-2xl font-semibold text-gray-600 dark:text-gray-200">
                Vendedores
            </h2>
        </div>
        <LaraDashTable class="mb-6">
            <template #col>
                <th class="px-4 py-3 text-xs">Id Vendedor</th>
                <th class="px-4 py-3 text-xs">Codigo Vendedor</th>
                <th class="px-4 py-3 text-xs">Nombre</th>
                <th class="px-4 py-3 text-xs">Fecha de Ingreso</th>
                <th class="px-4 py-3 text-xs">Numero de Documento</th>
                <th class="px-4 py-3 text-xs">Pais</th>
                <th class="px-4 py-3 text-xs">Sucursal</th>
            </template>
            <template #row>
                <tr class="text-gray-500 hover:bg-gray-200 dark:hover:bg-gray-600 dark:text-gray-300" v-for="p in vendedores" :key="p.id">
                    <td class="px-4 py-1 text-xs">{{ p.id_vendedor }}</td>
                    <td class="px-4 py-1 text-xs">{{ p.codigo_vendedor }}</td>
                    <td class="px-4 py-1 text-xs">{{ p.nombre_vendedor }}</td>
                    <td class="px-4 py-1 text-xs">{{ p.fecha_ingreso }}</td>
                    <td class="px-4 py-1 text-xs">{{ p.numero_documento }}</td>
                    <td class="px-4 py-1 text-xs">{{ p.pais }}</td>
                    <td class="px-4 py-1 text-xs">{{ p.sucursal }}</td>
                </tr>
            </template>
        </LaraDashTable>

    </LaraDashLayout>
</template>

<script setup>
    // importaciones
    import LaraDashLayout from '@/Layouts/Laradash'
    import { Head } from '@inertiajs/inertia-vue3'
    import LaraDashTable from '@/Components/Table'
    import GitHub from '@/Components/GitHub'
    import SucursalesChart from "@/Components/SucursalesChart";

    // declaraciones
    const props = defineProps({
        sucursales: Object,
        vendedores: Object
    })
    const sucursales = props.sucursales
    const vendedores = props.vendedores
        
</script>

