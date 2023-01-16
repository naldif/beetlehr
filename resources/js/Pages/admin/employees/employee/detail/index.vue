<script>
export default {
    layout: AppLayout
}
</script>
<script setup>
import dayjs from 'dayjs'
import { Head, Link } from "@inertiajs/inertia-vue3";
import { ref, onMounted, watch } from "vue";
import { Inertia } from '@inertiajs/inertia'
import { object, string } from "vue-types";
import AppLayout from '@/layouts/apps.vue';
import VBack from '@/components/src/icons/VBack.vue'
import VBreadcrumb from '@/components/VBreadcrumb/index.vue';
import VButton from '@/components/VButton/index.vue';
import VSidebarSetting from '@/components/VSidebarSetting/index.vue';
import VModalForm from '@/Pages/admin/employees/employee/ModalForm.vue';

const breadcrumb = [
    {
        name: "Dashboard",
        active: false,
        to: route('dashboard.index')
    },
    {
        name: "Employee",
        active: false,
        to: route('employment.employee.index')
    },
    {
        name: "Employee",
        active: false,
        to: route('employment.employee.index')
    },
    {
        name: "Detail Employee",
        active: true,
        to: route('employment.employee.show', {'id': props.additional.employee.id})
    },
]
const backActive = ref(false)
const updateAction = ref(false)
let itemSelected = {}
const openModalForm = ref(false)
const basicInformation = ref([]);
const finance = ref([]);
const employmentData = ref([]);

const props = defineProps({
    additional: object(),
    title: string()
})

const handleEditModalForm = () => {
    updateAction.value = true
    openModalForm.value = true
}

const successSubmit = () => {
    openModalForm.value = false
    Inertia.reload()
}

const closeModalForm = () => {
    itemSelected.value = ref({})
    openModalForm.value = false
}

