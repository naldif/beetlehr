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
import { ref, onMounted } from "vue";
import debounce from "@/composables/debounce"
import { object, string } from "vue-types";
import AppLayout from '@/layouts/apps.vue';
import VBreadcrumb from '@/components/VBreadcrumb/index.vue';
import VLoading from '@/components/VLoading/index.vue';
import VDataTable from '@/components/VDataTable/index.vue';
import VSelect from '@/components/VSelect/index.vue';
import VPresent from '@/components/src/icons/solid/VPresent.vue';
import VLate from '@/components/src/icons/solid/VLate.vue';
import VAbsent from '@/components/src/icons/solid/VAbsent.vue';
import VClockoutEarly from '@/components/src/icons/solid/VClockoutEarly.vue';
import VLeave from '@/components/src/icons/solid/VLeave.vue';
import VEmpty from '@/components/src/icons/VEmpty.vue';
import VHoliday from '@/components/src/icons/solid/VHoliday.vue';
import VUnassign from '@/components/src/icons/solid/VUnassign.vue';
import VModalForm from './ModalForm.vue'

const itemSelected = ref({})
const openModalForm = ref(false)
const attendanceQuery = ref([])
const attendanceRecapQuery = ref([])
const attendanceListHeader = ref([])
const filterBranchValue = ref(1);
const breadcrumb = [
    {
        name: "Dashboard",
        active: false,
        to: route('dashboard.index')
    },
    {
        name: "Attendance",
        active: false,
        to: route('attendance.attendance-overview.index')
    },
    {
        name: "Attendance",
        active: true,
        to: route('attendance.attendance-overview.index')
    },
]
const overviewLoading = ref(true)
const attendanceListLoading = ref(true)
const attendanceRecapLoading = ref(true)
const filter = ref({
    month: {
        month: dayjs().get('month'),
        year: dayjs().get('year')
    },
    currentMonth: dayjs().format('MMMM YYYY')
})
const attendanceOverview = ref([
    {
        label: 'Present',
        value: 0,
        type: 'present'
    },
    {
        label: 'Late',
        value: 0,
        type: 'late'
    },
    {
        label: 'Absent',
        value: 0,
        type: 'absent'
    },
    {
        label: 'Clockout Early',
        value: 0,
        type: 'clockout_early'
    },
    {
        label: 'Leave',
        value: 0,
        type: 'leave'
    },
    {
        label: 'Holiday',
        value: 0,
        type: 'holiday'
    },
]);

const props = defineProps({
    additional: object(),
    title: string()
})

const handleDate = () => {
    if (filter.value.month) {
        const date = new Date(filter.value.month.year, filter.value.month.month, 1);
        const startDate = dayjs(date).format('YYYY-MM-DD');
        filter.value.filtermonth = startDate
        filter.value.currentMonth = dayjs(date).format('MMMM YYYY')
    }

    initData()
}

const getAttendanceOverviewData = debounce(async (page) => {
    axios.get(route('attendance.attendance-overview.getdataoverview'), {
        params: {
            filter_date: filter.value.filtermonth,
            filter_branch: filterBranchValue.value
        }
    }).then((res) => {
        let present = attendanceOverview.value.find(e => e.type === 'present')
        let absent = attendanceOverview.value.find(e => e.type === 'absent')
        let late = attendanceOverview.value.find(e => e.type === 'late')
        let clockout_early = attendanceOverview.value.find(e => e.type === 'clockout_early')
        let leave = attendanceOverview.value.find(e => e.type === 'leave')
        let holiday = attendanceOverview.value.find(e => e.type === 'holiday')

        present.value = res.data.data.total_present
        absent.value = res.data.data.total_absent
        late.value = res.data.data.total_late
        clockout_early.value = res.data.data.total_clockout_early
        leave.value = res.data.data.total_leaves
        holiday.value = res.data.data.total_holiday
    }).catch((res) => {
        notify({
            type: "error",
            group: "top",
            text: res.response.data.message
        }, 2500)
    }).finally(() => overviewLoading.value = false)
}, 500);

const getAttendanceListData = debounce(async (page) => {
    axios.get(route('attendance.attendance-overview.getdata'), {
        params: {
            filter_date: filter.value.filtermonth,
            filter_branch: filterBranchValue.value
        }
    }).then((res) => {
        attendanceQuery.value = res.data
    }).catch((res) => {
        notify({
            type: "error",
            group: "top",
            text: res.response.data.message
        }, 2500)
    }).finally(() => attendanceListLoading.value = false)
}, 500);

