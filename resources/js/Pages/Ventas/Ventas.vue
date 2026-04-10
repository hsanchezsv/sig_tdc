<template>
    <LaraDashLayout>
        <Head title="Ventas / Facturas"/>
        <div class="flex justify-between mb-2">
            <h2 class="my-3 text-2xl font-semibold text-gray-600 dark:text-gray-200">
                Ventas / Facturas
            </h2>
            <button @click="abrirModalCrear" class="btn-blue my-2">+ Nueva Factura</button>
        </div>

        <!-- Búsqueda -->
        <div class="mb-4">
            <input
                v-model="buscar"
                @keyup.enter="realizarBusqueda"
                type="text"
                placeholder="Buscar por número de factura..."
                class="laradash-input max-w-xs"
            />
        </div>

        <LaraDashTable class="max-w-6xl">
            <template #col>
                <th class="px-4 py-3 text-xs">Nº Factura</th>
                <th class="px-4 py-3 text-xs">Fecha</th>
                <th class="px-4 py-3 text-xs">Sucursal</th>
                <th class="px-4 py-3 text-xs">Vendedor</th>
                <th class="px-4 py-3 text-xs">Productos</th>
                <th class="px-4 py-3 text-xs">Total Unidades</th>
                <th class="px-4 py-3 text-xs">Total Facturado</th>
                <th class="px-4 py-3 text-xs">Acción</th>
            </template>
            <template #row>
                <tr
                    v-for="v in ventas.data"
                    :key="v.num_factura"
                    class="text-gray-500 hover:bg-gray-200 dark:hover:bg-gray-600 dark:text-gray-300"
                >
                    <td class="px-4 py-1 text-xs">{{ v.num_factura }}</td>
                    <td class="px-4 py-1 text-xs">{{ v.fecha_compra }}</td>
                    <td class="px-4 py-1 text-xs">{{ v.sucursal?.nombre }}</td>
                    <td class="px-4 py-1 text-xs">{{ v.vendedor?.nombre_vendedor }}</td>
                    <td class="px-4 py-1 text-xs">{{ v.num_productos }}</td>
                    <td class="px-4 py-1 text-xs">{{ v.total_items }}</td>
                    <td class="px-4 py-1 text-xs">{{ v.total }}</td>
                    <td class="px-4 py-1 text-xs flex gap-2">
                        <button @click="verDetalle(v.num_factura)" class="hover:text-blue-600">Detalle</button>
                        <button @click="eliminar(v.num_factura)" class="hover:text-red-600">Eliminar</button>
                    </td>
                </tr>
            </template>
            <template #pagination>
                <LaraDashPagination
                    :links="ventas.links"
                    :total="ventas.total"
                    :to="ventas.to"
                    :from="ventas.from"
                />
            </template>
        </LaraDashTable>

        <!-- Modal Nueva Factura -->
        <JetDialogModal v-if="modalCrear" :show="modalCrear" @close="modalCrear = false" max-width="2xl">
            <template #title>Nueva Factura</template>
            <template #content>
                <form @submit.prevent="grabar" class="flex flex-col gap-3">
                    <div class="grid grid-cols-2 gap-3">
                        <label class="block">
                            <span class="laradash-label">Fecha *</span>
                            <input v-model="form.fecha_compra" type="date" class="laradash-input" />
                            <span v-if="errores.fecha_compra" class="text-red-500 text-xs">{{ errores.fecha_compra }}</span>
                        </label>
                        <label class="block">
                            <span class="laradash-label">Nº Factura (opcional)</span>
                            <input v-model="form.num_factura" class="laradash-input" placeholder="Se genera automáticamente" />
                            <span v-if="errores.num_factura" class="text-red-500 text-xs">{{ errores.num_factura }}</span>
                        </label>
                        <label class="block">
                            <span class="laradash-label">Sucursal *</span>
                            <select v-model="form.id_sucursal" class="laradash-input">
                                <option value="">Seleccione...</option>
                                <option v-for="s in sucursales" :key="s.id_sucursal" :value="s.id_sucursal">{{ s.nombre }}</option>
                            </select>
                            <span v-if="errores.id_sucursal" class="text-red-500 text-xs">{{ errores.id_sucursal }}</span>
                        </label>
                        <label class="block">
                            <span class="laradash-label">Vendedor *</span>
                            <select v-model="form.id_vendedor" class="laradash-input">
                                <option value="">Seleccione...</option>
                                <option
                                    v-for="vend in vendedoresFiltrados"
                                    :key="vend.id_vendedor"
                                    :value="vend.id_vendedor"
                                >{{ vend.nombre_vendedor }}</option>
                            </select>
                            <span v-if="errores.id_vendedor" class="text-red-500 text-xs">{{ errores.id_vendedor }}</span>
                        </label>
                    </div>

                    <!-- Items de la factura -->
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="laradash-label">Items *</span>
                            <button type="button" @click="agregarItem" class="btn-blue text-xs px-2 py-1">+ Agregar producto</button>
                        </div>
                        <span v-if="errores.items" class="text-red-500 text-xs">{{ errores.items }}</span>
                        <div
                            v-for="(item, idx) in form.items"
                            :key="idx"
                            class="grid grid-cols-4 gap-2 mb-2 items-end"
                        >
                            <label class="block col-span-2">
                                <span class="laradash-label text-xs">Producto</span>
                                <select v-model="item.id_producto" @change="autocompletarPrecio(item)" class="laradash-input">
                                    <option value="">Seleccione...</option>
                                    <option v-for="p in productos" :key="p.id_producto" :value="p.id_producto">
                                        {{ p.codigo_producto }} - {{ p.nombre_producto }}
                                    </option>
                                </select>
                                <span v-if="errores[`items.${idx}.id_producto`]" class="text-red-500 text-xs">
                                    {{ errores[`items.${idx}.id_producto`] }}
                                </span>
                            </label>
                            <label class="block">
                                <span class="laradash-label text-xs">Cantidad</span>
                                <input v-model.number="item.cantidad" type="number" min="1" class="laradash-input" />
                                <span v-if="errores[`items.${idx}.cantidad`]" class="text-red-500 text-xs">
                                    {{ errores[`items.${idx}.cantidad`] }}
                                </span>
                            </label>
                            <label class="block">
                                <span class="laradash-label text-xs">Precio Unitario</span>
                                <div class="flex gap-1">
                                    <input v-model.number="item.precio_unidad" type="number" step="0.01" min="0" class="laradash-input" />
                                    <button
                                        v-if="form.items.length > 1"
                                        type="button"
                                        @click="quitarItem(idx)"
                                        class="text-red-500 hover:text-red-700 text-lg leading-none"
                                    >&times;</button>
                                </div>
                                <span v-if="errores[`items.${idx}.precio_unidad`]" class="text-red-500 text-xs">
                                    {{ errores[`items.${idx}.precio_unidad`] }}
                                </span>
                            </label>
                        </div>
                    </div>

                    <button class="btn-blue mt-2">Emitir Factura</button>
                </form>
            </template>
        </JetDialogModal>

        <!-- Modal Detalle -->
        <JetDialogModal v-if="modalDetalle" :show="modalDetalle" @close="modalDetalle = false" max-width="2xl">
            <template #title>Detalle Factura {{ facturaDetalle }}</template>
            <template #content>
                <table class="w-full text-xs text-gray-600 dark:text-gray-300">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-gray-700">
                            <th class="px-3 py-2 text-left">Producto</th>
                            <th class="px-3 py-2 text-right">Cantidad</th>
                            <th class="px-3 py-2 text-right">Monto</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in itemsDetalle" :key="item.id_venta" class="border-b dark:border-gray-600">
                            <td class="px-3 py-1">{{ item.producto?.nombre_producto }}</td>
                            <td class="px-3 py-1 text-right">{{ item.cantidad }}</td>
                            <td class="px-3 py-1 text-right">{{ item.monto_facturado }}</td>
                        </tr>
                    </tbody>
                </table>
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
import { ref, reactive, computed } from 'vue'
import { Inertia } from '@inertiajs/inertia'

