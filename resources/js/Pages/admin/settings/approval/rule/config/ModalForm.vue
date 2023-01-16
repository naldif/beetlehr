<script setup>
import axios from "axios";
import { ref } from "vue";
import { bool, object } from "vue-types";
import { notify } from "notiwind";
import VDialog from '@/components/VDialog/index.vue';
import VButton from '@/components/VButton/index.vue';
import VSelect from '@/components/VSelect/index.vue';
import VSwitch from '@/components/VSwitch/index.vue';

const props = defineProps({
    openDialog: bool(),
    updateAction: bool().def(false),
    data: object().def({}),
    additional: object().def({})
})

const emit = defineEmits(['close', 'successSubmit'])

const trashColor = ref('text-rose-600')
const addColor = ref('text-slate-600')
const isLoading = ref(false);
const formError = ref({})
const form = ref({})
const approvalRuleForm = ref([])
const approvalRuleFormError = ref([])
const selectApprovers = ref({
    'designated_person': 'Designated Person',
    'reports_to': 'Direct manager'
})

const openForm = () => {
    if (props.updateAction) {
        form.value = Object.assign(form.value, props.data)
        if (props.data.approval_rule_levels.length > 0) {
            approvalRuleForm.value = Array.from(props.data.approval_rule_levels)
        } else {
            const newRule = [
                {
                    approval_rule_id: 1,
                    approver_type: "reports_to",
                    employee_id: null,
                    id: 1,
                    level_approval: 1
                }
            ]
            approvalRuleForm.value = Array.from(newRule)
        }

        if (props.data.employee.length > 0) {
            approvalRuleForm.value.employee = Array.from(props.data.employee)
        } else {
            const newEmployee = [
                []
            ]
            approvalRuleForm.value.employee = Array.from(newEmployee)
        }

        form.value.branch_id = form.value.id
    } else {
        form.value = ref({})
        approvalRuleForm.value = ref([])
    }
}

const closeForm = () => {
    form.value = ref({})
    approvalRuleForm.value = ref([])
    formError.value = ref({})
    approvalRuleFormError.value = ref([])
}

const submit = async () => {
    props.updateAction ? updateHandle() : createHandle()
}

const generatePlaceholderLevel = (data) => {
    if (data === 1) {
        return '1st Level Approval'
    } else if (data === 2) {
        return '2nd Level Approval'
    } else if (data === 3) {
        return '3rd Level Approval'
    } else {
        return data + 'th Level Approval'
    }
}

const addApprovalRules = () => {
    const lastRule = approvalRuleForm.value.slice(-1)[0]

    const obj = {
        id: lastRule.id + 1,
        approval_rule_id: form.value.approval_rule_id,
        approver_type: 'reports_to',
        employee_id: null,
        level_approval: lastRule.level_approval + 1
    };

    approvalRuleForm.value.push(obj)
}

const deleteRule = (index) => {
    approvalRuleForm.value.splice(index, 1)
}

const createHandle = async () => {
    isLoading.value = true
    axios.post(route('settings.company.bpjs.bpjsk.create'), form.value)
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
    axios.put(route('settings.approval.rule.config.update'), {
        branch_id: form.value.id,
        approval_type_id: props.additional.data.id,
        need_approval: form.value.need_approval,
        rules: approvalRuleForm.value,
        employee: approvalRuleForm.value.employee
    }).then((res) => {
        emit('close')
        emit('successSubmit')
        form.value = ref({})
        approvalRuleForm.value = ref({})
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
    <VDialog :showModal="openDialog" :title="updateAction ? 'Update Rule Configuration' : 'Add Payroll Component'"
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
            <div class="grid grid-cols-1 gap-3">
                <div>
                    <label class="text-sm font-bold text-slate-600 mb-1">Branch Need Approval</label>
                    <VSwitch v-model="form.need_approval" label="need_approval" />
                </div>
                <div v-if="approvalRuleForm.length > 0">
                    <label class="text-sm font-bold text-slate-600 mb-1">Approvers</label>
                    <div v-for="(data, index) in approvalRuleForm" :key="index">
                        <label class="block text-sm font-medium text-slate-600 mb-1">{{ generatePlaceholderLevel(index +
                                1)
                        }}</label>
                        <div class="flex flex-row w-full space-x-2 mb-1">
                            <div class="w-1/2">
                                <VSelect placeholder="Select Type" v-model="approvalRuleForm[index].approver_type"
                                    :options="selectApprovers" @update:modelValue="form.approver === ''"
                                    :clearable="false" />
                            </div>
                            <div class="w-[44%]" v-if="data.approver_type === 'designated_person'">
                                <VSelect placeholder="Select Employee" v-model="approvalRuleForm.employee[index]"
                                    :errorMessage="approvalRuleFormError.employee ? approvalRuleFormError.employee[index] : ''"
                                    @update:modelValue="approvalRuleFormError.employee ? approvalRuleFormError.employee[index] === '' : ''"
                                    type="async-single"
                                    :optionRoute="route('settings.approval.rule.config.getemployeerule')" />
                            </div>
                            <div class="my-auto h-full cursor-pointer" v-if="index > 0"
                                @mouseover="trashColor = 'text-rose-700'" @mouseout="trashColor = 'text-rose-600'"
                                @click="deleteRule(index)">
                                <font-awesome-icon icon="fa-solid fa-trash-can" class="h-6" :class="trashColor" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center cursor-pointer w-max" @mouseover="addColor = 'text-blue-500'"
                    @mouseout="addColor = 'text-slate-600'" @click="addApprovalRules">
                    <font-awesome-icon icon="fa-solid fa-circle-plus" :class="addColor" />
                    <p class="block text-sm font-bold ml-1" :class="addColor">Add an Approver</p>
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