<template>
    <LaraDashLayout>
        <Head title="Informes Gerenciales"/>
        <div class="flex justify-between mb-2">
            <h2 class="my-3 text-2xl font-semibold text-gray-600 dark:text-gray-200">
                Informes Gerenciales
            </h2>
        </div>
        <div class="shadow-lg rounded-2xl p-4 bg-white dark:bg-gray-800 sm:block my-4 mb-4 mx-4">
            <div class="flex justify-items-center mb-4">
                <h4 class="laradash-label">
                    Seleccione el informe a Consultar:
                </h4>
                
                <select name="select-informe" id="select-informe" class="laradash-input"  v-model="tipo_informe">
                    <option value="0" class="">--</option>
                    <option value="1" class="">Informe de Ventas de un período</option>
                    <option value="2" class="">Productos más vendidos por Zona</option>
                    <option value="3" class="">Análisis de Promociones y Productos vendidos</option>
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
                    <button class="btn-blue !text-xs" @click.prevent="generarInforme">Generar Informe</button>&nbsp;
                    <button class="btn-blue !text-xs" @click="exportToPDF()">Generar PDF</button>
                </label>

            </div>
        </div>
            
    <div id="app" ref="document">
        <div class="text-center">
            <h2 class="my-3 text-2xl font-semibold text-gray-600 dark:text-gray-200">
                Informes de Ventas del Periodo
            </h2>
            <h3 class="my-2 text-1xl font-semibold text-gray-600 dark:text-gray-200">
                    Periodo  {{ periodo_inicio }} - {{ periodo_fin }}
            </h3>
        </div>


        <div class="flex flex-col flex-wrap sm:flex-row text-center w-full">
            <div class="shadow-lg rounded-2xl p-4 bg-white dark:bg-gray-800 my-4 mb-4 mx-4">
                <div class="flex items-center">
                    <span class="rounded-xl relative p-4 bg-purple-200">
                        <svg width="40" fill="currentColor" height="40" class="text-purple-500 h-4 absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg">
                            <path d="M1362 1185q0 153-99.5 263.5t-258.5 136.5v175q0 14-9 23t-23 9h-135q-13 0-22.5-9.5t-9.5-22.5v-175q-66-9-127.5-31t-101.5-44.5-74-48-46.5-37.5-17.5-18q-17-21-2-41l103-135q7-10 23-12 15-2 24 9l2 2q113 99 243 125 37 8 74 8 81 0 142.5-43t61.5-122q0-28-15-53t-33.5-42-58.5-37.5-66-32-80-32.5q-39-16-61.5-25t-61.5-26.5-62.5-31-56.5-35.5-53.5-42.5-43.5-49-35.5-58-21-66.5-8.5-78q0-138 98-242t255-134v-180q0-13 9.5-22.5t22.5-9.5h135q14 0 23 9t9 23v176q57 6 110.5 23t87 33.5 63.5 37.5 39 29 15 14q17 18 5 38l-81 146q-8 15-23 16-14 3-27-7-3-3-14.5-12t-39-26.5-58.5-32-74.5-26-85.5-11.5q-95 0-155 43t-60 111q0 26 8.5 48t29.5 41.5 39.5 33 56 31 60.5 27 70 27.5q53 20 81 31.5t76 35 75.5 42.5 62 50 53 63.5 31.5 76.5 13 94z">
                            </path>
                        </svg>
                    </span>
                    <p class="text-md text-black dark:text-white ml-2">
                        Venta Total
                    </p>
                </div>
                <div class="flex flex-col justify-start">
                    <p class="text-gray-700 dark:text-gray-100 text-4xl text-left font-bold my-4">
                        {{ formatter.format(total_facturado[0].total_facturado) }}
                        <span class="text-sm">
                        
                        </span>
                    </p>
                    <div class="flex items-center text-green-500 text-sm">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg">
                            <path d="M1408 1216q0 26-19 45t-45 19h-896q-26 0-45-19t-19-45 19-45l448-448q19-19 45-19t45 19l448 448q19 19 19 45z">
                            </path>
                        </svg>
                        <span>
                           
                        </span>
                        <span class="text-gray-400">
                          
                        </span>
                    </div>
                </div>
            </div>

            <div class="shadow-lg rounded-2xl p-4 bg-white dark:bg-gray-800 sm:block my-4 mb-4 mx-4">
                <div class="flex items-center">
                    <span class="rounded-xl relative p-4 bg-purple-200">
                        <svg width="40" fill="currentColor" height="40" class="text-purple-500 h-4 absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg">
                            <path d="M1362 1185q0 153-99.5 263.5t-258.5 136.5v175q0 14-9 23t-23 9h-135q-13 0-22.5-9.5t-9.5-22.5v-175q-66-9-127.5-31t-101.5-44.5-74-48-46.5-37.5-17.5-18q-17-21-2-41l103-135q7-10 23-12 15-2 24 9l2 2q113 99 243 125 37 8 74 8 81 0 142.5-43t61.5-122q0-28-15-53t-33.5-42-58.5-37.5-66-32-80-32.5q-39-16-61.5-25t-61.5-26.5-62.5-31-56.5-35.5-53.5-42.5-43.5-49-35.5-58-21-66.5-8.5-78q0-138 98-242t255-134v-180q0-13 9.5-22.5t22.5-9.5h135q14 0 23 9t9 23v176q57 6 110.5 23t87 33.5 63.5 37.5 39 29 15 14q17 18 5 38l-81 146q-8 15-23 16-14 3-27-7-3-3-14.5-12t-39-26.5-58.5-32-74.5-26-85.5-11.5q-95 0-155 43t-60 111q0 26 8.5 48t29.5 41.5 39.5 33 56 31 60.5 27 70 27.5q53 20 81 31.5t76 35 75.5 42.5 62 50 53 63.5 31.5 76.5 13 94z">
                            </path>
                        </svg>
                    </span>
                    <p class="text-md text-black dark:text-white ml-2">
                        Venta Promedio
                    </p>
                </div>
                <div class="flex flex-col justify-start">
                    <p class="text-gray-700 dark:text-gray-100 text-4xl text-left font-bold my-4">
                        {{ formatter.format((total_facturado[0].promedio_ventas)) }}
                        <span class="text-sm">
                        
                        </span>
                    </p>
                    <div class="flex items-center text-green-500 text-sm">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg">
                            <path d="M1408 1216q0 26-19 45t-45 19h-896q-26 0-45-19t-19-45 19-45l448-448q19-19 45-19t45 19l448 448q19 19 19 45z">
                            </path>
                        </svg>
                        <span>
                          
                        </span>
                        <span class="text-gray-400">
                         
                        </span>
                    </div>
                </div>
            </div>

            <div class="shadow-lg rounded-2xl p-4 bg-white dark:bg-gray-800 sm:block my-4 mb-4 mx-4">
                <div class="flex items-center">
                    <span class="rounded-xl relative p-4 bg-purple-200">
                        <svg width="40" fill="currentColor" height="40" class="text-purple-500 h-4 absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg">
                            <path d="M1362 1185q0 153-99.5 263.5t-258.5 136.5v175q0 14-9 23t-23 9h-135q-13 0-22.5-9.5t-9.5-22.5v-175q-66-9-127.5-31t-101.5-44.5-74-48-46.5-37.5-17.5-18q-17-21-2-41l103-135q7-10 23-12 15-2 24 9l2 2q113 99 243 125 37 8 74 8 81 0 142.5-43t61.5-122q0-28-15-53t-33.5-42-58.5-37.5-66-32-80-32.5q-39-16-61.5-25t-61.5-26.5-62.5-31-56.5-35.5-53.5-42.5-43.5-49-35.5-58-21-66.5-8.5-78q0-138 98-242t255-134v-180q0-13 9.5-22.5t22.5-9.5h135q14 0 23 9t9 23v176q57 6 110.5 23t87 33.5 63.5 37.5 39 29 15 14q17 18 5 38l-81 146q-8 15-23 16-14 3-27-7-3-3-14.5-12t-39-26.5-58.5-32-74.5-26-85.5-11.5q-95 0-155 43t-60 111q0 26 8.5 48t29.5 41.5 39.5 33 56 31 60.5 27 70 27.5q53 20 81 31.5t76 35 75.5 42.5 62 50 53 63.5 31.5 76.5 13 94z">
                            </path>
                        </svg>
                    </span>
                    <p class="text-md text-black dark:text-white ml-2">
                        Total de Transacciones
                    </p>
                </div>
                <div class="flex flex-col justify-start">
                    <p class="text-gray-700 dark:text-gray-100 text-4xl text-left font-bold my-4">
                        {{ total_facturado[0].total_num_ventas }}
                        <span class="text-sm">
                        trx
                        </span>
                    </p>
                    <div class="flex items-center text-green-500 text-sm">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg">
                            <path d="M1408 1216q0 26-19 45t-45 19h-896q-26 0-45-19t-19-45 19-45l448-448q19-19 45-19t45 19l448 448q19 19 19 45z">
                            </path>
                        </svg>
                        <span>
                       
                        </span>
                        <span class="text-gray-400">
           
                        </span>
                    </div>
                </div>
            </div>

        </div>
      
      <div class="shadow-lg rounded-2xl p-4 bg-white dark:bg-gray-800 sm:block my-4 mb-4 mx-4">
        <LaraDashTable class="max-w-6xl">
            <template #col>
                <th class="px-4 py-3 text-xs">Codigo Vendedor</th>
                <th class="px-4 py-3 text-xs">Vendedor</th>
                <th class="px-4 py-3 text-xs">Numero de Ventas</th>
                <th class="px-4 py-3 text-xs">Facturacion</th>
            </template>
            <template #row>
                <tr class="text-gray-500 hover:bg-gray-200 dark:hover:bg-gray-600 dark:text-gray-300" v-for="p in vendedores" :key="p.id">
                    <td class="px-4 py-1 text-xs">{{ p.codigo_vendedor }}</td>
                    <td class="px-4 py-1 text-xs">{{ p.nombre_vendedor }}</td>
                    <td class="px-4 py-1 text-xs">{{ p.num_ventas }}</td>
                    <td class="px-4 py-1 text-xs">{{ formatter.format(p.venta_vendedor) }}</td>
                </tr>
            </template>
        </LaraDashTable>
      </div>
        
        <div class="shadow-lg rounded-2xl p-4 bg-white dark:bg-gray-800 sm:block my-4 mb-4 mx-4">
            <div class="justify-items-center">
                <div>
                    <highcharts :options="chartOptions" :callback="myCallback"></highcharts>
                </div>
            </div>
        </div>
        
    </div>
    </LaraDashLayout>
