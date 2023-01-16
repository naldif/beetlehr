<script>
export default {
    layout: AppLayout
}
</script>
<script setup>
import axios from "axios";
import { notify } from "notiwind";
import { Inertia } from '@inertiajs/inertia'
import { object, string } from "vue-types";
import { Head } from "@inertiajs/inertia-vue3";
import { ref, onMounted, reactive } from "vue";
import AppLayout from '@/layouts/apps.vue';
import debounce from "@/composables/debounce"
import VDropdownEditMenu from '@/components/VDropdownEditMenu/index.vue';
import VDataTable from '@/components/VDataTable/index.vue';
import VPagination from '@/components/VPagination/index.vue'
import VBreadcrumb from '@/components/VBreadcrumb/index.vue';
import VLoading from '@/components/VLoading/index.vue';
import VEmpty from '@/components/src/icons/VEmpty.vue';
import VButton from '@/components/VButton/index.vue';
import VBadge from '@/components/VBadge/index.vue';
import VAlert from '@/components/VAlert/index.vue';
import VDetail from '@/components/src/icons/VDetail.vue';
import VTrash from '@/components/src/icons/VTrash.vue';
import VFilter from './Filter.vue';
import VPaidForm from './ModalPaidForm.vue';

const query = ref([])
const searchFilter = ref("");
const filter = ref({});
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
        active: true,
        to: route('payroll.run.payrollemployee', { 'id': props.additional.payroll_slip_id })
    }
]
const pagination = ref({
    count: '',
    current_page: 1,
    per_page: '',
    total: 0,
    total_pages: 1
})
const alertData = reactive({
    headerLabel: '',
    contentLabel: '',
    closeLabel: '',
    submitLabel: '',
})
const itemSelected = ref({})
const openAlert = ref(false)
const heads = ["Branch", "Employee", "Salary", "Status", ""]
const isLoading = ref(true)
const openModalForm = ref(false)

const props = defineProps({
    title: string(),
    additional: object(),
})

const getData = debounce(async (page) => {
    axios.get(route('payroll.run.getpayrollemployeedata', { 'id': props.additional.payroll_slip_id }), {
        params: {
            page: page,
            search: searchFilter.value
        }
    }).then((res) => {
        query.value = res.data.data
        pagination.value = res.data.meta.pagination
    }).catch((res) => {
        notify({
            type: "error",
            group: "top",
            text: res.response.data.message
        }, 2500)
    }).finally(() => isLoading.value = false)
}, 500);

const nextPaginate = () => {
    pagination.value.current_page += 1
    isLoading.value = true
    getData(pagination.value.current_page)
}

const previousPaginate = () => {
    pagination.value.current_page -= 1
    isLoading.value = true
    getData(pagination.value.current_page)
}

const successSubmit = () => {
    isLoading.value = true
    getData(pagination.value.current_page)
}

const searchHandle = (search) => {
    searchFilter.value = search
    isLoading.value = true
    getData(1)
};

const applyFilter = (data) => {
    filter.value = data
    isLoading.value = true
    getData(1)
}

const clearFilter = (data) => {
    filter.value = data
    isLoading.value = true
    getData(1)
}

const handleDetailPage = (data) => {
    Inertia.visit(route('payroll.run.payrollemployee.detail', { 'id': data.id }));
}

const alertDelete = (data) => {
    itemSelected.value = data
    openAlert.value = true
    alertData.headerLabel = 'Are you sure to delete this?'
    alertData.contentLabel = "You won't be able to revert this!"
    alertData.closeLabel = 'Cancel'
    alertData.submitLabel = 'Delete'
}

const closeAlert = () => {
    itemSelected.value = ref({})
    openAlert.value = false
}

const deleteDataHandle = async () => {
    axios.delete(route('payroll.run.payrollemployee.delete', { 'id': itemSelected.value.id })
    ).then((res) => {
        notify({
            type: "success",
            group: "top",
            text: res.data.meta.message
        }, 2500)
        openAlert.value = false
        isLoading.value = true
        getData(pagination.value.current_page)
    }).catch((res) => {
        notify({
            type: "error",
            group: "top",
            text: res.response.data.message
        }, 2500)
    })
};