const initData = () => {
    basicInformation.value = [
        {
            label: 'Name',
            value: props.additional.employee.user_detail.name
        },
        {
            label: 'Email',
            value: props.additional.employee.user_detail.email
        },
        {
            label: 'Phone Number',
            value: props.additional.employee.phone_number
        },
        {
            label: 'Designation',
            value: props.additional.employee.designation_detail.name
        },
        {
            label: 'Roles',
            value: props.additional.employee_role.length > 0 ? props.additional.employee_role[0] : '-'
        },
        {
            label: 'User Device',
            value: props.additional.employee.user_detail.user_device ?? '-'
        },
        {
            label: 'Address',
            value: props.additional.employee.address
        }
    ]

    finance.value = [
        {
            label: 'Bank Name',
            value: props.additional.employee.bank_name ?? '-'
        },
        {
            label: 'Account Name',
            value: props.additional.employee.account_name ?? '-'
        },
        {
            label: 'Account Number',
            value: props.additional.employee.account_number
        },
        {
            label: 'BPJS Kesehatan',
            value: props.additional.employee.is_use_bpjsk ? 'Active' : 'Non - Active'
        },
        {
            label: 'BPJS Ketenagakerjaan',
            value: props.additional.employee.is_use_bpjstk ? 'Active' : 'Non - Active'
        },
    ]

    employmentData.value = [
        {
            label: 'Branch',
            value: props.additional.employee.branch_detail.name
        },
        {
            label: 'Employment Status',
            value: props.additional.employee.employment_status_detail ? props.additional.employee.employment_status_detail.name : '-'
        },
        {
            label: 'PTKP Status',
            value: props.additional.employee.ptkp_status_detail ? props.additional.employee.ptkp_status_detail.name : '-'
        },
        {
            label: 'Begin Contract',
            value: dayjs(props.additional.employee.start_date).format('MM/DD/YYYY')
        },
        {
            label: 'End Contract',
            value: props.additional.employee.end_date ? dayjs(props.additional.employee.end_date).format('MM/DD/YYYY') : '-'
        },
        {
            label: 'Payroll Group',
            value: props.additional.employee.payroll_group_detail ? props.additional.employee.payroll_group_detail.name : '-'
        },
        {
            label: 'Direct Manager',
            value: props.additional.employee.manager ? props.additional.employee.manager.user_detail.name : '-'
        },
    ]

    itemSelected = {
        id: props.additional.employee.id,
        user_id: props.additional.employee.user_id,
        employee_name: props.additional.employee.user_detail.name,
        email: props.additional.employee.user_detail.email,
        phone_number: props.additional.employee.phone_number,
        employee_external_id: props.additional.employee.employee_external_id,
        designation_id: props.additional.employee.designation_id,
        role_id: props.additional.employee_role_id[0],
        address: props.additional.employee.address,
        user_device: props.additional.employee.user_detail.user_device,
        previewPicUrl: props.additional.employee_picture,
        bank_name: props.additional.employee.bank_name ?? '',
        account_name: props.additional.employee.account_name ?? '',
        account_number: props.additional.employee.account_number ?? '',
        is_use_bpjsk: props.additional.employee.is_use_bpjsk,
        bpjsk_number_card: props.additional.employee.bpjsk_number_card ?? '',
        bpjsk_setting_id: props.additional.employee.bpjsk_setting_id ?? '',
        bpjsk_use_specific_amount: props.additional.employee.bpjsk_specific_amount_integer ? true : false,
        bpjsk_specific_amount: props.additional.employee.bpjsk_specific_amount_integer ?? '',
        is_use_bpjstk: props.additional.employee.is_use_bpjstk,
        bpjstk_old_age: props.additional.employee.bpjstk_old_age,
        bpjstk_life_insurance: props.additional.employee.bpjstk_life_insurance,
        bpjstk_pension_time: props.additional.employee.bpjstk_pension_time,
        bpjstk_number_card: props.additional.employee.bpjstk_number_card ?? '',
        bpjstk_setting_id: props.additional.employee.bpjstk_setting_id ?? '',
        bpjstk_use_specific_amount: props.additional.employee.bpjstk_specific_amount_integer ? true : false,
        bpjstk_specific_amount: props.additional.employee.bpjstk_specific_amount_integer ?? '',
        branch_id: props.additional.employee.branch_id,
        employment_status_id: props.additional.employee.employment_status_id,
        ptkp_tax_list_id: props.additional.employee.ptkp_tax_list_id,
        start_contract: dayjs(props.additional.employee.start_date),
        end_contract: props.additional.employee.end_date ? dayjs(props.additional.employee.end_date) : null,
        payroll_group_id: props.additional.employee.payroll_group_id ?? '',
        image: props.additional.employee.image,
        manager_id: props.additional.employee.manager_id
    }
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
    <Head :title="title"></Head>
    <VBreadcrumb :routes="breadcrumb" />
    <div class="mb-4 sm:mb-6 flex justify-start space-x-2 items-center">
        <Link :href="route('employment.employee.index')" class="cursor-pointer">
        <VBack width="32" height="32" :is-active="backActive" @mouseover="backActive = true"
            @mouseout="backActive = false" />
        </Link>
        <h1 class="text-2xl md:text-3xl text-slate-800 font-bold">Detail Employee</h1>
    </div>
    <!-- Content -->
    <div class="bg-white shadow-lg rounded-sm mb-8">
        <div class="flex flex-col md:flex-row md:-mr-px">
            <VSidebarSetting :module="additional.menu" />
            <div class="grow">
                <!-- Panel Header -->
                <div class="border-b p-6 flex justify-between">
                    <div class="flex">
                        <img class="w-16 h-16 rounded-full" :src="additional.employee_picture" alt="Employee Picture" />
                        <div class="my-auto ml-6">
                            <div class="font-semibold text-xl text-slate-800">
                                {{ additional.employee.user_detail.name }}
                            </div>
                            <div class="font-medium text-sm text-slate-500">
                                {{ additional.employee.designation_detail.name }}
                            </div>
                        </div>
                    </div>
                    <VButton label="Edit" type="outline-primary" class="my-auto" icon="VEdit" @click="handleEditModalForm"/>
                </div>

                <div class="p-6 space-y-9">
                    <!-- Basic Information -->
                    <section>
                        <div class="font-semibold text-slate-800 text-base mb-4">Basic Information</div>
                        <div class="grid grid-cols-3 gap-6">
                            <div v-for="(data, index) in basicInformation" :key="index">
                                <label class="font-medium text-sm text-slate-600 mb-1">{{ data.label }}</label>
                                <div class="font-normal text-sm text-slate-500">{{ data.value }}</div>
                            </div>
                        </div>
                    </section>
                    
                    <!-- Finance -->
                    <section>
                        <div class="font-semibold text-slate-800 text-base mb-4">Finance</div>
                        <div class="grid grid-cols-3 gap-6">
                            <div v-for="(data, index) in finance" :key="index">
                                <label class="font-medium text-sm text-slate-600 mb-1">{{ data.label }}</label>
                                <div class="font-normal text-sm text-slate-500">{{ data.value }}</div>
                            </div>
                        </div>
                    </section>

                    <!-- Employment Data -->
                    <section>
                        <div class="font-semibold text-slate-800 text-base mb-4">Employment Data</div>
                        <div class="grid grid-cols-3 gap-6">
                            <div v-for="(data, index) in employmentData" :key="index">
                                <label class="font-medium text-sm text-slate-600 mb-1">{{ data.label }}</label>
                                <div class="font-normal text-sm text-slate-500">{{ data.value }}</div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
    <VModalForm :data="itemSelected" :update-action="updateAction" :open-dialog="openModalForm" @close="closeModalForm"
        @successSubmit="successSubmit" :additional="additional" />
</template>