<script>
export default {
    layout: AppLayout
}
</script>
<script setup>
import axios from "axios";
import dayjs from "dayjs";
import { notify } from "notiwind";
import { Head, Link } from "@inertiajs/inertia-vue3";
import { ref, onMounted } from "vue";
import { object, string } from "vue-types";
import AppLayout from '@/layouts/apps.vue';
import debounce from "@/composables/debounce"
import VBack from '@/components/src/icons/VBack.vue'
import VBreadcrumb from '@/components/VBreadcrumb/index.vue';
import VSidebarSetting from '@/components/VSidebarSetting/index.vue';
import VDataTable from '@/components/VDataTable/index.vue';
import VPagination from '@/components/VPagination/index.vue'
import VFilter from './Filter.vue';
import VLoading from '@/components/VLoading/index.vue';
import VBadge from '@/components/VBadge/index.vue';
import VEmpty from '@/components/src/icons/VEmpty.vue';
import VPresent from '@/components/src/icons/VPresent.vue';
import VHoliday from '@/components/src/icons/VHoliday.vue';
import VLeave from '@/components/src/icons/VLeave.vue';
import VLate from '@/components/src/icons/VLate.vue';
import VAbsent from '@/components/src/icons/VAbsent.vue';
import VClockoutEarly from '@/components/src/icons/VClockoutEarly.vue';

const props = defineProps({
    additional: object(),
    title: string()
})

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
        name: "Attendance Log",
        active: true,
        to: route('employment.employee.attendance-log.index', { 'id': props.additional.employee.id })
    },
]
const pagination = ref({
    count: '',
    current_page: 1,
    per_page: '',
    total: 0,
    total_pages: 1
})
const attendanceOverview = ref([
    {
        label: 'Present',
        value: 0,
        work_hours: '00:00:00',
        type: 'present'
    },
    {
        label: 'Absent',
        value: 0,
        work_hours: null,
        type: 'absent'
    },
    {
        label: 'Late',
        value: 0,
        work_hours: '01:00:00',
        type: 'late'
    },
    {
        label: 'Clockout Early',
        value: 0,
        work_hours: '09:00:00',
        type: 'clockout_early'
    },
]);
const backActive = ref(false)
const query = ref([])
const searchFilter = ref("");
const filter = ref({})
const dateFilter = ref([
    dayjs().startOf("month").toISOString(), dayjs().toISOString()
])
const heads = ["Date", "Clock In", "Clock Out", "Work Hours", "Status"]
const isLoading = ref(true)
const overviewLoading = ref(true)

