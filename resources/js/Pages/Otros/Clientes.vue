<template>
    <LaraDashLayout>
        <Head title="Clientes"/>
        <div class="flex justify-between mb-2">
            <h2 class="my-3 text-2xl font-semibold text-gray-600 dark:text-gray-200">
                Clientes
            </h2>
        </div>

        <LaraDashTable class="max-w-6xl">
            <template #col>
                <th class="px-4 py-3 text-xs">Id Cliente</th>
                <th class="px-4 py-3 text-xs">Nombre Cliente</th>
                <th class="px-4 py-3 text-xs">Pais</th>
                <th class="px-4 py-3 text-xs">NIT/RTU</th>
                <th class="px-4 py-3 text-xs">Direccion</th>
                <th class="px-4 py-3 text-xs">Contacto</th>
                <th class="px-4 py-3 text-xs">Telefono</th>
                <th class="px-4 py-3 text-xs">Fecha Ingreso</th>
            </template>
            <template #row>
                <tr class="text-gray-500 hover:bg-gray-200 dark:hover:bg-gray-600 dark:text-gray-300" v-for="p in clientes" :key="p.id">
                    <td class="px-4 py-1 text-xs">{{ p.id_cliente }}</td>
                    <td class="px-4 py-1 text-xs">{{ p.nombre_cliente }}</td>
                    <td class="px-4 py-1 text-xs">{{ p.pais }}</td>
                    <td class="px-4 py-1 text-xs">{{ p.nit }}</td>
                    <td class="px-4 py-1 text-xs">{{ p.direccion }}</td>
                    <td class="px-4 py-1 text-xs">{{ p.contacto_nombre }}</td>
                    <td class="px-4 py-1 text-xs">{{ p.telefono }}</td>
                    <td class="px-4 py-1 text-xs">{{ p.fecha_ingreso }}</td>
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
        clientes: Object
    })
    const clientes = props.clientes
        

</script>

