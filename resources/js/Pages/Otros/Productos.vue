<template>
    <LaraDashLayout>
        <Head title="Productos"/>
        <div class="flex justify-between mb-2">
            <h2 class="my-3 text-2xl font-semibold text-gray-600 dark:text-gray-200">
                Productos
            </h2>
            <button @click="abrirModalCrear" class="btn-blue my-2">+</button>
        </div>

        <!-- Búsqueda -->
        <div class="mb-4">
            <input
                v-model="buscar"
                @keyup.enter="realizarBusqueda"
                type="text"
                placeholder="Buscar por nombre o código..."
                class="laradash-input max-w-xs"
            />
        </div>

        <LaraDashTable class="max-w-6xl">
            <template #col>
                <th class="px-4 py-3 text-xs">ID</th>
                <th class="px-4 py-3 text-xs">Código</th>
                <th class="px-4 py-3 text-xs">Nombre</th>
                <th class="px-4 py-3 text-xs">Proveedor</th>
                <th class="px-4 py-3 text-xs">País Origen</th>
                <th class="px-4 py-3 text-xs">Precio Unitario</th>
                <th class="px-4 py-3 text-xs">Fecha Compra</th>
                <th class="px-4 py-3 text-xs">Lote #</th>
                <th class="px-4 py-3 text-xs">Acción</th>
            </template>
            <template #row>
                <tr
                    v-for="p in productos.data"
                    :key="p.id_producto"
                    class="text-gray-500 hover:bg-gray-200 dark:hover:bg-gray-600 dark:text-gray-300"
                >
                    <td class="px-4 py-1 text-xs">{{ p.id_producto }}</td>
                    <td class="px-4 py-1 text-xs">{{ p.codigo_producto }}</td>
                    <td class="px-4 py-1 text-xs">{{ p.nombre_producto }}</td>
                    <td class="px-4 py-1 text-xs">{{ p.proveedor?.nombre_proveedor }}</td>
                    <td class="px-4 py-1 text-xs">{{ p.pais?.nombre }}</td>
                    <td class="px-4 py-1 text-xs">{{ p.precio_unidad }}</td>
                    <td class="px-4 py-1 text-xs">{{ p.fecha_compra }}</td>
                    <td class="px-4 py-1 text-xs">{{ p.lote_numero }}</td>
                    <td class="px-4 py-1 text-xs flex gap-2">
                        <button @click="abrirModalEditar(p)" class="hover:text-blue-600">Editar</button>
                        <button @click="eliminar(p.id_producto)" class="hover:text-red-600">Eliminar</button>
                    </td>
                </tr>
            </template>
            <template #pagination>
                <LaraDashPagination
                    :links="productos.links"
                    :total="productos.total"
                    :to="productos.to"
                    :from="productos.from"
                />
            </template>
        </LaraDashTable>

        <!-- Modal Crear -->
        <JetDialogModal v-if="modalCrear" :show="modalCrear" @close="modalCrear = false" max-width="xl">
            <template #title>Nuevo Producto</template>
            <template #content>
                <form @submit.prevent="grabar" class="flex flex-col gap-2">
                    <label class="block">
                        <span class="laradash-label">Código *</span>
                        <input v-model="form.codigo_producto" class="laradash-input" />
                        <span v-if="errores.codigo_producto" class="text-red-500 text-xs">{{ errores.codigo_producto }}</span>
                    </label>
                    <label class="block">
                        <span class="laradash-label">Nombre *</span>
                        <input v-model="form.nombre_producto" class="laradash-input" />
                        <span v-if="errores.nombre_producto" class="text-red-500 text-xs">{{ errores.nombre_producto }}</span>
                    </label>
                    <label class="block">
                        <span class="laradash-label">Proveedor *</span>
                        <select v-model="form.id_proveedor" class="laradash-input">
                            <option value="">Seleccione...</option>
                            <option v-for="pv in proveedores" :key="pv.id_proveedor" :value="pv.id_proveedor">{{ pv.nombre_proveedor }}</option>
                        </select>
                        <span v-if="errores.id_proveedor" class="text-red-500 text-xs">{{ errores.id_proveedor }}</span>
                    </label>
                    <label class="block">
                        <span class="laradash-label">País de Origen *</span>
                        <select v-model="form.id_pais" class="laradash-input">
                            <option value="">Seleccione...</option>
                            <option v-for="p in paises" :key="p.id_pais" :value="p.id_pais">{{ p.nombre }}</option>
                        </select>
                        <span v-if="errores.id_pais" class="text-red-500 text-xs">{{ errores.id_pais }}</span>
                    </label>
                    <label class="block">
                        <span class="laradash-label">Precio Unitario</span>
                        <input v-model="form.precio_unidad" type="number" step="0.01" class="laradash-input" />
                    </label>
                    <label class="block">
                        <span class="laradash-label">Fecha Compra</span>
                        <input v-model="form.fecha_compra" type="date" class="laradash-input" />
                    </label>
                    <label class="block">
                        <span class="laradash-label">Número de Lote</span>
                        <input v-model="form.lote_numero" class="laradash-input" />
                    </label>
                    <button class="btn-blue mt-2">Guardar</button>
                </form>
            </template>
        </JetDialogModal>

        <!-- Modal Editar -->
        <JetDialogModal v-if="modalEditar" :show="modalEditar" @close="modalEditar = false" max-width="xl">
            <template #title>Editar Producto</template>
            <template #content>
                <form @submit.prevent="actualizar" class="flex flex-col gap-2">
                    <label class="block">
                        <span class="laradash-label">Código *</span>
                        <input v-model="form.codigo_producto" class="laradash-input" />
                        <span v-if="errores.codigo_producto" class="text-red-500 text-xs">{{ errores.codigo_producto }}</span>
                    </label>
                    <label class="block">
                        <span class="laradash-label">Nombre *</span>
                        <input v-model="form.nombre_producto" class="laradash-input" />
                        <span v-if="errores.nombre_producto" class="text-red-500 text-xs">{{ errores.nombre_producto }}</span>
                    </label>
                    <label class="block">
                        <span class="laradash-label">Proveedor *</span>
                        <select v-model="form.id_proveedor" class="laradash-input">
                            <option value="">Seleccione...</option>
                            <option v-for="pv in proveedores" :key="pv.id_proveedor" :value="pv.id_proveedor">{{ pv.nombre_proveedor }}</option>
                        </select>
                        <span v-if="errores.id_proveedor" class="text-red-500 text-xs">{{ errores.id_proveedor }}</span>
                    </label>
                    <label class="block">
                        <span class="laradash-label">País de Origen *</span>
                        <select v-model="form.id_pais" class="laradash-input">
                            <option value="">Seleccione...</option>
                            <option v-for="p in paises" :key="p.id_pais" :value="p.id_pais">{{ p.nombre }}</option>
                        </select>
                        <span v-if="errores.id_pais" class="text-red-500 text-xs">{{ errores.id_pais }}</span>
                    </label>
                    <label class="block">
                        <span class="laradash-label">Precio Unitario</span>
                        <input v-model="form.precio_unidad" type="number" step="0.01" class="laradash-input" />
                    </label>
                    <label class="block">
                        <span class="laradash-label">Fecha Compra</span>
                        <input v-model="form.fecha_compra" type="date" class="laradash-input" />
                    </label>
                    <label class="block">
                        <span class="laradash-label">Número de Lote</span>
                        <input v-model="form.lote_numero" class="laradash-input" />
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
    productos:   Object,
    paises:      Array,
    proveedores: Array,
    filtro:      String,
})

