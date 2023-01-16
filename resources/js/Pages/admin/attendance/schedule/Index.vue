<script>
export default {
    layout: AppLayout
}
</script>
<script setup>
import axios from "axios";
import dayjs from "dayjs";
import { notify } from "notiwind";
import { Head } from "@inertiajs/inertia-vue3";
import { ref, onMounted, reactive } from "vue";
import debounce from "@/composables/debounce"
import { object, string } from "vue-types";
import AppLayout from '@/layouts/apps.vue';
import VBreadcrumb from '@/components/VBreadcrumb/index.vue';
import VFilter from './Filter.vue';
import VButton from '@/components/VButton/index.vue';
import VAlert from '@/components/VAlert/index.vue';
import VDataTable from '@/components/VDataTable/index.vue';
import VEmpty from '@/components/src/icons/VEmpty.vue';
import VLoading from '@/components/VLoading/index.vue';
import VModalForm from './ModalForm.vue';
import VSelect from '@/components/VSelect/index.vue';

const shiftLists = ref([])
const shiftChoose = ref([])
const query = ref([])
const queryReport = ref([])
const totalReport = ref([])
const searchFilter = ref("");
const filterBranchValue = ref(1);
const filter = ref({})
const chooseValue = ref([])
const chooseSelectHandler = ref()
const today = ref();
const head = ref([])
const headReport = ref([])
const breadcrumb = [
    {
        name: "Dashboard",
        active: false,
        to: route('dashboard.index')
    },
    {
        name: "Attendance",
        active: false,
        to: route('attendance.schedule.index')
    },
    {
        name: "Schedule",
        active: true,
        to: route('attendance.schedule.index')
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
const isLoading = ref(true)
const isRecapLoading = ref(true)

const props = defineProps({
    additional: object(),
    title: string()
})

const getData = debounce(async (page) => {
    axios.get(route('attendance.schedule.getdata'), {
        params: {
            page: page,
            search: searchFilter.value,
            filter_branch: filterBranchValue.value,
            valid_month: filter.value.valid_month,
            shift:filter.value.type
        }
    }).then((res) => {
        query.value = res.data.data.final.data
        head.value = res.data.data.head.data

        chooseValue.value = res.data.data.final.data
        today.value = filter.value.valid_month ? new Date(filter.value.valid_month) : new Date()
        pagination.value = res.data.meta.pagination
    }).catch((res) => {
        const result = res.response.data
        const metaError = res.response.data.meta.error
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

    getReport()
}, 500);

const getShiftOptions = () => {
    axios.get(route('attendance.schedule.getshiftOptions'), {
        params: {
            branch_id: filterBranchValue.value
        }
    }).then((res) => {
        shiftChoose.value = res.data.choose
        shiftLists.value = res.data.shift_list
    }).catch((res) => {
        const result = res.response.data
        const metaError = res.response.data.meta.error
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
    })
};

const getReport = debounce(async () => {
    axios.get(route('attendance.schedule.getreport', {
        search: searchFilter.value,
        filter_branch: filterBranchValue.value,
        valid_month: filter.value.valid_month,
        shift:filter.value.type
    }))
        .then((res) => {
            headReport.value =  res.data.data.headReport.data
            queryReport.value = res.data.data.report.data
            totalReport.value = res.data.data.total_count.data

        }).catch((res) => {
            const result = res.response.data
            const metaError = res.response.data.meta.error
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
        }).finally(() => isRecapLoading.value = false)
}, 500);

const searchHandle = (search) => {
    searchFilter.value = search
    isLoading.value = true
    getData()
};

const applyFilter = (data) => {
    filter.value = data
    isLoading.value = true
    getData()
}

const clearFilter = (data) => {
    filter.value = data
    isLoading.value = true
    getData()
}

const handleAddModalForm = () => {
    updateAction.value = false
    openModalForm.value = true
}

const handleChangeSchedule = (user_id, date, type) => {
    const data = {
        date: dayjs(today.value).get('year') + '-' + (dayjs(today.value).get('month') + 1) + '-' + date,
        type: type
    }

    console.log(data)

    updateSchedule(user_id, data)
    getData()
}

const updateSchedule = async (user_id, data) => {
    axios.post(route('attendance.schedule.update', { 'user_id': user_id }), data)
        .then((res) => {
            if (res.data.meta.success == false) {
                notify({
                    type: "error",
                    group: "top",
                    text: res.data.meta.message
                }, 2500)
            } else {
                notify({
                    type: "success",
                    group: "top",
                    text: res.data.meta.message
                }, 2500)
            }

            chooseSelectHandler.value.clearSelected()
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

const successSubmit = () => {
    isLoading.value = true
    getData(pagination.value.current_page)
}

const closeModalForm = () => {
    itemSelected.value = ref({})
    openModalForm.value = false
}


const closeAlert = () => {
    itemSelected.value = ref({})
    openAlert.value = false
}


const filterBranch = () => {
    isLoading.value = true
    getData()
    getShiftOptions()
}


onMounted(() => {
    getData();
    getShiftOptions();
});
</script>

<template>

    <Head :title="title"></Head>
    <VBreadcrumb :routes="breadcrumb" />
    <div class="mb-4 sm:mb-6 flex justify-between">
        <h1 class="text-2xl md:text-3xl text-slate-800 font-bold">Schedule</h1>
        <VSelect placeholder="Select Branch" v-model="filterBranchValue" :options="additional.branch_list" class="w-1/6"
            :clearable="false" @update:modelValue="filterBranch" />
    </div>
    <!-- Content -->
    <div class="bg-white shadow-lg rounded-sm mb-8 relative">
        <div class="grow">
            <!-- Table  -->
            <section class="py-6 px-4">
                <header class="block justify-between items-center sm:flex py-6">
                    <h2 class="font-semibold text-slate-800">
                        All schedule
                    </h2>
                    <div class="mt-3 sm:mt-0 flex space-x-2 sm:justify-between justify-end">
                        <!-- Filter -->
                        <VFilter @search="searchHandle" @apply="applyFilter" @clear="clearFilter" :additional="additional"
                            :shiftList="shiftLists" />
                        <VButton label="Generate schedule" type="primary" @click="handleAddModalForm" class="mt-auto" />
                    </div>
                </header>
                <div v-if="isLoading">
                    <VLoading />
                </div>
                <VDataTable head-center :heads="head" v-if="!isLoading" wrapperClass="!px-0" :freezeTable="true">
                    <tr v-if="query.length === 0 && !isLoading">
                        <td class="overflow-hidden my-2" :colspan="32">
                            <div class="flex items-center flex-col w-full my-32">
                                <VEmpty />
                                <div class="mt-9 text-slate-500 text-xl md:text-xl font-medium">Result not
                                    found.</div>
                            </div>
                        </td>
                    </tr>
                    <tr v-for="(data, index) in query" :key="index" v-else>
                        <td class="px-4 whitespace-nowrap  bg-white h-16 w-56 font-bold fixed-left"> {{
                            data.name
                            }} </td>
                        <td v-for="(schedule, index2) in data.date" :key="index2" class="px-4 whitespace-nowrap h-16">
                            <VSelect placeholder="" ref="chooseSelectHandler" v-model="chooseValue[index].date[index2]"
                                :options="shiftChoose" class="w-40 !z-0" :clearable="false"
                                @update:modelValue="handleChangeSchedule(index, index2, $event)"
                                :disabled="dayjs(`${today.getFullYear()}-${today.getMonth() + 1}-${index2}`).format('YYYY-MM-DD') < dayjs().format('YYYY-MM-DD') ? true : false" />
                        </td>
            
                    </tr>
                </VDataTable>
            </section>
        </div>
        <!-- </div> -->
    </div>

    <!-- Content 2 (Report) -->
    <div class="bg-white shadow-lg rounded-sm mb-8">
        <div class="flex flex-col md:flex-row md:-mr-px">
            <div class="grow">
                <div class="pt-6 px-4">
                    <div class="font-semibold text-xl text-slate-800">Recap </div>
                </div>
            </div>
        </div>
        <!-- Table -->
        <section class="py-6 px-4">
            <div v-if="isRecapLoading">
                <VLoading />
            </div>
            <VDataTable head-center :heads="headReport" v-if="!isRecapLoading" wrapperClass="!px-0" :freezeTable="true">
                <tr v-if="queryReport.length === 0 && !isRecapLoading">
                    <td class="overflow-hidden my-2" :colspan="32">
                        <div class="flex items-center flex-col w-full my-32">
                            <VEmpty />
                            <div class="mt-9 text-slate-500 text-xl md:text-xl font-medium">Result not
                                found.</div>
                        </div>
                    </td>
                </tr>
                <tr v-for="(data, index) in queryReport" :key="index" v-else>
                    <td class="px-4 whitespace-nowrap  bg-white w-56 font-bold capitalize fixed-left"> {{ index
                    }} </td>
                    <td v-for="(report, index2) in data" :key="index2" class="px-4 whitespace-nowrap h-16 bg-white w-56"> {{ report
                    }} </td>
                </tr>
                <tr>
                    <td class="px-4 whitespace-nowrap h-16 bg-white w-56 font-bold capitalize fixed-left"> Total
                    </td>
                    <td v-for="(totalReport, index3) in totalReport" :key="index3"
                        class="px-4 whitespace-nowrap h-16 bg-white w-56 font-semibold"> {{ totalReport }}
                    </td>
                </tr>
            </VDataTable>
        </section>
    </div>

    <VModalForm :data="itemSelected" :update-action="updateAction" :open-dialog="openModalForm" @close="closeModalForm"
        @successSubmit="successSubmit" :additional="additional" :branch="filterBranchValue" :shiftList="shiftLists"/>
    <VAlert :open-dialog="openAlert" @closeAlert="closeAlert" type="danger" :headerLabel="alertData.headerLabel"
        :content-label="alertData.contentLabel" :close-label="alertData.closeLabel"
        :submit-label="alertData.submitLabel" />
</template>