const getAttendanceRecapData = debounce(async (page) => {
    axios.get(route('attendance.attendance-overview.getdatarecap'), {
        params: {
            filter_date: filter.value.filtermonth,
            filter_branch: filterBranchValue.value
        }
    }).then((res) => {
        console.log(res.data)
        attendanceRecapQuery.value = res.data
    }).catch((res) => {
        notify({
            type: "error",
            group: "top",
            text: res.response.data.message
        }, 2500)
    }).finally(() => attendanceRecapLoading.value = false)
}, 500);

const getAttendanceListHeader = debounce(async (page) => {
    axios.get(route('attendance.attendance-overview.getattendanceheader'), {
        params: {
            filter_date: filter.value.filtermonth
        }
    }).then((res) => {
        attendanceListHeader.value = res.data
        getAttendanceListData()
    }).catch((res) => {
        notify({
            type: "error",
            group: "top",
            text: res.response.data.message
        }, 2500)
    })
}, 500);

const filterBranch = () => {
    initData()
}

const initData = () => {
    overviewLoading.value = true
    attendanceListLoading.value = true
    attendanceRecapLoading.value = true
    getAttendanceOverviewData()
    getAttendanceListHeader()
    getAttendanceRecapData()
}

const closeModalForm = () => {
    itemSelected.value = ref({})
    openModalForm.value = false
}

onMounted(() => {
    initData()
});
</script>

