<template>
    <LaraDashLayout>
        <Head title="Sucursales y Vendedores"/>

        <!-- ===== SUCURSALES ===== -->
        <div class="flex justify-between mb-2">
            <h2 class="my-3 text-2xl font-semibold text-gray-600 dark:text-gray-200">
                Sucursales
            </h2>
            <button @click="abrirModalCrearSuc" class="btn-blue my-2">+</button>
        </div>

        <div class="mb-4">
            <input
                v-model="buscarSuc"
                @keyup.enter="realizarBusquedaSuc"
                type="text"
                placeholder="Buscar sucursal..."
                class="laradash-input max-w-xs"
            />
        </div>

        <LaraDashTable class="mb-8">
            <template #col>
                <th class="px-4 py-3 text-xs">ID</th>
                <th class="px-4 py-3 text-xs">Código</th>
                <th class="px-4 py-3 text-xs">Nombre</th>
                <th class="px-4 py-3 text-xs">País</th>
                <th class="px-4 py-3 text-xs">Acción</th>
            </template>
            <template #row>
                <tr
                    v-for="s in sucursales.data"
                    :key="s.id_sucursal"
                    class="text-gray-500 hover:bg-gray-200 dark:hover:bg-gray-600 dark:text-gray-300"
                >
                    <td class="px-4 py-1 text-xs">{{ s.id_sucursal }}</td>
                    <td class="px-4 py-1 text-xs">{{ s.codigo }}</td>
                    <td class="px-4 py-1 text-xs">{{ s.nombre }}</td>
                    <td class="px-4 py-1 text-xs">{{ s.pais?.nombre }}</td>
                    <td class="px-4 py-1 text-xs flex gap-2">
                        <button @click="abrirModalEditarSuc(s)" class="hover:text-blue-600">Editar</button>
                        <button @click="eliminarSuc(s.id_sucursal)" class="hover:text-red-600">Eliminar</button>
                    </td>
                </tr>
            </template>
            <template #pagination>
                <LaraDashPagination
                    :links="sucursales.links"
                    :total="sucursales.total"
                    :to="sucursales.to"
                    :from="sucursales.from"
                />
            </template>
        </LaraDashTable>

        <!-- ===== VENDEDORES ===== -->
        <div class="flex justify-between mb-2">
            <h2 class="my-3 text-2xl font-semibold text-gray-600 dark:text-gray-200">
                Vendedores
            </h2>
            <button @click="abrirModalCrearVen" class="btn-blue my-2">+</button>
        </div>

        <div class="mb-4">
            <input
                v-model="buscarVen"
                @keyup.enter="realizarBusquedaVen"
                type="text"
                placeholder="Buscar vendedor..."
                class="laradash-input max-w-xs"
            />
        </div>

        <LaraDashTable class="mb-6">
            <template #col>
                <th class="px-4 py-3 text-xs">ID</th>
                <th class="px-4 py-3 text-xs">Código</th>
                <th class="px-4 py-3 text-xs">Nombre</th>
                <th class="px-4 py-3 text-xs">Fecha Ingreso</th>
                <th class="px-4 py-3 text-xs">N° Documento</th>
                <th class="px-4 py-3 text-xs">Sucursal</th>
                <th class="px-4 py-3 text-xs">Acción</th>
            </template>
            <template #row>
                <tr
                    v-for="v in vendedores.data"
                    :key="v.id_vendedor"
                    class="text-gray-500 hover:bg-gray-200 dark:hover:bg-gray-600 dark:text-gray-300"
                >
                    <td class="px-4 py-1 text-xs">{{ v.id_vendedor }}</td>
                    <td class="px-4 py-1 text-xs">{{ v.codigo_vendedor }}</td>
                    <td class="px-4 py-1 text-xs">{{ v.nombre_vendedor }}</td>
                    <td class="px-4 py-1 text-xs">{{ v.fecha_ingreso }}</td>
                    <td class="px-4 py-1 text-xs">{{ v.numero_documento }}</td>
                    <td class="px-4 py-1 text-xs">{{ v.sucursal?.nombre }}</td>
                    <td class="px-4 py-1 text-xs flex gap-2">
                        <button @click="abrirModalEditarVen(v)" class="hover:text-blue-600">Editar</button>
                        <button @click="eliminarVen(v.id_vendedor)" class="hover:text-red-600">Eliminar</button>
                    </td>
                </tr>
            </template>
            <template #pagination>
                <LaraDashPagination
                    :links="vendedores.links"
                    :total="vendedores.total"
                    :to="vendedores.to"
                    :from="vendedores.from"
                />
            </template>
        </LaraDashTable>

        <!-- ===== MODALES SUCURSAL ===== -->
        <JetDialogModal v-if="modalCrearSuc" :show="modalCrearSuc" @close="modalCrearSuc = false" max-width="lg">
            <template #title>Nueva Sucursal</template>
            <template #content>
                <form @submit.prevent="grabarSuc" class="flex flex-col gap-2">
                    <label class="block">
                        <span class="laradash-label">Código *</span>
                        <input v-model="formSuc.codigo" class="laradash-input" />
                        <span v-if="erroresSuc.codigo" class="text-red-500 text-xs">{{ erroresSuc.codigo }}</span>
                    </label>
                    <label class="block">
                        <span class="laradash-label">Nombre *</span>
                        <input v-model="formSuc.nombre" class="laradash-input" />
                        <span v-if="erroresSuc.nombre" class="text-red-500 text-xs">{{ erroresSuc.nombre }}</span>
                    </label>
                    <label class="block">
                        <span class="laradash-label">País *</span>
                        <select v-model="formSuc.id_pais" class="laradash-input">
                            <option value="">Seleccione...</option>
                            <option v-for="p in paises" :key="p.id_pais" :value="p.id_pais">{{ p.nombre }}</option>
                        </select>
                        <span v-if="erroresSuc.id_pais" class="text-red-500 text-xs">{{ erroresSuc.id_pais }}</span>
                    </label>
                    <button class="btn-blue mt-2">Guardar</button>
                </form>
            </template>
        </JetDialogModal>

        <JetDialogModal v-if="modalEditarSuc" :show="modalEditarSuc" @close="modalEditarSuc = false" max-width="lg">
            <template #title>Editar Sucursal</template>
            <template #content>
                <form @submit.prevent="actualizarSuc" class="flex flex-col gap-2">
                    <label class="block">
                        <span class="laradash-label">Código *</span>
                        <input v-model="formSuc.codigo" class="laradash-input" />
                        <span v-if="erroresSuc.codigo" class="text-red-500 text-xs">{{ erroresSuc.codigo }}</span>
                    </label>
                    <label class="block">
                        <span class="laradash-label">Nombre *</span>
                        <input v-model="formSuc.nombre" class="laradash-input" />
                        <span v-if="erroresSuc.nombre" class="text-red-500 text-xs">{{ erroresSuc.nombre }}</span>
                    </label>
                    <label class="block">
                        <span class="laradash-label">País *</span>
                        <select v-model="formSuc.id_pais" class="laradash-input">
                            <option value="">Seleccione...</option>
                            <option v-for="p in paises" :key="p.id_pais" :value="p.id_pais">{{ p.nombre }}</option>
                        </select>
                        <span v-if="erroresSuc.id_pais" class="text-red-500 text-xs">{{ erroresSuc.id_pais }}</span>
                    </label>
                    <button class="btn-blue mt-2">Actualizar</button>
                </form>
            </template>
        </JetDialogModal>

        <!-- ===== MODALES VENDEDOR ===== -->
        <JetDialogModal v-if="modalCrearVen" :show="modalCrearVen" @close="modalCrearVen = false" max-width="lg">
            <template #title>Nuevo Vendedor</template>
            <template #content>
                <form @submit.prevent="grabarVen" class="flex flex-col gap-2">
                    <label class="block">
                        <span class="laradash-label">Código *</span>
                        <input v-model="formVen.codigo_vendedor" class="laradash-input" />
                        <span v-if="erroresVen.codigo_vendedor" class="text-red-500 text-xs">{{ erroresVen.codigo_vendedor }}</span>
                    </label>
                    <label class="block">
                        <span class="laradash-label">Nombre *</span>
                        <input v-model="formVen.nombre_vendedor" class="laradash-input" />
                        <span v-if="erroresVen.nombre_vendedor" class="text-red-500 text-xs">{{ erroresVen.nombre_vendedor }}</span>
                    </label>
                    <label class="block">
                        <span class="laradash-label">Sucursal *</span>
                        <select v-model="formVen.id_sucursal" class="laradash-input">
                            <option value="">Seleccione...</option>
                            <option v-for="s in sucursalesSelect" :key="s.id_sucursal" :value="s.id_sucursal">{{ s.nombre }}</option>
                        </select>
                        <span v-if="erroresVen.id_sucursal" class="text-red-500 text-xs">{{ erroresVen.id_sucursal }}</span>
                    </label>
                    <label class="block">
                        <span class="laradash-label">Fecha Ingreso</span>
                        <input v-model="formVen.fecha_ingreso" type="date" class="laradash-input" />
                    </label>
                    <label class="block">
                        <span class="laradash-label">Número de Documento</span>
                        <input v-model="formVen.numero_documento" class="laradash-input" />
                    </label>
                    <button class="btn-blue mt-2">Guardar</button>
                </form>
            </template>
        </JetDialogModal>

        <JetDialogModal v-if="modalEditarVen" :show="modalEditarVen" @close="modalEditarVen = false" max-width="lg">
            <template #title>Editar Vendedor</template>
            <template #content>
                <form @submit.prevent="actualizarVen" class="flex flex-col gap-2">
                    <label class="block">
                        <span class="laradash-label">Código *</span>
                        <input v-model="formVen.codigo_vendedor" class="laradash-input" />
                        <span v-if="erroresVen.codigo_vendedor" class="text-red-500 text-xs">{{ erroresVen.codigo_vendedor }}</span>
                    </label>
                    <label class="block">
                        <span class="laradash-label">Nombre *</span>
                        <input v-model="formVen.nombre_vendedor" class="laradash-input" />
                        <span v-if="erroresVen.nombre_vendedor" class="text-red-500 text-xs">{{ erroresVen.nombre_vendedor }}</span>
                    </label>
                    <label class="block">
                        <span class="laradash-label">Sucursal *</span>
                        <select v-model="formVen.id_sucursal" class="laradash-input">
                            <option value="">Seleccione...</option>
                            <option v-for="s in sucursalesSelect" :key="s.id_sucursal" :value="s.id_sucursal">{{ s.nombre }}</option>
                        </select>
                        <span v-if="erroresVen.id_sucursal" class="text-red-500 text-xs">{{ erroresVen.id_sucursal }}</span>
                    </label>
                    <label class="block">
                        <span class="laradash-label">Fecha Ingreso</span>
                        <input v-model="formVen.fecha_ingreso" type="date" class="laradash-input" />
                    </label>
                    <label class="block">
                        <span class="laradash-label">Número de Documento</span>
                        <input v-model="formVen.numero_documento" class="laradash-input" />
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
    sucursales:       Object,
    vendedores:       Object,
    sucursalesSelect: Array,
    paises:           Array,
    filtroSucursal:   String,
    filtroVendedor:   String,
})

