<template>
    <LaraDashLayout>
        <Head title="Proveedores"/>
        <div class="flex justify-between mb-2">
            <h2 class="my-3 text-2xl font-semibold text-gray-600 dark:text-gray-200">
                Proveedores
            </h2>
            <button @click="abrirModalCrear" class="btn-blue my-2">+</button>
        </div>

        <!-- Búsqueda -->
        <div class="mb-4">
            <input
                v-model="buscar"
                @keyup.enter="realizarBusqueda"
                type="text"
                placeholder="Buscar por nombre o contacto..."
                class="laradash-input max-w-xs"
            />
        </div>

        <LaraDashTable class="max-w-6xl">
            <template #col>
                <th class="px-4 py-3 text-xs">ID</th>
                <th class="px-4 py-3 text-xs">Nombre Proveedor</th>
                <th class="px-4 py-3 text-xs">País</th>
                <th class="px-4 py-3 text-xs">Dirección</th>
                <th class="px-4 py-3 text-xs">Teléfono</th>
                <th class="px-4 py-3 text-xs">Contacto</th>
                <th class="px-4 py-3 text-xs">Acción</th>
            </template>
            <template #row>
                <tr
                    v-for="p in proveedores.data"
                    :key="p.id_proveedor"
                    class="text-gray-500 hover:bg-gray-200 dark:hover:bg-gray-600 dark:text-gray-300"
                >
                    <td class="px-4 py-1 text-xs">{{ p.id_proveedor }}</td>
                    <td class="px-4 py-1 text-xs">{{ p.nombre_proveedor }}</td>
                    <td class="px-4 py-1 text-xs">{{ p.pais?.nombre }}</td>
                    <td class="px-4 py-1 text-xs">{{ p.direccion }}</td>
                    <td class="px-4 py-1 text-xs">{{ p.telefono }}</td>
                    <td class="px-4 py-1 text-xs">{{ p.nombre_contacto }}</td>
                    <td class="px-4 py-1 text-xs flex gap-2">
                        <button @click="abrirModalEditar(p)" class="hover:text-blue-600">Editar</button>
                        <button @click="eliminar(p.id_proveedor)" class="hover:text-red-600">Eliminar</button>
                    </td>
                </tr>
            </template>
            <template #pagination>
                <LaraDashPagination
                    :links="proveedores.links"
                    :total="proveedores.total"
                    :to="proveedores.to"
                    :from="proveedores.from"
                />
            </template>
        </LaraDashTable>

        <!-- Modal Crear -->
        <JetDialogModal v-if="modalCrear" :show="modalCrear" @close="modalCrear = false" max-width="xl">
            <template #title>Nuevo Proveedor</template>
            <template #content>
                <form @submit.prevent="grabar" class="flex flex-col gap-2">
                    <label class="block">
                        <span class="laradash-label">Nombre Proveedor *</span>
                        <input v-model="form.nombre_proveedor" class="laradash-input" />
                        <span v-if="errores.nombre_proveedor" class="text-red-500 text-xs">{{ errores.nombre_proveedor }}</span>
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
                        <span class="laradash-label">Dirección</span>
                        <input v-model="form.direccion" class="laradash-input" />
                    </label>
                    <label class="block">
                        <span class="laradash-label">Teléfono</span>
                        <input v-model="form.telefono" class="laradash-input" />
                    </label>
                    <label class="block">
                        <span class="laradash-label">Nombre Contacto</span>
                        <input v-model="form.nombre_contacto" class="laradash-input" />
                    </label>
                    <button class="btn-blue mt-2">Guardar</button>
                </form>
            </template>
        </JetDialogModal>

        <!-- Modal Editar -->
        <JetDialogModal v-if="modalEditar" :show="modalEditar" @close="modalEditar = false" max-width="xl">
            <template #title>Editar Proveedor</template>
            <template #content>
                <form @submit.prevent="actualizar" class="flex flex-col gap-2">
                    <label class="block">
                        <span class="laradash-label">Nombre Proveedor *</span>
                        <input v-model="form.nombre_proveedor" class="laradash-input" />
                        <span v-if="errores.nombre_proveedor" class="text-red-500 text-xs">{{ errores.nombre_proveedor }}</span>
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
                        <span class="laradash-label">Dirección</span>
                        <input v-model="form.direccion" class="laradash-input" />
                    </label>
                    <label class="block">
                        <span class="laradash-label">Teléfono</span>
                        <input v-model="form.telefono" class="laradash-input" />
                    </label>
                    <label class="block">
                        <span class="laradash-label">Nombre Contacto</span>
                        <input v-model="form.nombre_contacto" class="laradash-input" />
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
    proveedores: Object,
    paises:      Array,
    filtro:      String,
})

const buscar      = ref(props.filtro ?? '')
const modalCrear  = ref(false)
const modalEditar = ref(false)
const registroActual = ref(null)
const errores = reactive({})

const form = reactive({
    nombre_proveedor: '',
    id_pais:          '',
    direccion:        '',
    telefono:         '',
    nombre_contacto:  '',
})

const resetForm = () => {
    Object.assign(form, {
        nombre_proveedor: '', id_pais: '', direccion: '', telefono: '', nombre_contacto: '',
    })
    Object.keys(errores).forEach(k => delete errores[k])
}

const realizarBusqueda = () => {
    Inertia.get(route('proveedores.index'), { buscar: buscar.value }, { preserveState: true })
}

const abrirModalCrear = () => {
    resetForm()
    modalCrear.value = true
}

const abrirModalEditar = (proveedor) => {
    resetForm()
    registroActual.value = proveedor
    Object.assign(form, {
        nombre_proveedor: proveedor.nombre_proveedor,
        id_pais:          proveedor.id_pais,
        direccion:        proveedor.direccion ?? '',
        telefono:         proveedor.telefono ?? '',
        nombre_contacto:  proveedor.nombre_contacto ?? '',
    })
    modalEditar.value = true
}

const grabar = () => {
    Inertia.post(route('proveedores.store'), form, {
        preserveScroll: true,
        onSuccess: () => { modalCrear.value = false; resetForm() },
        onError: (e) => Object.assign(errores, e),
    })
}

const actualizar = () => {
    Inertia.put(route('proveedores.update', registroActual.value.id_proveedor), form, {
        preserveScroll: true,
        onSuccess: () => { modalEditar.value = false },
        onError: (e) => Object.assign(errores, e),
    })
}

const eliminar = (id) => {
    if (confirm('¿Eliminar este proveedor?')) {
        Inertia.delete(route('proveedores.destroy', id), { preserveScroll: true })
    }
}
</script>