</template>


<script setup>
    // importaciones
    import LaraDashLayout from '@/Layouts/Laradash'
    import { Head, useForm } from '@inertiajs/inertia-vue3'
    import LaraDashTable from '@/Components/Table'
    import SucursalesChart from "@/Components/SucursalesChart";

    const props = defineProps({
        vendedores: Object,
        periodo_inicio: Object,
        periodo_fin: Object,
        total_facturado: Object,
        meses: Object,
        ultimos_6_meses: Object,
    })

    const vendedores = props.vendedores
    const total_facturado = props.total_facturado
    const meses = props.meses
    const ultimos_6_meses = props.ultimos_6_meses

        
</script>

<script type="module">
  import { ref, toRefs } from 'vue'
  import { Inertia } from '@inertiajs/inertia'
  import html2pdf from 'html2pdf.js'

    const date = ref(false)
    const date2 = ref(false)
    const tipo_informe = ref(false)
    const form = useForm({
        fecha_inicio: '',
        fecha_fin: '',
        informe_tipo: ''
    })

    const meses = ref(false)
    const ultimos_6_meses = ref(false)
    
    var formatter = new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    });

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

    
   export default {
        name: "app",
        data() {
            return {
            chartOptions: {
                    chart: {
                type: 'area'
            },
            accessibility: {
                description: 'Image description: An area chart compares the nuclear stockpiles of the USA and the USSR/Russia between 1945 and 2017. The number of nuclear weapons is plotted on the Y-axis and the years on the X-axis. The chart is interactive, and the year-on-year stockpile levels can be traced for each country. The US has a stockpile of 6 nuclear weapons at the dawn of the nuclear age in 1945. This number has gradually increased to 369 by 1950 when the USSR enters the arms race with 6 weapons. At this point, the US starts to rapidly build its stockpile culminating in 32,040 warheads by 1966 compared to the USSR’s 7,089. From this peak in 1966, the US stockpile gradually decreases as the USSR’s stockpile expands. By 1978 the USSR has closed the nuclear gap at 25,393. The USSR stockpile continues to grow until it reaches a peak of 45,000 in 1986 compared to the US arsenal of 24,401. From 1986, the nuclear stockpiles of both countries start to fall. By 2000, the numbers have fallen to 10,577 and 21,000 for the US and Russia, respectively. The decreases continue until 2017 at which point the US holds 4,018 weapons compared to Russia’s 4,500.'
            },
            title: {
                text: 'Ventas de ultimos 6 meses - Empresa SkyHigh'
            },
            subtitle: {
                text: 'Periodo ultimos 6 meses'
            },
            xAxis: {
                categories: [
                    'Jan',
                    'Feb',
                    'Mar',
                    'Apr',
                    'May',
                    'Jun'
                ],
            },
            yAxis: {
                title: {
                    text: 'Monto vendido en $'
                },

            },
            tooltip: {
                pointFormat: '{series.name}'
            },
            plotOptions: {
                area: {
                    marker: {
                        enabled: true,
                        symbol: 'circle',
                        radius: 2,
                        states: {
                            hover: {
                                enabled: true
                            }
                        },
                        dataLabels: {
                        enabled: true
                    },
                    },
                    dataLabels: {
                        enabled: true
                    },
                }
            },
            series: [{
                name: 'Ventas por mes',
                data: [3963, 9511, 3682, 6246, 6406, 2194 ]
            }],
                responsive: {
                rules: [
                    {
                    condition: {
                        maxWidth: 500
                    },
                    chartOptions: {
                        legend: {
                        layout: "horizontal",
                        align: "center",
                        verticalAlign: "bottom"
                        }
                    }
                    }
                ]
                }
            }
            };
        },
        mounted() {},
        methods: {
            myCallback() {
            console.log("this is callback function");
            },
            exportToPDF () {
				html2pdf(this.$refs.document, {
					margin: 1,
					filename: 'InformedeVentas.pdf',
					image: { type: 'jpeg', quality: 1 },
					html2canvas: { dpi: 300, letterRendering: true },
					jsPDF: { unit: 'in', format: 'letter', orientation: 'landscape' }
				})
			}
        }
    };

    
</script>



<style>
.highcharts-container {
width: 600px;
height: 400px;
border: 1px solid #ddd;
margin: auto;
}
</style>
<style>
	#app {
		font-family: 'Avenir', Helvetica, Arial, sans-serif;
		-webkit-font-smoothing: antialiased;
		-moz-osx-font-smoothing: grayscale;
		color: #2c3e50;
		margin-top: 20px;
	}
</style>
