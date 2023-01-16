<script>
export default {
    layout: AppLayout
}
</script>
<script setup>
import { array, object } from 'vue-types';
import AppLayout from '@/layouts/apps.vue';

const props = defineProps({
    filter: object(),
    query: array(),
    modules: array()
})
</script>

<template>
    <div class="mb-4 sm:mb-6 flex justify-between items-center">
        <h1 class="text-2xl md:text-3xl text-slate-800 font-bold">Admin</h1>
    </div>
</template>