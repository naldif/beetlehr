<script setup>
import axios from "axios";
import dayjs from "dayjs"
import { onMounted, ref } from "vue";
import { bool, object } from "vue-types";
import { notify } from "notiwind";
import VDialog from '@/components/VDialog/index.vue';
import VButton from '@/components/VButton/index.vue';
import VInput from '@/components/VInput/index.vue';
import VSelect from '@/components/VSelect/index.vue';

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
const branchSelectHandle = ref()
const employees = ref()

const openForm = () => {
    if (props.updateAction) {
        form.value = Object.assign(form.value, props.data)
        // handlebranch(form.value.branch_id)
    } else {
        form.value = ref({})
    }
}

const closeForm = () => {
    form.value = ref({})
    formError.value = ref({})
    branchSelectHandle.value.clearSelected()
}


const submit = async () => {
    if (form.value.start_time && form.value.end_time) {
        form.value.start_time = form.value.start_time.hours + ':' + form.value.start_time.minutes
        form.value.end_time = form.value.end_time.hours + ':' + form.value.end_time.minutes
    }
    props.updateAction ? updateData() : createData()
}

const handleShiftType = () => {
    if (form.value.start_time && form.value.end_time) {
        const start_time = form.value.start_time.hours + ':' + form.value.start_time.minutes + ':0'
        const end_time = form.value.end_time.hours + ':' + form.value.end_time.minutes + ':0'
        form.value.is_night_shift = Date.parse('01/01/2011 ' + end_time) < Date.parse('01/01/2011 ' + start_time) ? true : false
        form.value.shift_type = form.value.is_night_shift ? 'Night Shift' : 'Normal Shift'
    }
}

const createData = async () => {
    isLoading.value = true

    axios.post(route('attendance.shift.create'), form.value)
        .then((res) => {
            // console.log(res)
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

const updateData = async () => {
    isLoading.value = true
    axios.put(route('attendance.shift.update', { 'id': props.data.id }), form.value)
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
    <VDialog :showModal="openDialog" :title="updateAction ? 'Update Shift' : 'Add Shift'"
        @opened="openForm" @closed="closeForm" size="xl">
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
                    <VSelect placeholder="Select Branch" :required="true" v-model="form.branch_id"
                        :options="additional.branch_list" label="Branch" :errorMessage="formError.branch_id"
                        ref="branchSelectHandle" />
                </div>
                <VInput placeholder="Input Shift Name" label="Name" :required="true" v-model="form.name"
                    :errorMessage="formError.name" @update:modelValue="formError.name = ''" />
                <VInput placeholder="Input Shift Type" label="Shift Type" :required="true" v-model="form.shift_type"
                    :errorMessage="formError.shift_type" @update:modelValue="formError.shift_type = ''" disabled />

                <div>
                    <label class="block text-sm font-medium text-slate-600 mb-1">Start Time
                        <span class="text-rose-500">*</span>
                    </label>

                    <Datepicker v-model="form.start_time" @update:modelValue="form.end_time ? handleShiftType() : ''"
                        time-picker position="left" :clearable="true" placeholder="00:00"
                        :class="{ 'date_error': formError.start_time }" />
                    <div class="text-xs mt-1" :class="[{
                        'text-rose-500': formError.start_time,
                    }]" v-if="formError.start_time">
                        {{ formError.start_time }}
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-600 mb-1">End Time
                        <span class="text-rose-500">*</span>
                    </label>

                    <Datepicker v-model="form.end_time" @update:modelValue="handleShiftType()" time-picker
                        position="left" :clearable="true" placeholder="00:00"
                        :class="{ 'date_error': formError.end_time }" />
                    <div class="text-xs mt-1" :class="[{
                        'text-rose-500': formError.end_time,
                    }]" v-if="formError.end_time">
                        {{ formError.end_time }}
                    </div>
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