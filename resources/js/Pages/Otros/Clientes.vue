<template>
    <LaraDashLayout>
        <Head title="Clientes"/>
        <div class="flex justify-between mb-2">
            <h2 class="my-3 text-2xl font-semibold text-gray-600 dark:text-gray-200">
                Clientes
            </h2>
            <button @click="abrirModalCrear" class="btn-blue my-2">+</button>
        </div>

        <!-- Búsqueda -->
        <div class="mb-4">
            <input
                v-model="buscar"
                @keyup.enter="realizarBusqueda"
                type="text"
                placeholder="Buscar por nombre o NIT..."
                class="laradash-input max-w-xs"
            />
        </div>

        <LaraDashTable class="max-w-6xl">
            <template #col>
                <th class="px-4 py-3 text-xs">ID</th>
                <th class="px-4 py-3 text-xs">Nombre Cliente</th>
                <th class="px-4 py-3 text-xs">País</th>
                <th class="px-4 py-3 text-xs">NIT/RTU</th>
                <th class="px-4 py-3 text-xs">Dirección</th>
                <th class="px-4 py-3 text-xs">Contacto</th>
                <th class="px-4 py-3 text-xs">Teléfono</th>
                <th class="px-4 py-3 text-xs">Fecha Ingreso</th>
                <th class="px-4 py-3 text-xs">Acción</th>
            </template>
            <template #row>
                <tr
                    v-for="c in clientes.data"
                    :key="c.id_cliente"
                    class="text-gray-500 hover:bg-gray-200 dark:hover:bg-gray-600 dark:text-gray-300"
                >
                    <td class="px-4 py-1 text-xs">{{ c.id_cliente }}</td>
                    <td class="px-4 py-1 text-xs">{{ c.nombre_cliente }}</td>
                    <td class="px-4 py-1 text-xs">{{ c.pais?.nombre }}</td>
                    <td class="px-4 py-1 text-xs">{{ c.nit }}</td>
                    <td class="px-4 py-1 text-xs">{{ c.direccion }}</td>
                    <td class="px-4 py-1 text-xs">{{ c.contacto_nombre }}</td>
                    <td class="px-4 py-1 text-xs">{{ c.telefono }}</td>
                    <td class="px-4 py-1 text-xs">{{ c.fecha_ingreso }}</td>
                    <td class="px-4 py-1 text-xs flex gap-2">
                        <button @click="abrirModalEditar(c)" class="hover:text-blue-600">Editar</button>
                        <button @click="eliminar(c.id_cliente)" class="hover:text-red-600">Eliminar</button>
                    </td>
                </tr>
            </template>
            <template #pagination>
                <LaraDashPagination
                    :links="clientes.links"
                    :total="clientes.total"
                    :to="clientes.to"
                    :from="clientes.from"
                />
            </template>
        </LaraDashTable>

        <!-- Modal Crear -->
        <JetDialogModal v-if="modalCrear" :show="modalCrear" @close="modalCrear = false" max-width="xl">
            <template #title>Nuevo Cliente</template>
            <template #content>
                <form @submit.prevent="grabar" class="flex flex-col gap-2">
                    <label class="block">
                        <span class="laradash-label">Nombre Cliente *</span>
                        <input v-model="form.nombre_cliente" class="laradash-input" />
                        <span v-if="errores.nombre_cliente" class="text-red-500 text-xs">{{ errores.nombre_cliente }}</span>
                    </label>
                    <label class="block">
                        <span class="laradash-label">País *</span>
                        <select v-model="form.id_pais" class="laradash-input">
                            <option value="">Seleccione...</option>
                            <option v-for="p in paises" :key="p.id_pais" :value="p.id_pais">{{ p.nombre }}</option>
                        </select>
                        <span v-if="errores.id_pais" class="text-red-500 text-xs">{{ errores.id_pais }}</span>
                    </label>
                    <label class="block">
                        <span class="laradash-label">NIT/RTU</span>
                        <input v-model="form.nit" class="laradash-input" />
                    </label>
                    <label class="block">
                        <span class="laradash-label">Dirección</span>
                        <input v-model="form.direccion" class="laradash-input" />
                    </label>
                    <label class="block">
                        <span class="laradash-label">Contacto</span>
                        <input v-model="form.contacto_nombre" class="laradash-input" />
                    </label>
                    <label class="block">
                        <span class="laradash-label">Teléfono</span>
                        <input v-model="form.telefono" class="laradash-input" />
                    </label>
                    <label class="block">
                        <span class="laradash-label">Fecha Ingreso</span>
                        <input v-model="form.fecha_ingreso" type="date" class="laradash-input" />
                    </label>
                    <button class="btn-blue mt-2">Guardar</button>
                </form>
            </template>
        </JetDialogModal>

        <!-- Modal Editar -->
        <JetDialogModal v-if="modalEditar" :show="modalEditar" @close="modalEditar = false" max-width="xl">
            <template #title>Editar Cliente</template>
            <template #content>
                <form @submit.prevent="actualizar" class="flex flex-col gap-2">
                    <label class="block">
                        <span class="laradash-label">Nombre Cliente *</span>
                        <input v-model="form.nombre_cliente" class="laradash-input" />
                        <span v-if="errores.nombre_cliente" class="text-red-500 text-xs">{{ errores.nombre_cliente }}</span>
                    </label>
                    <label class="block">
                        <span class="laradash-label">País *</span>
                        <select v-model="form.id_pais" class="laradash-input">
                            <option value="">Seleccione...</option>
                            <option v-for="p in paises" :key="p.id_pais" :value="p.id_pais">{{ p.nombre }}</option>
                        </select>
                        <span v-if="errores.id_pais" class="text-red-500 text-xs">{{ errores.id_pais }}</span>
                    </label>
                    <label class="block">
                        <span class="laradash-label">NIT/RTU</span>
                        <input v-model="form.nit" class="laradash-input" />
                    </label>
                    <label class="block">
                        <span class="laradash-label">Dirección</span>
                        <input v-model="form.direccion" class="laradash-input" />
                    </label>
                    <label class="block">
                        <span class="laradash-label">Contacto</span>
                        <input v-model="form.contacto_nombre" class="laradash-input" />
                    </label>
                    <label class="block">
                        <span class="laradash-label">Teléfono</span>
                        <input v-model="form.telefono" class="laradash-input" />
                    </label>
                    <label class="block">
                        <span class="laradash-label">Fecha Ingreso</span>
                        <input v-model="form.fecha_ingreso" type="date" class="laradash-input" />
                    </label>
                    <button class="btn-blue mt-2">Actualizar</button>
                </form>
            </template>
        </JetDialogModal>

    </LaraDashLayout>