// Búsquedas independientes
const buscarSuc = ref(props.filtroSucursal ?? '')
const buscarVen = ref(props.filtroVendedor ?? '')

// Estados de modales
const modalCrearSuc  = ref(false)
const modalEditarSuc = ref(false)
const modalCrearVen  = ref(false)
const modalEditarVen = ref(false)
const sucursalActual = ref(null)
const vendedorActual = ref(null)

// Formularios y errores
const formSuc = reactive({ codigo: '', nombre: '', id_pais: '' })
const erroresSuc = reactive({})

const formVen = reactive({
    codigo_vendedor: '', nombre_vendedor: '', id_sucursal: '',
    fecha_ingreso: '', numero_documento: '',
})
const erroresVen = reactive({})

// --- Búsquedas ---
const realizarBusquedaSuc = () => {
    Inertia.get(route('sucursales.index'), {
        buscar_sucursal: buscarSuc.value,
        buscar_vendedor: buscarVen.value,
    }, { preserveState: true })
}

const realizarBusquedaVen = () => {
    Inertia.get(route('sucursales.index'), {
        buscar_sucursal: buscarSuc.value,
        buscar_vendedor: buscarVen.value,
    }, { preserveState: true })
}

// --- Sucursales ---
const resetFormSuc = () => {
    Object.assign(formSuc, { codigo: '', nombre: '', id_pais: '' })
    Object.keys(erroresSuc).forEach(k => delete erroresSuc[k])
}