const getData = debounce(async (page) => {
    axios.get(route('employment.employee.attendance-log.getdata', { 'id': props.additional.employee.id }), {
        params: {
            page: page,
            search: searchFilter.value,
            filter_date: dateFilter.value,
            filter_status: filter.value.filter_status
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

const getAttendanceOverviewData = debounce(async (page) => {
    axios.get(route('employment.employee.attendance-log.getdataoverview', { 'id': props.additional.employee.id }), {
        params: {
            page: page,
            filter_date: dateFilter.value
        }
    }).then((res) => {
        let present = attendanceOverview.value.find(e => e.type === 'present')
        let absent = attendanceOverview.value.find(e => e.type === 'absent')
        let late = attendanceOverview.value.find(e => e.type === 'late')
        let clockout_early = attendanceOverview.value.find(e => e.type === 'clockout_early')

        present.value = res.data.data.total_present
        present.work_hours = res.data.data.present_work_hours
        absent.value = res.data.data.total_absent
        late.value = res.data.data.total_late
        late.work_hours = res.data.data.late_work_hours
        clockout_early.value = res.data.data.total_clockout_early,
        clockout_early.work_hours = res.data.data.clockout_early_work_hours
    }).catch((res) => {
        notify({
            type: "error",
            group: "top",
            text: res.response.data.message
        }, 2500)
    }).finally(() => overviewLoading.value = false)
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

const getTypeStatus = (status) => {
    if (status === 'present') {
        return 'primary-solid';
    } else if(status === 'holiday'){
        return 'yellow-solid';
    } else if (status === 'leave') {
        return 'pink-solid';
    } else if (status === 'late') {
        return 'amber-solid';
    } else if (status === 'absent') {
        return 'red-solid';
    } else if (status === 'clockout_early') {
        return 'cyan-solid';
    } else {
        return 'primary';
    }
}

const getIconStatus = (status) => {
    if (status === 'present') {
        return VPresent;
    } else if (status === 'holiday') {
        return VHoliday;
    } else if (status === 'leave') {
        return VLeave;
    } else if (status === 'late') {
        return VLate;
    } else if (status === 'absent') {
        return VAbsent;
    } else if (status === 'clockout_early') {
        return VClockoutEarly;
    } else {
        return '';
    }
}

onMounted(() => {
    getAttendanceOverviewData()
    getData(1)
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
        <div class="flex flex-col sm:flex-row md:-mr-px">
            <VSidebarSetting :module="additional.menu" />
            <div class="grow">
                <div class="p-4">
                    <div class="flex justify-between">
                        <div class="font-semibold text-xl text-slate-800 mb-5">
                            Attendance Overview
                        </div>
                        <Datepicker v-model="dateFilter" range position="right" :enableTimePicker="false"
                            :clearable="false"
                            @update:modelValue="isLoading = true, overviewLoading = true, getData(1), getAttendanceOverviewData()" />
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div class="rounded border py-4 pr-4 flex" v-for="(data, index) in attendanceOverview"
                            :key="index">
                            <div class="w-1.5 rounded-r mr-4" :class="{
                                'bg-blue-500': data.type === 'present',
                                'bg-red-500': data.type === 'absent',
                                'bg-amber-500': data.type === 'late',
                                'bg-cyan-600': data.type === 'clockout_early'
                            }"></div>
                            <div class="text-slate-800">
                                <div class="font-medium text-base">
                                    {{ data.label }}
                                </div>
                                <div v-if="overviewLoading" class="mt-1">
                                    <VLoading />
                                </div>
                                <div v-else>
                                    <div class="font-semibold text-3xl">
                                        {{ data.value }}
                                    </div>
                                    <div class="font-medium text-sm !text-slate-500" v-if="data.work_hours">
                                        {{ data.work_hours }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Table  -->
                    <section>
                        <header class="block justify-between items-center sm:flex pt-8 pb-5">
                            <h2 class="font-semibold text-slate-800">
                                All Attendance Log <span class="text-slate-400 !font-medium ml">{{ pagination.total
                                }}</span>
                            </h2>
                            <div class="mt-3 sm:mt-0 flex space-x-2 sm:justify-between justify-end">
                                <!-- Filter -->
                                <VFilter @search="searchHandle" @apply="applyFilter" @clear="clearFilter"/>
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
                                        <div class="mt-9 text-slate-500 text-xl md:text-xl font-medium">Result not
                                            found.</div>
                                    </div>
                                </td>
                            </tr>
                            <tr v-for="(data, index) in query" :key="index" v-else>
                                <td class="px-4 whitespace-nowrap h-16"> {{ data.date_formatted }} </td>
                                <td class="px-4 whitespace-nowrap h-16"> {{ data.clock_in }} </td>
                                <td class="px-4 whitespace-nowrap h-16"> {{ data.clock_out }} </td>
                                <td class="px-4 whitespace-nowrap h-16"> {{ data.work_hours }} </td>
                                <td class="px-4 whitespace-nowrap h-16 capitalize"> 
                                    <VBadge :text="data.status.replace(/_/g, ' ')" :color="getTypeStatus(data.status)" :icon="getIconStatus(data.status)" size="sm" />
                                </td>
                            </tr>
                        </VDataTable>
                        <div class="py-6">
                            <VPagination :pagination="pagination" @next="nextPaginate" @previous="previousPaginate" />
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</template>