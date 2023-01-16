<script>
export default {
    layout: AppLayout
}
</script>
<script setup>
import axios from "axios";
import { Inertia } from '@inertiajs/inertia'
import { notify } from "notiwind";
import { object, string } from "vue-types";
import { Head } from "@inertiajs/inertia-vue3";
import { ref, onMounted } from "vue";
import AppLayout from '@/layouts/apps.vue';
import VBreadcrumb from '@/components/VBreadcrumb/index.vue';
import VButton from '@/components/VButton/index.vue';
import VSelect from '@/components/VSelect/index.vue';
import VInput from '@/components/VInput/index.vue';
import dayjs from "dayjs";

const breadcrumb = [
    {
        name: "Dashboard",
        active: false,
        to: route('dashboard.index')
    },
    {
        name: "Payroll",
        active: false,
        to: route('payroll.run.index')
    },
    {
        name: "Payroll Employee",
        active: false,
        to: route('payroll.run.payrollemployee', { 'id': props.additional.payroll_employee.payroll_slip_id })
    },
    {
        name: "Payroll Slip",
        active: false,
        to: route('payroll.run.payrollemployee.detail', { 'id': props.additional.payroll_employee.id })
    },
    {
        name: "Edit Slip",
        active: true,
        to: route('payroll.run.payrollemployee.edit', { 'id': props.additional.payroll_employee.id })
    },
]
const addEarningColor = ref('text-slate-600')
const addDeductionColor = ref('text-slate-600')
const isLoading = ref(false)
const formError = ref({})
const form = ref({
    'paid_on': props.additional.payroll_employee.paid_on,
    'status': props.additional.payroll_employee.status,
    'dateVal': props.additional.payroll_employee.paid_on ? new Date(props.additional.payroll_employee.paid_on) : null,
})
const earningComponentOptions = ref({})
const deductionComponentOptions = ref({})

const props = defineProps({
    title: string(),
    additional: object(),
})

const handleDate = () => {
    if (form.value.dateVal) {
        formError.value.paid_on = ''
        form.value.paid_on = dayjs(form.value.dateVal).format('YYYY-MM-DD');
    }
}

const updateEarning = (index, data) => {
    const earning = props.additional.payroll_earning_components.find(e => e.id == data)
    form.value.earningComponents[index].value = earning.value
    form.value.earningComponents[index].amount = earning.amount
    form.value.earningComponents[index].is_editable = earning.is_editable
}

const updateDeduction = (index, data) => {
    const deduction = props.additional.payroll_deduction_components.find(e => e.id == data)
    form.value.deductionComponents[index].value = deduction.value
    form.value.deductionComponents[index].amount = deduction.amount
    form.value.deductionComponents[index].is_editable = deduction.is_editable
}

const deleteEarning = (index) => {
    form.value.earningComponents.splice(index, 1)
}

const deleteDeduction = (index) => {
    form.value.deductionComponents.splice(index, 1)
}

const addEarningComponents = () => {
    const defaultEarning = props.additional.payroll_earning_components[0]

    if(defaultEarning) {
        const obj = {
            id: null,
            amount: defaultEarning.amount,
            is_editable: defaultEarning.is_editable,
            name: defaultEarning.name,
            is_taxable: defaultEarning.is_taxable,
            type: defaultEarning.type,
            payroll_component_id: defaultEarning.id,
            value: defaultEarning.value
        };

        form.value.earningComponents.push(obj)   
    }
}

const addDeductionComponents = () => {
    const defaultDeduction = props.additional.payroll_deduction_components[0]

    if (defaultDeduction) {
        const obj = {
            id: null,
            amount: defaultDeduction.amount,
            is_editable: defaultDeduction.is_editable,
            is_taxable: defaultDeduction.is_taxable,
            name: defaultDeduction.name,
            type: defaultDeduction.type,
            payroll_component_id: defaultDeduction.id,
            value: defaultDeduction.value
        };

        form.value.deductionComponents.push(obj)
    }
}

