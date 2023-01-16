<script setup>
import axios from "axios";
import { notify } from "notiwind";
import { ref, onMounted, reactive } from "vue";
import debounce from "@/composables/debounce"
import { object } from "vue-types";
import VFilter from './Filter.vue';
import VButton from '@/components/VButton/index.vue';
import VAlert from '@/components/VAlert/index.vue';
import VDataTable from '@/components/VDataTable/index.vue';
import VPagination from '@/components/VPagination/index.vue'
import VDropdownEditMenu from '@/components/VDropdownEditMenu/index.vue';
import VEmpty from '@/components/src/icons/VEmpty.vue';
import VTrash from '@/components/src/icons/VTrash.vue';
import VEdit from '@/components/src/icons/VEdit.vue';
import VBadge from '@/components/VBadge/index.vue'
import VLoading from '@/components/VLoading/index.vue';
import VModalForm from './ModalForm.vue';

const query = ref([])
const searchFilter = ref("");
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
const heads = ["NPP Name", "Company Registration Number", "Jaminan Kecelakaan Kerja", "Jaminan Hari Tua", "Jaminan Kematian", "Jaminan Pensiun", "Minimum Value", "Valid Month", "Status", ""]
const isLoading = ref(true)
const filter = ref({})

const props = defineProps({
    additional: object()
})

const getData = debounce(async (page) => {
    axios.get(route('settings.company.bpjs.bpjstk.getdata'), {
        params: {
            page: page,
            search: searchFilter.value,
            sort_minimum_value: filter.value.minimum_value,
            filter_valid_month: filter.value.valid_month,
            filter_active_status: filter.value.active_status,
            filter_inactive_status: filter.value.inactive_status
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

const handleEditModalForm = (data) => {
    updateAction.value = true
    itemSelected.value = data
    openModalForm.value = true
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

const deleteHandle = async () => {
    axios.delete(route('settings.company.bpjs.bpjstk.delete', { 'id': itemSelected.value.id })
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
    if (status === 'active' || status === 'yes') {
        return 'success';
    } else {
        return 'danger';
    }
}

onMounted(() => {
    getData(1);
});
</script>

<template>
    <!-- Table  -->
    <section>
        <header class="block justify-between items-center sm:flex pb-6">
            <h2 class="font-semibold text-slate-800">
                All BPJS Ketenagakerjaan <span class="text-slate-400 !font-medium ml">{{ pagination.total }}</span>
            </h2>
            <div class="mt-3 sm:mt-0 flex space-x-2 sm:justify-between justify-end">
                <!-- Filter -->
                <VFilter @search="searchHandle" @apply="applyFilter" @clear="clearFilter" />
                <VButton label="Add BPJSTK" type="primary" @click="handleAddModalForm" class="mt-auto"/>
            </div>
        </header>
    
        <VDataTable :heads="heads" :isLoading="isLoading" wrapperClass="!px-0">
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
                <td class="px-4 whitespace-nowrap h-16"> {{ data.registration_number ?? '-' }} </td>
                <td class="px-4 whitespace-nowrap h-16 capitalize">
                    <VBadge :text="data.bpjstk_risk_formatted" :color="getTypeStatus(data.bpjstk_risk_formatted)" size="sm" />
                </td>
                <td class="px-4 whitespace-nowrap h-16 capitalize">
                    <VBadge :text="data.old_age_formatted" :color="getTypeStatus(data.old_age_formatted)" size="sm" />
                </td>
                <td class="px-4 whitespace-nowrap h-16 capitalize">
                    <VBadge :text="data.life_insurance_formatted" :color="getTypeStatus(data.life_insurance_formatted)" size="sm" />
                </td>
                <td class="px-4 whitespace-nowrap h-16 capitalize">
                    <VBadge :text="data.pension_time_formatted" :color="getTypeStatus(data.pension_time_formatted)" size="sm" />
                </td>
                <td class="px-4 whitespace-nowrap h-16"> Rp{{ data.minimum_value_formatted }} </td>
                <td class="px-4 whitespace-nowrap h-16">{{ data.valid_month_formatted }}</td>
                <td class="px-4 whitespace-nowrap h-16 capitalize">
                    <VBadge :text="data.status_formatted" :color="getTypeStatus(data.status_formatted)" size="sm" />
                </td>
                <td class="px-4 whitespace-nowrap h-16 text-right">
                    <VDropdownEditMenu class="relative inline-flex r-0" :align="'right'"
                        :last="index === query.length - 1 ? true : false">
                        <li class="cursor-pointer hover:bg-slate-100" @click="handleEditModalForm(data)">
                            <div class="flex items-center space-x-2 p-3">
                                <span>
                                    <VEdit />
                                </span>
                                <span>Edit</span>
                            </div>
                        </li>
                        <li class="cursor-pointer hover:bg-slate-100" @click="alertDelete(data)">
                            <div class="flex items-center space-x-2 p-3">
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
        <div class="py-6">
            <VPagination :pagination="pagination" @next="nextPaginate" @previous="previousPaginate" />
        </div>
    </section>
    <VModalForm :data="itemSelected" :update-action="updateAction" :open-dialog="openModalForm" @close="closeModalForm"
        @successSubmit="successSubmit" :additional="additional" />
    <VAlert :open-dialog="openAlert" @closeAlert="closeAlert" @submitAlert="deleteHandle" type="danger" :headerLabel="alertData.headerLabel" :content-label="alertData.contentLabel" :close-label="alertData.closeLabel" :submit-label="alertData.submitLabel"/>
</template>