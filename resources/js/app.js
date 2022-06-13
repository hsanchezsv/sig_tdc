require('./bootstrap');

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/inertia-vue3';
import { InertiaProgress } from '@inertiajs/progress';
import VueGates from 'vue-gates';
import Permissions from './Plugins/Permissions';
import Highchart from "highcharts/highcharts";
import HighchartsVue from "highcharts-vue";
import stockInit from "highcharts/modules/stock";
import 'v-calendar/dist/style.css';
import VCalendar from 'v-calendar';

stockInit(Highchart);

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'SIG-TDC';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => require(`./Pages/${name}.vue`),
    setup({ el, app, props, plugin }) {
        return createApp({ render: () => h(app, props) })
            .use(plugin)
            .use(VueGates)
            .use(Permissions)
            .use(HighchartsVue)
            .use(VCalendar, {})
            .mixin({ methods: { route } })
            .mount(el);
    },
});

InertiaProgress.init({ color: '#2563EB' });