<template>
    <Head :title="title"></Head>
    <VBreadcrumb :routes="breadcrumb" />
    <div class="mb-4 sm:mb-6 flex justify-between">
        <h1 class="text-2xl md:text-3xl text-slate-800 font-bold">Attendance</h1>
        <VSelect placeholder="Select Branch" v-model="filterBranchValue" :options="additional.branch_list" class="w-1/6" :clearable="false" @update:modelValue="filterBranch" />
    </div>

    <!-- Attendance Overview -->
    <div class="bg-white shadow-lg rounded-md mb-8 p-4">
        <div class="mb-5 flex justify-between items-center">
            <h1 class="text-xl text-slate-800 font-semibold">Attendance Overview</h1>
            <div class="inline-flex space-x-2">
                <!-- <VButton label="Export" type="outline-primary" class="my-auto" /> -->
                <Datepicker v-model="filter.month" @update:modelValue="handleDate" month-picker :enableTimePicker="false" position="left"
                    :clearable="false" format="MMMM yyyy" previewFormat="MMMM yyyy" placeholder="Select Active Month"/>
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-6">
            <div class="rounded border py-4 pr-4 flex" v-for="(data, index) in attendanceOverview" :key="index">
                <div class="w-1.5 rounded-r mr-4" :class="{
                    'bg-blue-500': data.type === 'present',
                    'bg-red-500': data.type === 'absent',
                    'bg-amber-500': data.type === 'late',
                    'bg-cyan-600': data.type === 'clockout_early',
                    'bg-pink-500': data.type === 'leave',
                    'bg-yellow-400': data.type === 'holiday'
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
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance List -->
    <div class="bg-white shadow-lg rounded-sm mb-8">
        <div class="flex flex-col md:flex-row md:-mr-px">
            <div class="grow">
                <div class="py-6 px-4">
                    <div class="font-semibold text-xl text-slate-800">All Attendance </div>
                    <div class="flex justify-between">
                        <div class="text-slate-500 font-medium text-sm"> {{ filter.currentMonth }} </div>
                        <div class="inline-flex space-x-2 text-slate-500 font-normal text-sm">
                            <div class="inline-flex">
                                <VPresent class="my-auto mr-1"/>
                                Present
                            </div>
                            <div class="inline-flex">
                                <VLate class="my-auto mr-1"/>
                                Late
                            </div>
                            <div class="inline-flex">
                                <VAbsent class="my-auto mr-1"/>
                                Absent
                            </div>
                            <div class="inline-flex">
                                <VClockoutEarly class="my-auto mr-1"/>
                                Clockout Early
                            </div>
                            <div class="inline-flex">
                                <VLeave class="my-auto mr-1" />
                                Leave
                            </div>
                            <div class="inline-flex">
                                <VHoliday class="my-auto mr-1" />
                                Holiday
                            </div>
                            <div class="inline-flex">
                                <VUnassign class="my-auto mr-1" />
                                Unassign
                            </div>
                        </div>  
                    </div>
                </div>
            </div>
        </div>
        <!-- Table -->
        <section class="py-6 px-4">
            <div v-if="attendanceListLoading">
                <VLoading />
            </div>
            <VDataTable :heads="attendanceListHeader" v-if="!attendanceListLoading" wrapperClass="!px-0" :freezeTable="true">
                <tr v-if="attendanceQuery.length === 0 && !attendanceListLoading">
                    <td class="overflow-hidden my-2" :colspan="attendanceListHeader.length">
                        <div class="flex items-center flex-col w-full my-32">
                            <VEmpty />
                            <div class="mt-9 text-slate-500 text-xl md:text-xl font-medium">Result not
                                found.</div>
                        </div>
                    </td>
                </tr>
                <tr v-for="(data, index) in attendanceQuery" :key="index" v-else>
                    <td class="px-4 whitespace-nowrap h-16 fixed-left"> {{data.employee_name}} </td>
                    <td class="px-4 whitespace-nowrap h-16" v-for="(attendanceData, attendanceIndex) in data.attendances" :key="attendanceIndex"> 
                        <div v-if="attendanceData.status === 'netral'">
                            -
                        </div>
                        <div v-else class="p-1 border-2 rounded-lg border-slate-200" 
                            :class="{'cursor-pointer': attendanceData.status === 'present' || attendanceData.status === 'late' || attendanceData.status === 'clockout_early'}">
                            <VHoliday v-if="attendanceData.status === 'holiday'"/>
                            <VPresent v-else-if="attendanceData.status === 'present'" @click="openModalForm = true, itemSelected = attendanceData"/>
                            <VLate v-else-if="attendanceData.status === 'late'" @click="openModalForm = true, itemSelected = attendanceData"/>
                            <VAbsent v-else-if="attendanceData.status === 'absent'" />
                            <VClockoutEarly v-else-if="attendanceData.status === 'clockout_early'" @click="openModalForm = true, itemSelected = attendanceData" />
                            <VLeave v-else-if="attendanceData.status === 'leave'" />
                            <VUnassign v-else-if="attendanceData.status === 'unassigned'" />
                        </div>
                    </td>
                </tr>
            </VDataTable>
        </section>
    </div>

    <!-- Attendance Recap -->
    <div class="bg-white shadow-lg rounded-sm mb-8">
        <div class="flex flex-col md:flex-row md:-mr-px">
            <div class="grow">
                <div class="py-6 px-4">
                    <div class="font-semibold text-xl text-slate-800">Recap </div>
                    <div class="text-slate-500 font-medium text-sm"> {{ filter.currentMonth }} </div>
                </div>
            </div>
        </div>
        <!-- Table -->
        <section class="py-6 px-4">
            <div v-if="attendanceRecapLoading">
                <VLoading />
            </div>
            <VDataTable :heads="attendanceListHeader" v-if="!attendanceRecapLoading" wrapperClass="!px-0"
                :freezeTable="true">
                <tr v-if="attendanceRecapQuery.length === 0 && !attendanceRecapLoading">
                    <td class="overflow-hidden my-2" :colspan="attendanceListHeader.length">
                        <div class="flex items-center flex-col w-full my-32">
                            <VEmpty />
                            <div class="mt-9 text-slate-500 text-xl md:text-xl font-medium">Result not
                                found.</div>
                        </div>
                    </td>
                </tr>
                <tr v-for="(data, index) in attendanceRecapQuery" :key="index" v-else>
                    <td class="px-4 whitespace-nowrap h-16 fixed-left capitalize"> {{data.status.replace(/_/g, ' ')}} </td>
                    <td class="px-4 whitespace-nowrap h-16" v-for="(recapData, recapIndex) in data.recaps" :key="recapIndex">
                        {{ recapData.total_recap }}
                    </td>
                </tr>
            </VDataTable>
        </section>
    </div>
    <VModalForm :data="itemSelected" :open-dialog="openModalForm" @close="closeModalForm" />
</template>

<style scoped>
.dp__main.dp__theme_light {
    width: 168px;
}
</style>