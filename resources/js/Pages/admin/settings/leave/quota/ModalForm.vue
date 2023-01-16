<script setup>
import axios from "axios";
import { ref } from "vue";
import { bool, object } from "vue-types";
import { notify } from "notiwind";
import VDialog from '@/components/VDialog/index.vue';
import VButton from '@/components/VButton/index.vue';
import VInput from '@/components/VInput/index.vue';
import VSelect from '@/components/VSelect/index.vue';
import debounce from "@/composables/debounce"

const props = defineProps({
    openDialog: bool(),
    updateAction: bool().def(false),
    data: object().def({}),
    additional: object().def({})
})

const emit = defineEmits(['close', 'successSubmit'])

const leavePlaceholder = ref('Select Branch First')
const leaveOptions = ref([])
const branchSelectHandle = ref()
const leaveTypeSelectHandle = ref()
const isLoading = ref(false);
const leaveTypeLoading = ref(true);
const formError = ref({})
const form = ref({})

const openForm = () => {
    if (props.updateAction) {
        form.value = Object.assign(form.value, props.data)
    } else {
        form.value = ref({})
    }
}

const closeForm = () => {
    if (!props.updateAction) {
        branchSelectHandle.value.clearSelected()
        leaveTypeSelectHandle.value.clearSelected()
    }
    form.value = ref({})
    formError.value = ref({})
}

const submit = async () => {
    props.updateAction ? updateHandle() : createHandle()
}

const getLeaveTypePlaceholder = () => {
    if (!form.value.branch_id || form.value.branch_id === '') {
        leavePlaceholder.value = 'Select Branch First';
    } else if (leaveTypeLoading.value) {
        leavePlaceholder.value = 'Loading...';
    } else {
        leavePlaceholder.value = 'Select Leave Type';
    }
}

const branchSelected = () => {
    formError.value.branch_id = ''
    leaveTypeLoading.value = true
    leaveTypeSelectHandle.value.clearSelected()
    getLeaveTypePlaceholder()

    if (!form.value.branch_id) {
        leaveTypeLoading.value = true
    } else {
        getLeaveType()
    }
}

const getLeaveType = debounce(async () => {
    axios.get(route('settings.leave.quota.getLeaveType'), {
        params: {
            branch_id: form.value.branch_id
        }
    }).then((res) => {
        leaveOptions.value = res.data
    }).catch((res) => {
        notify({
            type: "error",
            group: "top",
            text: res.response.data.message
        }, 2500)
    }).finally(() => {
        leaveTypeLoading.value = false
        getLeaveTypePlaceholder()
    })
}, 500);

const createHandle = async () => {
    isLoading.value = true
    axios.post(route('settings.leave.quota.create'), form.value)
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
            const metaError = res.response.data.meta.error
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

const updateHandle = async () => {
    isLoading.value = true
    axios.put(route('settings.leave.quota.update', { 'id': props.data.id }), form.value)
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
            const metaError = res.response.data.meta.error
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
    <VDialog :showModal="openDialog" :title="updateAction ? 'Update Leave Type' : 'Add Leave Type'" @opened="openForm"
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
            <div class="grid grid-cols-1 gap-3" v-if="!updateAction">
                <VSelect placeholder="Select Branch" :required="true" v-model="form.branch_id"
                    :options="additional.branch_list" label="Branch" :errorMessage="formError.branch_id"
                    @update:modelValue="branchSelected" ref="branchSelectHandle" :disabled="updateAction" />
                <VSelect :placeholder="leavePlaceholder" :required="true" v-model="form.leave_type_id"
                    :options="leaveOptions" label="Leave Type" :errorMessage="formError.leave_type_id"
                    @update:modelValue="formError.leave_type_id = ''" ref="leaveTypeSelectHandle"
                    :disabled="leaveTypeLoading" />
                <VInput placeholder="Input Quota" label="Quota" :required="true" v-model="form.quota"
                    :errorMessage="formError.quota" @update:modelValue="formError.quota = ''" type="number" />
            </div>
            <div class="grid grid-cols-1 gap-3" v-else>
                <VInput placeholder="Employee Name" label="Employee Name" v-model="form.employee_name"
                    :disabled="true" />
                <VInput placeholder="Leave Type" label="Leave Type" v-model="form.leave_update_name" :disabled="true" />
                <VInput placeholder="Input Quota" label="Quota" :required="true" v-model="form.quota"
                    :errorMessage="formError.quota" @update:modelValue="formError.quota = ''" type="number" />
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