<template>
    <Link :href="'/'">
        <img src="https://cdn-icons-png.flaticon.com/512/2720/2720776.png" class="rounded-full w-28 mx-auto"/>
    </Link>
</template>

<script>
    import { defineComponent } from 'vue'
    import { Link } from '@inertiajs/inertia-vue3';

    export default defineComponent({
        components: {
            Link,
        },
    })
</script>
