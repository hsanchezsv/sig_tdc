<template>
    <LaraDashLayout>
        <Head title="Productos"/>
        <div class="flex justify-between mb-2">
            <h2 class="my-3 text-2xl font-semibold text-gray-600 dark:text-gray-200">
                Productos
            </h2>
        </div>

        <LaraDashTable class="max-w-6xl">
            <template #col>
                <th class="px-4 py-3 text-xs">Id Producto</th>
                <th class="px-4 py-3 text-xs">Codigo</th>
                <th class="px-4 py-3 text-xs">Nombre</th>
                <th class="px-4 py-3 text-xs">Proveedor</th>
                <th class="px-4 py-3 text-xs">Pais Origen</th>
                <th class="px-4 py-3 text-xs">Precio Unitario</th>
                <th class="px-4 py-3 text-xs">Fecha Compra</th>
                <th class="px-4 py-3 text-xs">Lote #</th>
            </template>
            <template #row>
                <tr class="text-gray-500 hover:bg-gray-200 dark:hover:bg-gray-600 dark:text-gray-300" v-for="p in productos" :key="p.id">
                    <td class="px-4 py-1 text-xs">{{ p.id_producto }}</td>
                    <td class="px-4 py-1 text-xs">{{ p.codigo_producto }}</td>
                    <td class="px-4 py-1 text-xs">{{ p.nombre_producto }}</td>
                    <td class="px-4 py-1 text-xs">{{ p.nombre_proveedor }}</td>
                    <td class="px-4 py-1 text-xs">{{ p.pais }}</td>
                    <td class="px-4 py-1 text-xs">{{ p.precio_unidad }}</td>
                    <td class="px-4 py-1 text-xs">{{ p.fecha_compra }}</td>
                    <td class="px-4 py-1 text-xs">{{ p.lote_numero }}</td>
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

    // declaraciones
    const props = defineProps({
        productos: Object
    })
    const productos = props.productos
        

</script>