const abrirModalCrearSuc = () => { resetFormSuc(); modalCrearSuc.value = true }

const abrirModalEditarSuc = (s) => {
    resetFormSuc()
    sucursalActual.value = s
    Object.assign(formSuc, { codigo: s.codigo, nombre: s.nombre, id_pais: s.id_pais })
    modalEditarSuc.value = true
}

const grabarSuc = () => {
    Inertia.post(route('sucursales.store'), formSuc, {
        preserveScroll: true,
        onSuccess: () => { modalCrearSuc.value = false; resetFormSuc() },
        onError: (e) => Object.assign(erroresSuc, e),
    })
}

const actualizarSuc = () => {
    Inertia.put(route('sucursales.update', sucursalActual.value.id_sucursal), formSuc, {
        preserveScroll: true,
        onSuccess: () => { modalEditarSuc.value = false },
        onError: (e) => Object.assign(erroresSuc, e),
    })
}

const eliminarSuc = (id) => {
    if (confirm('¿Eliminar esta sucursal?')) {
        Inertia.delete(route('sucursales.destroy', id), { preserveScroll: true })
    }
}

// --- Vendedores ---
const resetFormVen = () => {
    Object.assign(formVen, {
        codigo_vendedor: '', nombre_vendedor: '', id_sucursal: '',
        fecha_ingreso: '', numero_documento: '',
    })
    Object.keys(erroresVen).forEach(k => delete erroresVen[k])
}

