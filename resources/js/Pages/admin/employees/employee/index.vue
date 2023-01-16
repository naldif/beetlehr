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
import VSelect from '@/components/VSelect/index.vue';
import VAlert from '@/components/VAlert/index.vue';
import VDetail from '@/components/src/icons/VDetail.vue';
import VTrash from '@/components/src/icons/VTrash.vue';
import VModalForm from './ModalForm.vue';
import VFilter from './Filter.vue';

const query = ref([])
const searchFilter = ref("");
const filterBranchValue = ref('all');
const filter = ref({});
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
        active: true,
        to: route('employment.employee.index')
    },
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
const updateAction = ref(false)
const itemSelected = ref({})
const openAlert = ref(false)
const openModalForm = ref(false)
const heads = ["Name", "Branch", "Designation", "Email", "Begin Contract", "End Contract", "Role", ""]
const isLoading = ref(true)

const props = defineProps({
    title: string(),
    additional: object(),
})

const getData = debounce(async (page) => {
    axios.get(route('employment.employee.getdata'), {
        params: {
            page: page,
            search: searchFilter.value,
            filter_branch: filterBranchValue.value,
            sort_by_name: filter.value.sort_by_name,
            filter_role: filter.value.filter_role,
            filter_designation: filter.value.filter_designation,
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

const searchHandle = (search) => {
    searchFilter.value = search
    isLoading.value = true
    getData(1)
};

const filterBranch = () => {
    isLoading.value = true
    getData(1)
}

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

const handleAddModalForm = () => {
    updateAction.value = false
    openModalForm.value = true
}

const handleDetailEmployee = (data) => {
    Inertia.visit(route('employment.employee.show', { 'id': data.id }));
}

const successSubmit = () => {
    isLoading.value = true
    getData(pagination.value.current_page)
}

const closeModalForm = () => {
    itemSelected.value = ref({})
    openModalForm.value = false
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

const deleteEmployee = async () => {
    axios.delete(route('employment.employee.delete', { 'id': itemSelected.value.id })
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

onMounted(() => {
    getData(1);
});
</script>

<template>
    <Head :title="props.title" />
    <VBreadcrumb :routes="breadcrumb" />
    <div class="mb-4 sm:mb-6 flex justify-between items-center">
        <h1 class="text-2xl md:text-3xl text-slate-800 font-bold">Employee</h1>
        <VSelect placeholder="Select Branch" v-model="filterBranchValue" :options="additional.branches_filter" class="w-1/6" :clearable="false" @update:modelValue="filterBranch"/>
    </div>
    <div class="bg-white shadow-lg rounded-sm border border-slate-200"
        :class="isLoading && 'min-h-[40vh] sm:min-h-[50vh]'">
        <header class="block justify-between items-center sm:flex py-6 px-4">
            <h2 class="font-semibold text-slate-800">
                All Employee <span class="text-slate-400 !font-medium ml">{{ pagination.total }}</span>
            </h2>
            <div class="mt-3 sm:mt-0 flex space-x-2 sm:justify-between justify-end">
                <!-- Filter -->
                <VFilter @search="searchHandle" @apply="applyFilter" @clear="clearFilter" :additional="additional"/>
                <VButton label="Add Employee" type="primary" @click="handleAddModalForm" class="mt-auto" />
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
                <td class="px-4 whitespace-nowrap h-16"> {{ data.name }} </td>
                <td class="px-4 whitespace-nowrap h-16"> {{ data.branch }} </td>
                <td class="px-4 whitespace-nowrap h-16"> {{ data.designation }} </td>
                <td class="px-4 whitespace-nowrap h-16"> {{ data.email }} </td>
                <td class="px-4 whitespace-nowrap h-16"> {{ data.begin_contract }} </td>
                <td class="px-4 whitespace-nowrap h-16"> {{ data.end_contract }} </td>
                <td class="px-4 whitespace-nowrap h-16"> {{ data.role }} </td>
                <td class="px-4 whitespace-nowrap h-16 text-right">
                    <VDropdownEditMenu class="relative inline-flex r-0" :align="'right'"
                        :last="index === query.length - 1 ? true : false">
                        <li class="cursor-pointer hover:bg-slate-100" @click="handleDetailEmployee(data)">
                            <div class="flex items-center space-x-2 p-3">
                                <span>
                                    <VDetail color="primary" />
                                </span>
                                <span>Detail</span>
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
    <VModalForm :data="itemSelected" :update-action="updateAction" :open-dialog="openModalForm" @close="closeModalForm"
        :additional="additional" @successSubmit="successSubmit" ref="modalForm" />
    <VAlert :open-dialog="openAlert" @closeAlert="closeAlert" @submitAlert="deleteEmployee" type="danger"
        :headerLabel="alertData.headerLabel" :content-label="alertData.contentLabel" :close-label="alertData.closeLabel"
        :submit-label="alertData.submitLabel" />
</template>