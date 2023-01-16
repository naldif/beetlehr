<script setup>
import axios from "axios";
import dayjs from "dayjs"
import { ref } from "vue";
import { bool, object } from "vue-types";
import { notify } from "notiwind";
import VDialog from '@/components/VDialog/index.vue';
import VButton from '@/components/VButton/index.vue';

const props = defineProps({
    openDialog: bool(),
    additional: object().def({})
})

const emit = defineEmits(['close', 'successSubmit'])

const isLoading = ref(false);
const formError = ref({})
const form = ref({
    'dateVal': null,
})

const openForm = () => {
    form.value = ref({})
}

const handleDate = () => {
    if (form.value.dateVal) {
        formError.value.paid_date = ''
        form.value.paid_date = dayjs(form.value.dateVal).format('YYYY-MM-DD');
    }
}

const closeForm = () => {
    form.value = ref({})
    formError.value = ref({})
}

const submit = async () => {
    isLoading.value = true
    axios.post(route('payroll.run.payrollemployee.paidall', { 'id': props.additional.payroll_slip_id }), form.value)
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
            const metaError = res.response.data.meta?.error
            if (result.hasOwnProperty('errors')) {
                formError.value = ref({});
                Object.keys(result.errors).map((key) => {
                    formError.value[key] = result.errors[key].toString();
                });
            }

            if (metaError) {
                notify({
                    type: "error",
                    group: "top",
                    text: metaError
                }, 2500)
            } else {
                notify({
                    type: "error",
                    group: "top",
                    text: result.message
                }, 2500)
            }
        }).finally(() => isLoading.value = false)
}
</script>

<template>
    <VDialog :showModal="openDialog" title="Paid All" @opened="openForm" @closed="closeForm" size="xl">
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
                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-1">
                            Paid Date <span class="text-rose-500">*</span>
                        </label>
                        <Datepicker v-model="form.dateVal" @update:modelValue="handleDate" single-picker :enableTimePicker="false"
                            position="left" :clearable="false" format="dd MMMM yyyy" previewFormat="dd MMMM yyyy"
                            placeholder="Select Paid Date" :class="{ 'date_error': formError.paid_date }" />
                        <div class="text-xs" :class="[{
                                                                'text-rose-500': formError.paid_date,
                                                            }]" v-if="formError.paid_date">
                            {{ formError.paid_date }}
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <template v-slot:footer>
            <div class="flex flex-wrap justify-end space-x-2">
                <VButton label="Cancel" type="default" @click="$emit('close')" />
                <VButton :is-loading="isLoading" label="Save" type="primary" @click="submit" />
            </div>
        </template>
    </VDialog>
</template>