const getTypeStatus = (status) => {
    if (status === 'Generated') {
        return 'warning';
    } else {
        return 'success';
    }
}

onMounted(() => {
    getData(1);
});
</script>

<template>
    <Head :title="props.title" />
    <VBreadcrumb :routes="breadcrumb" />
    <div class="mb-4 sm:mb-6 flex justify-between items-center">
        <h1 class="text-2xl md:text-3xl text-slate-800 font-bold">Payroll Employee</h1>
    </div>
    <div class="bg-white shadow-lg rounded-sm border border-slate-200"
        :class="isLoading && 'min-h-[40vh] sm:min-h-[50vh]'">
        <header class="block justify-between items-center sm:flex py-6 px-4">
            <h2 class="font-semibold text-slate-800">
                All Payroll Employees <span class="text-slate-400 !font-medium ml">{{ pagination.total }}</span>
            </h2>
            <div class="mt-3 sm:mt-0 flex space-x-2 sm:justify-between justify-end">
                <!-- Filter -->
                <VFilter @search="searchHandle" @apply="applyFilter" @clear="clearFilter" :additional="additional" />
                <VButton label="Paid All" type="primary" class="mt-auto" @click="openModalForm = true"/>
            </div>
        </header>

        <VDataTable :heads="heads" :isLoading="isLoading">
            <tr v-if="isLoading">
                <td class="h-[100%] overflow-hidden my-2" :colspan="heads.length">
                    <VLoading />
                </td>
            </tr>
            <tr v-else-if="query.length === 0 && !isLoading">
                <td class="overflow-hidden my-2" :colspan="heads.length">
                    <div class="flex items-center flex-col w-full my-32">
                        <VEmpty />
                        <div class="mt-9 text-slate-500 text-xl md:text-xl font-medium">Result not found.</div>
                    </div>
                </td>
            </tr>
            <tr v-for="(data, index) in query" :key="index" v-else>
                <td class="px-4 whitespace-nowrap h-16"> {{ data.branch_name }} </td>
                <td class="px-4 whitespace-nowrap h-16"> {{ data.employee_name }} </td>
                <td class="px-4 whitespace-nowrap h-16"> Rp{{ data.total_amount }} </td>
                <td class="px-4 whitespace-nowrap h-16"> 
                    <VBadge :text="data.status" :color="getTypeStatus(data.status)" size="sm" />
                </td>
                <td class="px-4 whitespace-nowrap h-16 text-right">
                    <VDropdownEditMenu class="relative inline-flex r-0" :align="'right'"
                        :last="index === query.length - 1 ? true : false">
                        <li class="cursor-pointer hover:bg-slate-100" @click="handleDetailPage(data)">
                            <div class="flex items-center space-x-2 p-3">
                                <span>
                                    <VDetail color="primary" />
                                </span>
                                <span>Show</span>
                            </div>
                        </li>
                        <li class="cursor-pointer hover:bg-slate-100">
                            <div class="flex justify-between items-center space-x-2 p-3" @click="alertDelete(data)">
                                <span>
                                    <VTrash color="danger" />
                                </span>
                                <span>Delete</span>
                            </div>
                        </li>
                    </VDropdownEditMenu>
                </td>
            </tr>
        </VDataTable>
        <div class="px-4 py-6">
            <VPagination :pagination="pagination" @next="nextPaginate" @previous="previousPaginate" />
        </div>
    </div>
    <VAlert :open-dialog="openAlert" @closeAlert="closeAlert" @submitAlert="deleteDataHandle" type="danger"
        :headerLabel="alertData.headerLabel" :content-label="alertData.contentLabel" :close-label="alertData.closeLabel"
        :submit-label="alertData.submitLabel" />
    <VPaidForm :open-dialog="openModalForm" @close="openModalForm = false" @successSubmit="successSubmit" :additional="additional"/>
</template>