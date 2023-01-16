<script setup>
import axios from "axios";
import dayjs from "dayjs"
import { onMounted, ref } from "vue";
import { bool, object, any } from "vue-types";
import { notify } from "notiwind";
import VDialog from '@/components/VDialog/index.vue';
import VButton from '@/components/VButton/index.vue';
import VSelect from '@/components/VSelect/index.vue';

const props = defineProps({
    openDialog: bool(),
    updateAction: bool().def(false),
    data: object().def({}),
    additional: object().def({}),
    branch: any(),
    shiftList: object().def({})
})

const emit = defineEmits(['close', 'successSubmit'])

const isLoading = ref(false);
const formError = ref({})
const form = ref({})
const branchSelectHandle = ref()
const typeSelectHandle = ref()
const employeeSelectHandle = ref()
const groupSelectHandle = ref()

const employee_list = ref([])
const group_list = ref([])
const typeOptions = ref(['Employee', 'Group'])

const changeUser = () => {
    employeeSelectHandle.value ? employeeSelectHandle.value.clearSelected() : ''
    groupSelectHandle.value ? groupSelectHandle.value.clearSelected() : ''

    if (form.value.branch_id) {
        axios.get(route('attendance.schedule.getEmployeeByBranch', { 'branch_id': form.value.branch_id })
        ).then((res) => {
            employee_list.value = res.data
            console.log(employee_list.value)
        }).catch((err) => {
            console.log(err)
        })

        axios.get(route('attendance.schedule.getGroupByBranch', { 'branch_id': form.value.branch_id })
        ).then((res) => {
            group_list.value = res.data
        }).catch((err) => {
            console.log(err)
        })
    }

}

const handleType = () => {
    employeeSelectHandle.value ? employeeSelectHandle.value.clearSelected() : ''
    groupSelectHandle.value ? groupSelectHandle.value.clearSelected() : ''
}

const handleDateRange = () => {
    const dateStart = new Date(form.value.date[0].getFullYear(), form.value.date[0].getMonth(), form.value.date[0].getDate())
    const dateEnd = new Date(form.value.date[1].getFullYear(), form.value.date[1].getMonth(), form.value.date[1].getDate())

    form.value.start_date = dayjs(dateStart).format('YYYY-MM-DD')
    form.value.end_date = dayjs(dateEnd).format('YYYY-MM-DD')
}

const openForm = () => {
    if (props.updateAction) {
        form.value = Object.assign(form.value, props.data)
        // handlebranch(form.value.branch_id)
    } else {
        form.value = ref({})
        form.value.branch_id = props.branch
        changeUser()
    }
}

const closeForm = () => {
    branchSelectHandle.value.clearSelected()
    employeeSelectHandle.value ? employeeSelectHandle.value.clearSelected() : ''
    groupSelectHandle.value ? groupSelectHandle.value.clearSelected() : ''
    typeSelectHandle.value.clearSelected()
    form.value = ref({})
    formError.value = ref({})
}


const submit = async () => {
    props.updateAction ? '' : createData()
}



const createData = async () => {
    isLoading.value = true
    const data = {
        start_date: form.value.start_date,
        end_date: form.value.end_date,
        shift_id: form.value.shift_id,
        employee_id: form.value.employee_id,
        group_id: form.value.group_id,
        type: form.value.type,
        date: form.value.date
    }
    axios.post(route('attendance.schedule.createBulk'), data)
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

</script>



<template>
    <VDialog :showModal="openDialog" :title="updateAction ? 'Update Employee Group' : 'Generate Schedule'"
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
                        ref="branchSelectHandle" @update:modelValue="changeUser()" :disabled="true" />
                </div>
                <div class="col-span-2">
                    <VSelect placeholder="Select Type" :required="true" v-model="form.type" :options="typeOptions"
                        label="Type" :errorMessage="formError.type" ref="typeSelectHandle"
                        @update:modelValue="handleType" />
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-medium text-slate-600 mb-1">
                        Date Range <span class="text-rose-500">*</span>
                    </label>
                    <Datepicker v-model="form.date" @update:modelValue="handleDateRange" range :partial-range="false"
                        :enableTimePicker="false" position="left" :clearable="false" format="dd MMMM yyyy"
                        previewFormat="dd MMMM yyyy" placeholder="Select Date Range"
                        :class="{ 'date_error': formError.date }" />
                    <div class="text-xs" :class="[{
                        'text-rose-500': formError.date,
                    }]" v-if="formError.date">
                        {{ formError.date }}
                    </div>
                </div>

                <div class="col-span-2">
                    <VSelect placeholder="Select Shift" :required="true" v-model="form.shift_id"
                        :options="shiftList" label="Shift" :errorMessage="formError.shift_id"
                        ref="shiftSelectHandle" @update:modelValue="formError.shift_id = ''" />
                </div>

                <div v-if="form.type == 'Employee'" class="col-span-2">
                    <VSelect placeholder="Select employee" type="tags" :required="true" v-model="form.employee_id"
                        :options="employee_list" label="Employee" :errorMessage="formError.employee_id"
                        ref="employeeSelectHandle" @update:modelValue="formError.employee_id = ''" />
                </div>

                <div v-if="form.type == 'Group'" class="col-span-2">
                    <VSelect placeholder="Select group" type="tags" :required="true" v-model="form.group_id"
                        :options="group_list" label="Group" :errorMessage="formError.group_id" ref="groupSelectHandle"
                        @update:modelValue="formError.group_id = ''" />
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