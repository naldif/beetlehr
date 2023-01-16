<script setup>
import axios from "axios";
import { ref } from "vue";
import { bool, object } from "vue-types";
import { notify } from "notiwind";
import VDialog from '@/components/VDialog/index.vue';
import VButton from '@/components/VButton/index.vue';
import VInput from '@/components/VInput/index.vue';
import dayjs from "dayjs";

const props = defineProps({
    openDialog: bool(),
    updateAction: bool().def(false),
    data: object().def({}),
    additional: object().def({})
})

const emit = defineEmits(['close', 'successSubmit'])

const isLoading = ref(false);
const formError = ref({})
const form = ref({})

const openForm = () => {
    if (props.updateAction) {
        form.value = Object.assign(form.value, props.data)
        form.value.dateVal = props.data.date
    } else {
        form.value = ref({})
    }
}

const closeForm = () => {
    form.value = ref({})
    formError.value = ref({})
}

const submit = async () => {
    props.updateAction ? updateHandle() : createHandle()
}

const handleDate = () => {
    if (form.value.dateVal) {
        console.log(form.value.dateVal)
        const date = new Date(form.value.dateVal.getFullYear(), form.value.dateVal.getMonth(), form.value.dateVal.getDate());
        const newDate = dayjs(date).format('YYYY-MM-DD');
        form.value.date = newDate
    }
}

const createHandle = async () => {
    isLoading.value = true
    axios.post(route('settings.attendance.holiday.create'), form.value)
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

const updateHandle = async () => {
    isLoading.value = true
    axios.put(route('settings.attendance.holiday.update', { 'id': props.data.id }), form.value)
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
    <VDialog :showModal="openDialog" :title="updateAction ? 'Update Holiday Schedule' : 'Add Holiday Schedule'" @opened="openForm"
        @closed="closeForm" size="md">
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
            <div class="grid grid-cols-1 gap-3">
                <VInput placeholder="Input Name" label="Name" :required="true" v-model="form.name"
                    :errorMessage="formError.name" @update:modelValue="formError.name = ''" />
                <label class="block text-sm font-medium text-slate-600 mb-1">
                    Date <span class="text-rose-500">*</span>
                </label>
                <Datepicker v-model="form.dateVal" @update:modelValue="handleDate" single-picker :enableTimePicker="false"
                    position="left" :clearable="false" format="dd MMMM yyyy" previewFormat="dd MMMM yyyy"
                    placeholder="Select Date Holiday" :class="{ 'date_error': formError.date }" />
                    <div class="text-xs" :class="[{
                        'text-rose-500': formError.date,
                    }]" v-if="formError.date">
                        {{ formError.date }}
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