const submit = async () => {
    isLoading.value = true

    console.log(form.value)

    axios.put(route('payroll.run.payrollemployee.update', { 'id': props.additional.payroll_employee.id }), form.value)
        .then((res) => {
            notify({
                type: "success",
                group: "top",
                text: res.data.meta.message
            }, 2500)
            
            Inertia.visit(route('payroll.run.payrollemployee.detail', { 'id': props.additional.payroll_employee.id }))
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

const initData = () => {
    form.value.earningComponents = Array.from(props.additional.earning_components)
    form.value.deductionComponents = Array.from(props.additional.deduction_components)
    earningComponentOptions.value = Object.fromEntries(props.additional.payroll_earning_components.map(el => [el.id, el.name]));
    deductionComponentOptions.value = Object.fromEntries(props.additional.payroll_deduction_components.map(el => [el.id, el.name]));
}

onMounted(() => {
    initData()
});
</script>

<template>
    <Head :title="props.title" />
    <VBreadcrumb :routes="breadcrumb" />
    <div class="mb-4 sm:mb-6 flex justify-between items-center">
        <h1 class="text-2xl md:text-3xl text-slate-800 font-bold">Edit Slip</h1>
    </div>
    <div class="bg-white shadow-lg rounded-sm border border-slate-200"
        :class="isLoading && 'min-h-[40vh] sm:min-h-[50vh]'">
        <header class="block justify-between items-center sm:flex py-6 px-4 border-b">
            <h2 class="font-bold text-slate-800">
                Payroll Slip for the Month of {{ additional.payroll_date }}
            </h2>
        </header>
        <div class="p-6">
            <div class="w-full text-center font-bold text-md bg-sky-100 p-2 rounded-md border border-sky-100">
                <div class="mb-2">
                    Salary Slip
                </div>
                <div>
                    Period : {{ additional.payroll_date }}
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4 py-6 px-2 border-b">
                <div class="grid grid-cols-2 gap-4">
                    <div class="text-slate-600 font-bold text-md">
                        Employee Name
                    </div>
                    <div>
                        {{ additional.payroll_employee.employee_detail.user_detail.name }}
                    </div>
                    <div class="text-slate-600 font-bold text-md">
                        Employee External Id
                    </div>
                    <div>
                        {{ additional.payroll_employee.employee_detail.employee_external_id }}
                    </div>
                    <div class="text-slate-600 font-bold text-md">
                        Designation
                    </div>
                    <div>
                        {{ additional.payroll_employee.employee_detail.designation_detail.name }}
                    </div>
                    <div class="text-slate-600 font-bold text-md">
                        Phone Number
                    </div>
                    <div>
                        {{ additional.payroll_employee.employee_detail.phone_number }}
                    </div>
                    
                </div>
               <div class="grid grid-cols-2 gap-4">
                    <div class="text-slate-600 font-bold text-md">
                        Branch Name
                    </div>
                    <div>
                        {{ additional.payroll_employee.employee_detail.branch_detail.name }}
                    </div>
                    <div class="text-slate-600 font-bold text-md">
                        Bank Name
                    </div>
                    <div>
                        {{ additional.payroll_employee.employee_detail.bank_name ?? '-' }}
                    </div>
                    <div class="text-slate-600 font-bold text-md">
                        Account Name
                    </div>
                    <div>
                        {{ additional.payroll_employee.employee_detail.account_name ?? '-' }}
                    </div>
                    <div class="text-slate-600 font-bold text-md">
                        Account Number
                    </div>
                    <div>
                        {{ additional.payroll_employee.employee_detail.account_number ?? '-' }}
                    </div>
                </div>
            </div>
            <div  class="grid grid-cols-2 gap-4 py-6 px-2">
                <div>
                    <label class="block text-sm font-medium text-slate-600 mb-1">
                        Paid On <span class="text-rose-500">*</span>
                    </label>
                    <Datepicker v-model="form.dateVal" @update:modelValue="handleDate" single-picker :enableTimePicker="false"
                        position="left" :clearable="false" format="dd MMMM yyyy" previewFormat="dd MMMM yyyy"
                        placeholder="Select Paid Date" :class="{ 'date_error': formError.paid_on }" />
                    <div class="text-xs" :class="[{
                                            'text-rose-500': formError.paid_on,
                                        }]" v-if="formError.paid_on">
                        {{ formError.paid_on }}
                    </div>
                </div>
                <VSelect placeholder="Select Status" :required="true" v-model="form.status" :options="{'Generated': 'Generated', 'Paid': 'Paid'}"
                    label="Status" :errorMessage="formError.status" @update:model-value="formError.status = ''"/>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <!-- Income -->
                <div>
                    <div class="bg-sky-100 text-center p-2 font-bold text-md rounded-md border border-sky-100 mb-2">Income</div>
                    <div class="px-2 space-y-2">
                        <div class="flex justify-between items-center" v-for="(data, index) in form.earningComponents" :key="index">
                            <div class="w-5/12">
                                <VSelect v-model="form.earningComponents[index].payroll_component_id" :options="earningComponentOptions" placeholder="Select Component" :clearable="false" @update:model-value="updateEarning(index, form.earningComponents[index].payroll_component_id)"/>
                            </div>
                            <div class="flex space-x-2 items-center">
                                <div v-if="data.is_editable">
                                    <VInput v-model="form.earningComponents[index].value" placeholder="Insert Value" />
                                </div>
                                <div v-else class="">
                                    Rp{{ data.amount }}
                                </div>
                                <div class="cursor-pointer" @click="deleteEarning(index)" :class="
                                    {'mt-[1px]': !data.is_editable, 'mt-[2px]': data.is_editable}
                                ">
                                    <font-awesome-icon icon="fa-solid fa-trash-can" class="h-6 text-rose-600" />
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center cursor-pointer w-max" @mouseover="addEarningColor = 'text-blue-500'"
                            @mouseout="addEarningColor = 'text-slate-600'" @click="addEarningComponents">
                            <font-awesome-icon icon="fa-solid fa-circle-plus" :class="addEarningColor" />
                            <p class="block text-sm font-bold ml-1" :class="addEarningColor">Add an Earning Component</p>
                        </div>
                    </div>
                </div>
                
                <!-- Deduction -->
                <div>
                    <div class="bg-sky-100 text-center p-2 font-bold text-md rounded-md border border-sky-100 mb-2">Deduction</div>
                    <div class="px-2 space-y-2">
                        <div class="flex justify-between items-center" v-for="(data, index) in form.deductionComponents" :key="index">
                            <div class="w-5/12">
                                <VSelect v-model="form.deductionComponents[index].payroll_component_id" :options="deductionComponentOptions" placeholder="Select Component" :clearable="false" @update:model-value="updateDeduction(index, form.deductionComponents[index].payroll_component_id)" v-if="data.payroll_component_id"/>
                                <div v-else>
                                    {{ data.name }}
                                </div>
                            </div>
                            <div class="flex space-x-2 items-center">
                                <div v-if="data.is_editable">
                                    <VInput v-model="form.deductionComponents[index].value" placeholder="Insert Value" />
                                </div>
                                <div v-else class="">
                                    Rp{{ data.amount }}
                                </div>
                                <div class="cursor-pointer" @click="deleteDeduction(index)" :class="
                                    {'mt-[1px]': !data.is_editable, 'mt-[2px]': data.is_editable}
                                " v-if="data.payroll_component_id">
                                    <font-awesome-icon icon="fa-solid fa-trash-can" class="h-6 text-rose-600" />
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center cursor-pointer w-max" @mouseover="addDeductionColor = 'text-blue-500'"
                            @mouseout="addDeductionColor = 'text-slate-600'" @click="addDeductionComponents">
                            <font-awesome-icon icon="fa-solid fa-circle-plus" :class="addDeductionColor" />
                            <p class="block text-sm font-bold ml-1" :class="addDeductionColor">Add a Deduction Component</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <footer class="block justify-start items-center sm:flex p-4 border-t">
            <VButton :is-loading="isLoading" label="Save" type="primary" @click="submit" />
        </footer>
    </div>
</template>