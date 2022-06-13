<template>
    <LaraDashLayout>
        <Head title="Informes Gerenciales"/>
        <div class="flex justify-between mb-2">
            <h2 class="my-3 text-2xl font-semibold text-gray-600 dark:text-gray-200">
                Informes Gerenciales
            </h2>
        </div>

        <div class="flex justify-items-center mb-4">
            <h4 class="laradash-label">
                Seleccione el informe a Consultar:
            </h4>
            
            <select name="select-informe" id="select-informe" class="laradash-input"  v-model="tipo_informe">
                <option value="0" class="">--</option>
                <option value="1" class="">Informe de Ventas de un período</option>
                <option value="2" class="">Informe de Ventas por Sucursal</option>
                <option value="3" class=""></option>
            </select>
        </div>

        <div class="flex justify-items-center mb-4">
            <div class="laradash-label">
                Fecha Inicio:
            </div>
           <v-date-picker v-model="date"  mode="date" :masks="masks">
            <template v-slot="{ inputValue, inputEvents }">
                <input
                class="bg-white border px-2 py-1 rounded"
                :value="inputValue"
                v-on="inputEvents"
                />
            </template>
            </v-date-picker>

            <div class="laradash-label">
                Fecha Fin:
            </div>
           <v-date-picker v-model="date2"  mode="date" :masks="masks">
            <template v-slot="{ inputValue, inputEvents }">
                <input
                class="bg-white border px-2 py-1 rounded"
                :value="inputValue"
                v-on="inputEvents"
                />
            </template>
            </v-date-picker>
            <label class="w-full text-sm mx-1">
                <button class="btn-blue !text-xs" @click.prevent="generarInforme">Generar Informe</button>
            </label>

        </div>

    </LaraDashLayout>
</template>


<script setup>
    // importaciones
    import LaraDashLayout from '@/Layouts/Laradash'
    import { Head, useForm } from '@inertiajs/inertia-vue3'
    import LaraDashTable from '@/Components/Table'
    // declaraciones
    const props = defineProps({
        sucursales: Object
    })
    const sucursales = props.sucursales
        
</script>
<script type="module">
  import { ref, toRefs } from 'vue'
  import { Inertia } from '@inertiajs/inertia'

    const date = ref(false)
    const date2 = ref(false)
    const tipo_informe = ref(false)
    const form = useForm({
        fecha_inicio: '',
        fecha_fin: '',
        informe_tipo: ''
    })
    
    

    const generarInforme = () => {
      
        const d = new Date(date.value);
        const d2 = new Date(date2.value);
        let fecha_inicio = formatDate(d); 
        let fecha_fin = formatDate(d2); 
        let tipo_reporte = tipo_informe.value;

    
          console.log(formatDate(d));
          console.log(formatDate(d2));
          console.log(tipo_informe.value);
        
        
        Inertia.post(route('informes.view_informe'),{
            fecha_inicio: fecha_inicio,
            fecha_fin: fecha_fin,
            tipo_reporte: tipo_reporte
        })
    }
  function formatDate(date) {
        var day = date.getDate();
        if (day < 10) {
            day = "0" + day;
        }
        var month = date.getMonth() + 1;
        if (month < 10) {
            month = "0" + month;
        }
        var year = date.getFullYear();
        return year + "-" + month + "-" + day;
    }
</script>

