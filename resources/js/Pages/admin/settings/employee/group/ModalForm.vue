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
const employeeSelectHandle = ref()
const employees = ref()

const openForm = () => {
    if (props.updateAction) {
        form.value = Object.assign(form.value, props.data)
        handleBranch(form.value.branch_id)
    } else {
        form.value = ref({})
    }
    console.log(props.data.id)
}

const closeForm = () => {
    form.value = ref({})
    formError.value = ref({})
    branchSelectHandle.value.clearSelected()
    employeeSelectHandle.value.clearSelected()
}


const submit = async () => {
    props.updateAction ? updateEmployeeGroup() : createEmployeeGroup()
}

const createEmployeeGroup = async () => {
    isLoading.value = true
    axios.post(route('settings.employee.group.create'), form.value)
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

const updateEmployeeGroup = async () => {
    isLoading.value = true
    axios.put(route('settings.employee.group.update', { 'id': props.data.id }), form.value)
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


function handleBranch(event) {
    axios.post(route('settings.employee.group.getEmployeeByBranch'), { 'branch_id': event })
        .then((res) => {
            employees.value = res.data
        }).catch((err) => {
            alert(err)
        })
}

</script>

<template>
    <VDialog :showModal="openDialog" :title="updateAction ? 'Update Employee Group' : 'Add Employee Group'"
        @opened="openForm" @closed="closeForm" size="lg">
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
                    <VInput placeholder="Input Name Group" label="Name" :required="true" v-model="form.name"
                        :errorMessage="formError.name" @update:modelValue="formError.name = ''" />
                </div>
                <div class="col-span-2">
                    <VSelect placeholder="Select branch" :required="true" v-model="form.branch_id"
                        :options="additional.branch_list" label="Branch" :errorMessage="formError.branch_id"
                        @update:modelValue="handleBranch($event)" ref="branchSelectHandle" />
                </div>
                <div class="col-span-2">
                    <VSelect placeholder="Select employee" type="tags" :required="true" v-model="form.employee_id"
                        label="Employee" :options="employees" :errorMessage="formError.employee_id"
                        @update:modelValue="formError.employee_id = ''" ref="employeeSelectHandle" />
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