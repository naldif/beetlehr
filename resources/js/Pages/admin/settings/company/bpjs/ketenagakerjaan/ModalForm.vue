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
import VSelect from '@/components/VSelect/index.vue';

const props = defineProps({
    openDialog: bool(),
    updateAction: bool().def(false),
    data: object().def({}),
    additional: object().def({})
})

const emit = defineEmits(['close', 'successSubmit'])

const riskLevelSelectHandle = ref()
const isLoading = ref(false);
const formError = ref({})
const form = ref({})

const openForm = () => {
    if(props.updateAction){
        form.value = Object.assign(form.value, props.data)
        handleDate()
    }else{
        form.value = ref({})
    }
}

const closeForm = () => {
    riskLevelSelectHandle.value.clearSelected()
    form.value = ref({})
    formError.value = ref({})
}

const submit = async () => {
    props.updateAction ? updateHandle() : createHandle()
}

const handleDate = () => {
    if (form.value.date) {
        formError.value.valid_month = ''
        const date = new Date(form.value.date.year, form.value.date.month, 1);
        form.value.valid_month = dayjs(date).format('YYYY-MM-DD');
    }
}

const createHandle = async () => {
    isLoading.value = true
    axios.post(route('settings.company.bpjs.bpjstk.create'), form.value)
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
    console.log(form.value)
    axios.put(route('settings.company.bpjs.bpjstk.update', { 'id': props.data.id }), form.value)
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
    <VDialog :showModal="openDialog" :title="updateAction ? 'Update NPP BPJS Ketenagakerjaan' : 'Add NPP BPJS Ketenagakerjaan'" @opened="openForm" @closed="closeForm" size="xl">
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
            <!-- Basic Information Section -->
            <section class="grid grid-cols-2 gap-3">
                <label class="block text-sm font-semibold text-slate-800">
                    Basic Information
                </label>
                <div class="col-span-2">
                    <VInput placeholder="Input NPP Name" label="NPP Name" :required="true" v-model="form.name"
                        :errorMessage="formError.name" @update:modelValue="formError.name = ''" />
                </div>
                <VInput placeholder="Input Registration Number" label="Company Registration Number"
                    v-model="form.registration_number" :errorMessage="formError.registration_number"
                    @update:modelValue="formError.registration_number = ''" />
                <VInput placeholder="Input BPJS Office" label="BPJS Office" v-model="form.bpjs_office"
                    :errorMessage="formError.bpjs_office" @update:modelValue="formError.bpjs_office = ''" />
                <VInput placeholder="Input Minimum Value" label="Minimum Value" v-model="form.minimum_value"
                    :errorMessage="formError.minimum_value" @update:modelValue="formError.minimum_value = ''" type="number" />
                <div>
                    <label class="block text-sm font-medium text-slate-600 mb-1">
                        Valid Month <span class="text-rose-500">*</span>
                    </label>
                    <Datepicker v-model="form.date" @update:modelValue="handleDate" month-picker :enableTimePicker="false"
                        position="left" :clearable="false" format="MMMM yyyy" previewFormat="MMMM yyyy"
                        placeholder="Select Valid Month" :class="{'date_error' : formError.valid_month}" />
                    <div class="text-xs mt-1" :class="[{
                        'text-rose-500': formError.valid_month,
                    }]" v-if="formError.valid_month">
                        {{ formError.valid_month }}
                    </div>
                </div>
                <VSwitch v-model="form.status" label="Status" type="block"/>
            </section>

            <!-- Guarantee Section -->
            <section class="grid grid-cols-2 gap-3 mt-6">
                <div class="col-span-2">
                    <label class="block text-sm font-semibold text-slate-800">
                        Guarantee
                    </label>
                </div>
                <div class="col-span-2">
                    <VSwitch v-model="form.bpjstk_risk" label="Jaminan Kecelakaan Kerja" type="inline"/>
                </div>
                <transition enter-active-class="transition duration-200 ease-out" enter-from-class="transform scale-95 opacity-0"
                    enter-to-class="transform scale-200 opacity-200" leave-active-class="transition duration-75 ease-in"
                    leave-from-class="transform scale-200 opacity-200" leave-to-class="transform scale-95 opacity-0">
                    <VSelect placeholder="Select Risk Level" v-model="form.bpjstk_risk_level_id" :options="additional.risk_level"
                        :errorMessage="formError.bpjstk_risk_level_id" @update:modelValue="formError.bpjstk_risk_level_id = ''"
                        ref="riskLevelSelectHandle" v-show="form.bpjstk_risk"/>
                </transition>
                <div class="col-span-2">
                    <VSwitch v-model="form.old_age" label="Jaminan Hari Tua" type="inline"/>
                </div>
                <div class="col-span-2">
                    <VSwitch v-model="form.life_insurance" label="Jaminan Kematian" type="inline"/>
                </div>
                <div class="col-span-2">
                    <VSwitch v-model="form.pension_time" label="Jaminan Pensiun" type="inline"/>
                </div>
            </section>
        </template>
        <template v-slot:footer>
            <div class="flex flex-wrap justify-end space-x-2">
                <VButton label="Cancel" type="default" @click="$emit('close')" />
                <VButton :is-loading="isLoading" :label="updateAction ? 'Update' : 'Create'" type="primary" @click="submit" />
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