const buscar      = ref(props.filtro ?? '')
const modalCrear  = ref(false)
const modalEditar = ref(false)
const registroActual = ref(null)
const errores = reactive({})

const form = reactive({
    codigo_producto: '',
    nombre_producto: '',
    id_proveedor:    '',
    id_pais:         '',
    precio_unidad:   '',
    fecha_compra:    '',
    lote_numero:     '',
})

const resetForm = () => {
    Object.assign(form, {
        codigo_producto: '', nombre_producto: '', id_proveedor: '',
        id_pais: '', precio_unidad: '', fecha_compra: '', lote_numero: '',
    })
    Object.keys(errores).forEach(k => delete errores[k])
}

const realizarBusqueda = () => {
    Inertia.get(route('productos.index'), { buscar: buscar.value }, { preserveState: true })
}

const abrirModalCrear = () => {
    resetForm()
    modalCrear.value = true
}

const abrirModalEditar = (producto) => {
    resetForm()
    registroActual.value = producto
    Object.assign(form, {
        codigo_producto: producto.codigo_producto,
        nombre_producto: producto.nombre_producto,
        id_proveedor:    producto.id_proveedor,
        id_pais:         producto.id_pais,
        precio_unidad:   producto.precio_unidad ?? '',
        fecha_compra:    producto.fecha_compra ?? '',
        lote_numero:     producto.lote_numero ?? '',
    })
    modalEditar.value = true
}

const grabar = () => {
    Inertia.post(route('productos.store'), form, {
        preserveScroll: true,
        onSuccess: () => { modalCrear.value = false; resetForm() },
        onError: (e) => Object.assign(errores, e),
    })
}

const actualizar = () => {
    Inertia.put(route('productos.update', registroActual.value.id_producto), form, {
        preserveScroll: true,
        onSuccess: () => { modalEditar.value = false },
        onError: (e) => Object.assign(errores, e),
    })
}

const eliminar = (id) => {
    if (confirm('¿Eliminar este producto?')) {
        Inertia.delete(route('productos.destroy', id), { preserveScroll: true })
    }
}
</script>