const props = defineProps({
    ventas:     Object,
    sucursales: Array,
    vendedores: Array,
    productos:  Array,
    filtro:     String,
})

const buscar       = ref(props.filtro ?? '')
const modalCrear   = ref(false)
const modalDetalle = ref(false)
const facturaDetalle = ref('')
const itemsDetalle   = ref([])
const errores = reactive({})

const itemVacio = () => ({ id_producto: '', cantidad: 1, precio_unidad: 0 })

const form = reactive({
    fecha_compra: '',
    num_factura:  '',
    id_sucursal:  '',
    id_vendedor:  '',
    items: [itemVacio()],
})

const vendedoresFiltrados = computed(() => {
    if (!form.id_sucursal) return props.vendedores
    return props.vendedores.filter(v => v.id_sucursal === form.id_sucursal)
})

const resetForm = () => {
    Object.assign(form, {
        fecha_compra: '',
        num_factura:  '',
        id_sucursal:  '',
        id_vendedor:  '',
        items: [itemVacio()],
    })
    Object.keys(errores).forEach(k => delete errores[k])
}

const agregarItem = () => {
    form.items.push(itemVacio())
}

const quitarItem = (idx) => {
    form.items.splice(idx, 1)
}

const autocompletarPrecio = (item) => {
    const producto = props.productos.find(p => p.id_producto === item.id_producto)
    if (producto) item.precio_unidad = producto.precio_unidad ?? 0
}

const realizarBusqueda = () => {
    Inertia.get(route('ventas.index'), { buscar: buscar.value }, { preserveState: true })
}

const abrirModalCrear = () => {
    resetForm()
    modalCrear.value = true
}

const grabar = () => {
    Inertia.post(route('ventas.store'), form, {
        preserveScroll: true,
        onSuccess: () => { modalCrear.value = false; resetForm() },
        onError: (e) => Object.assign(errores, e),
    })
}

const eliminar = (numFactura) => {
    if (confirm(`¿Eliminar la factura ${numFactura} y todos sus items?`)) {
        Inertia.delete(route('ventas.destroy', numFactura), { preserveScroll: true })
    }
}

const verDetalle = async (numFactura) => {
    facturaDetalle.value = numFactura
    itemsDetalle.value   = []
    modalDetalle.value   = true

    const response = await fetch(route('ventas.show', numFactura))
    itemsDetalle.value = await response.json()
}
</script>
