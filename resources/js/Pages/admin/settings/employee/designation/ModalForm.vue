<script setup>
import axios from "axios";
import dayjs from "dayjs"
import { ref } from "vue";
import { bool, object } from "vue-types";
import { notify } from "notiwind";
import VDialog from '@/components/VDialog/index.vue';
import VButton from '@/components/VButton/index.vue';
import VInput from '@/components/VInput/index.vue';
import VSwitch from '@/components/VSwitch/index.vue';

const props = defineProps({
    openDialog: bool(),
    updateAction: bool().def(false),
    data: object().def({})
})

const emit = defineEmits(['close', 'successSubmit'])

const isLoading = ref(false);
const formError = ref({})
const form = ref({})

const openForm = () => {
    if (props.updateAction) {
        form.value = Object.assign(form.value, props.data)
        handleDate()
    } else {
        form.value = ref({})
    }
}

const closeForm = () => {
    form.value = ref({})
    formError.value = ref({})
}

const handleDate = () => {
    if (form.value.date) {
        formError.value.active_month = ''
        const date = new Date(form.value.date.year, form.value.date.month, 1);
        const startDate = dayjs(date).format('YYYY-MM-DD');
        form.value.active_month = startDate
    }
}

const submit = async () => {
    props.updateAction ? updateDesignation() : createDesignation()
}

const createDesignation = async () => {
    isLoading.value = true
    axios.post(route('settings.employee.designation.create'), form.value)
        .then((res) => {
            emit('close')
            emit('successSubmit')
            form.value = ref({})
            notify({
                type: "success",
                group: "top",
                text: res.data.meta.message
            }, 2500)
        }).catch((res) => {
            // Handle validation errors
            const result = res.response.data
            if (result.hasOwnProperty('errors')) {
                formError.value = ref({});
                Object.keys(result.errors).map((key) => {
                    formError.value[key] = result.errors[key].toString();
                });
            }

            notify({
                type: "error",
                group: "top",
                text: result.message
            }, 2500)
        }).finally(() => isLoading.value = false)
}

const updateDesignation = async () => {
    isLoading.value = true
    axios.put(route('settings.employee.designation.update', { 'id': props.data.id }), form.value)
        .then((res) => {
            emit('close')
            emit('successSubmit')
            form.value = ref({})
            notify({
                type: "success",
                group: "top",
                text: res.data.meta.message
            }, 2500)
        }).catch((res) => {
            // Handle validation errors
            const result = res.response.data
            if (result.hasOwnProperty('errors')) {
                formError.value = ref({});
                Object.keys(result.errors).map((key) => {
                    formError.value[key] = result.errors[key].toString();
                });
            }

            notify({
                type: "error",
                group: "top",
                text: result.message
            }, 2500)
        }).finally(() => isLoading.value = false)
}
</script>

<template>
    <VDialog :showModal="openDialog" :title="updateAction ? 'Update Designation' : 'Add Designation'" @opened="openForm"
        @closed="closeForm" size="lg">
        <template v-slot:close>
            <button class="text-slate-400 hover:text-slate-500" @click="$emit('close')">
                <div class="sr-only">Close</div>
                <svg class="w-4 h-4 fill-current">
                    <path
                        d="M7.95 6.536l4.242-4.243a1 1 0 111.415 1.414L9.364 7.95l4.243 4.242a1 1 0 11-1.415 1.415L7.95 9.364l-4.243 4.243a1 1 0 01-1.414-1.415L6.536 7.95 2.293 3.707a1 1 0 011.414-1.414L7.95 6.536z" />
                </svg>
            </button>
        </template>
        <template v-slot:content>
            <div class="grid grid-cols-2 gap-3">
                <div class="col-span-2">
                    <VInput placeholder="Input Designation Name" label="Designation Name" :required="true"
                        v-model="form.name" :errorMessage="formError.name" @update:modelValue="formError.name = ''" />
                </div>
            </div>
        </template>
        <template v-slot:footer>
            <div class="flex flex-wrap justify-end space-x-2">
                <VButton label="Cancel" type="default" @click="$emit('close')" />
                <VButton :is-loading="isLoading" :label="updateAction ? 'Update' : 'Create'" type="primary"
                    @click="submit" />
            </div>
        </template>
    </VDialog>
</template>

<style>
.dp__select {
    color: #4F8CF6 !important;
}

.date_error {
    --dp-border-color: #dc3545 !important;
}
</style>