</template>

<script setup>
import LaraDashLayout from '@/Layouts/Laradash'
import { Head } from '@inertiajs/inertia-vue3'
import LaraDashTable from '@/Components/Table'
import LaraDashPagination from '@/Components/Pagination'
import JetDialogModal from '@/Jetstream/DialogModal'
import { ref, reactive } from 'vue'
import { Inertia } from '@inertiajs/inertia'

const props = defineProps({
    clientes: Object,
    paises:   Array,
    filtro:   String,
})

const buscar      = ref(props.filtro ?? '')
const modalCrear  = ref(false)
const modalEditar = ref(false)
const registroActual = ref(null)
const errores = reactive({})

const form = reactive({
    nombre_cliente:  '',
    id_pais:         '',
    nit:             '',
    direccion:       '',
    contacto_nombre: '',
    telefono:        '',
    fecha_ingreso:   '',
})

const resetForm = () => {
    Object.assign(form, {
        nombre_cliente: '', id_pais: '', nit: '',
        direccion: '', contacto_nombre: '', telefono: '', fecha_ingreso: '',
    })
    Object.keys(errores).forEach(k => delete errores[k])
}

const realizarBusqueda = () => {
    Inertia.get(route('clientes.index'), { buscar: buscar.value }, { preserveState: true })
}

const abrirModalCrear = () => {
    resetForm()
    modalCrear.value = true
}

const abrirModalEditar = (cliente) => {
    resetForm()
    registroActual.value = cliente
    Object.assign(form, {
        nombre_cliente:  cliente.nombre_cliente,
        id_pais:         cliente.id_pais,
        nit:             cliente.nit ?? '',
        direccion:       cliente.direccion ?? '',
        contacto_nombre: cliente.contacto_nombre ?? '',
        telefono:        cliente.telefono ?? '',
        fecha_ingreso:   cliente.fecha_ingreso ?? '',
    })
    modalEditar.value = true
}

const grabar = () => {
    Inertia.post(route('clientes.store'), form, {
        preserveScroll: true,
        onSuccess: () => { modalCrear.value = false; resetForm() },
        onError: (e) => Object.assign(errores, e),
    })
}

const actualizar = () => {
    Inertia.put(route('clientes.update', registroActual.value.id_cliente), form, {
        preserveScroll: true,
        onSuccess: () => { modalEditar.value = false },
        onError: (e) => Object.assign(errores, e),
    })
}

const eliminar = (id) => {
    if (confirm('¿Eliminar este cliente?')) {
        Inertia.delete(route('clientes.destroy', id), { preserveScroll: true })
    }
}
</script>