const abrirModalCrearVen = () => { resetFormVen(); modalCrearVen.value = true }

const abrirModalEditarVen = (v) => {
    resetFormVen()
    vendedorActual.value = v
    Object.assign(formVen, {
        codigo_vendedor:  v.codigo_vendedor,
        nombre_vendedor:  v.nombre_vendedor,
        id_sucursal:      v.id_sucursal,
        fecha_ingreso:    v.fecha_ingreso ?? '',
        numero_documento: v.numero_documento ?? '',
    })
    modalEditarVen.value = true
}

const grabarVen = () => {
    Inertia.post(route('vendedores.store'), formVen, {
        preserveScroll: true,
        onSuccess: () => { modalCrearVen.value = false; resetFormVen() },
        onError: (e) => Object.assign(erroresVen, e),
    })
}

const actualizarVen = () => {
    Inertia.put(route('vendedores.update', vendedorActual.value.id_vendedor), formVen, {
        preserveScroll: true,
        onSuccess: () => { modalEditarVen.value = false },
        onError: (e) => Object.assign(erroresVen, e),
    })
}

const eliminarVen = (id) => {
    if (confirm('¿Eliminar este vendedor?')) {
        Inertia.delete(route('vendedores.destroy', id), { preserveScroll: true })
    }
}
</script>
