<script>
export default {
    layout: AppLayout
}
</script>
<script setup>
import { Inertia } from '@inertiajs/inertia'
import { object, string } from "vue-types";
import { Head } from "@inertiajs/inertia-vue3";
import { ref, onMounted, watch } from "vue";
import AppLayout from '@/layouts/apps.vue';
import VBreadcrumb from '@/components/VBreadcrumb/index.vue';
import VButton from '@/components/VButton/index.vue';

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
        active: true,
        to: route('payroll.run.payrollemployee.detail', { 'id': props.additional.payroll_employee.id })
    },
]
const isLoading = ref(true)
const payrollLeftInformation = ref([])

const props = defineProps({
    title: string(),
    additional: object(),
})

const handleEditPage = () => {
    Inertia.visit(route('payroll.run.payrollemployee.edit', { 'id': props.additional.payroll_employee.id }));
}

const initData = () => {
    payrollLeftInformation.value = [
        {
            label: 'Employee Name',
            value: props.additional.payroll_employee.employee_detail.user_detail.name
        },
        {
            label: 'Employee External Id',
            value: props.additional.payroll_employee.employee_detail.employee_external_id
        },
        {
            label: 'Designation',
            value: props.additional.payroll_employee.employee_detail.designation_detail.name
        },
        {
            label: 'Phone Number',
            value: props.additional.payroll_employee.employee_detail.phone_number
        }
    ]
}

watch(() => props.additional, (newValue, oldValue) => {
    if (newValue) {
        initData()
    }
});

onMounted(() => {
    initData()
});
</script>

<template>
    <Head :title="props.title" />
    <VBreadcrumb :routes="breadcrumb" />
    <div class="mb-4 sm:mb-6 flex justify-between items-center">
        <h1 class="text-2xl md:text-3xl text-slate-800 font-bold">Payroll Slip</h1>
    </div>
    <div class="bg-white shadow-lg rounded-sm border border-slate-200"
        :class="isLoading && 'min-h-[40vh] sm:min-h-[50vh]'">
        <header class="block justify-between items-center sm:flex py-6 px-4 border-b">
            <h2 class="font-bold text-slate-800">
                Payroll Slip for the Month of {{ additional.payroll_date }}
            </h2>
            <div class="mt-3 sm:mt-0 flex space-x-2 sm:justify-between justify-end">
                <VButton label="Edit Slip" type="primary" class="mt-auto" @click="handleEditPage"/>
            </div>
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
            <div class="grid grid-cols-2 gap-4 py-6 px-2">
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
            <div class="grid grid-cols-2 gap-4">
                <!-- Income -->
                <div>
                    <div class="bg-sky-100 text-center p-2 font-bold text-md rounded-md border border-sky-100 mb-2">Income</div>
                    <div class="px-2 space-y-2">
                        <div class="flex justify-between">
                            <div>
                                Base Salary
                            </div>
                            <div>
                                Rp{{ additional.base_salary }}
                            </div>
                        </div>
                        <div class="flex justify-between" v-for="(data, index) in additional.earning_components" :key="index">
                            <div>
                                {{ data.name }}
                            </div>
                            <div>
                                Rp{{ data.amount }}
                            </div>
                        </div>
                        <div class="flex justify-between">
                            <div class="font-semibold">
                                Total Income
                            </div>
                            <div>
                                Rp{{ additional.total_earning }}
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Deduction -->
                <div>
                    <div class="bg-sky-100 text-center p-2 font-bold text-md rounded-md border border-sky-100 mb-2">Deduction</div>
                    <div class="px-2 space-y-2">
                        <div class="flex justify-between" v-for="(data, index) in additional.deduction_components" :key="index">
                            <div>
                                {{ data.name }}
                            </div>
                            <div>
                                Rp{{ data.amount }}
                            </div>
                        </div>
                        <div class="flex justify-between">
                            <div class="font-semibold">
                                Total Deduction
                            </div>
                            <div>
                                Rp{{ additional.total_deduction }}
                            </div>
                        </div>
                    </div>
                    <div class="bg-sky-100 p-2 font-semibold text-md rounded-md border border-sky-100 mt-2 flex justify-between">
                        <div class="ml-6">
                            Take Home Pay
                        </div>
                        <div>
                            Rp{{ additional.take_home_pay }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>