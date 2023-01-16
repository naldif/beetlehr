<script setup>
import { ref } from "vue";
import { bool, object } from "vue-types";
import VDialog from '@/components/VDialog/index.vue';
import VBadge from '@/components/VBadge/index.vue';
import VButton from '@/components/VButton/index.vue';

const props = defineProps({
    openDialog: bool(),
    data: object().def({})
})

const attendanceInformation = ref({});
const timeInformation = ref({});
const clockinInformation = ref({});
const clockoutInformation = ref({});

const openForm = () => {
    initData()
}

const initData = () => {
    attendanceInformation.value = {
        'Date': props.data.date,
        'Name': props.data.user_name,
        'Type': props.data.status_formatted,
        'Force Clockout': props.data.is_force_clock_out
    }

    timeInformation.value = {
        'Clock In': props.data.clock_in,
        'Clock Out': props.data.clock_out,
        'Schedule Start': props.data.schedule_start,
        'Schedule End': props.data.schedule_end,
        'Total Late': props.data.total_late,
        'Total Clockout Early': props.data.total_clock_out_early,
        'Total Break Hours': props.data.total_break_hours,
        'Total Work Hours': props.data.total_work_hours,
    }

    clockinInformation.value = {
        'Outside Radius Clock In': props.data.outside_radius_clock_in,
        'Note Clock In': props.data.note_clock_in,
        'Clock In Location': props.data.address_clock_in,
        'Clock In Image': props.data.clock_in_image,
        'File Clock In Attachment': props.data.files_clock_in
    }

    clockoutInformation.value = {
        'Outside Radius Clock Out': props.data.outside_radius_clock_out,
        'Note Clock Out': props.data.note_clock_out,
        'Clock Out Location': props.data.address_clock_out,
        'Clock Out Image': props.data.clock_out_image,
        'File Clock Out Attachment': props.data.files_clock_out
    }
}

const getTypeStatus = (status) => {
    if (status === 'present') {
        return 'primary-solid';
    } else if (status === 'holiday') {
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

const emit = defineEmits(['close', 'successSubmit'])
</script>

<template>
    <VDialog :showModal="openDialog" title="Detail Attendance" size="2xl" @opened="openForm">
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
            <!-- Attendance Information -->
            <section class="p-5 border border-slate-300 rounded mb-5">
                <div class="font-semibold text-slate-800 text-base mb-3">Attendance Information</div>
                <div class="grid grid-cols-2 gap-2">
                    <div v-for="(data, index) in attendanceInformation" :key="index">
                        <div class="font-medium text-sm text-slate-600 mb-1">{{ index }}</div>
                        <VBadge :text="data" :color="getTypeStatus(data)" size="sm" v-if="index === 'Type'"/>
                        <div class="font-normal text-sm text-slate-500" v-else>{{ data }}</div>
                    </div>
                </div>
            </section>

            <!-- Time Information -->
            <section class="p-5 border border-slate-300 rounded mb-5">
                <div class="font-semibold text-slate-800 text-base mb-3">Time Information</div>
                <div class="grid grid-cols-2 gap-2">
                    <div v-for="(data, index) in timeInformation" :key="index">
                        <div class="font-medium text-sm text-slate-600 mb-1">{{ index }}</div>
                        <div class="font-normal text-sm text-slate-500">{{ data }}</div>
                    </div>
                </div>
            </section>

            <!-- Clock Information -->
            <div class="grid grid-cols-2 gap-4">
                <section class="p-5 border border-slate-300 rounded">
                    <div class="font-semibold text-slate-800 text-base mb-3">Clock In Information</div>
                    <div class="grid grid-cols-1 gap-2">
                        <div v-for="(clockinData, index) in clockinInformation" :key="index">
                            <div class="font-medium text-sm text-slate-600 mb-1">{{ index }}</div>
                            <img class="w-64 h-64 mb-1" :src="clockinData" width="80" height="80" v-if="index === 'Clock In Image'" />
                            <a :href="data.map_address_clock_in" target="_blank" class="font-normal text-sm text-blue-500 hover:text-blue-600" v-else-if="index === 'Clock In Location'">{{clockinData}}</a>
                            <div v-else-if="index === 'File Clock In Attachment'" v-for="(filesData, filesIndex) in clockinData" :key="filesIndex">
                                <a :href="filesData" target="_blank" class="font-normal text-sm text-blue-500 hover:text-blue-600">File {{ filesIndex+1
                                    }}</a>
                            </div>
                            <div class="font-normal text-sm text-slate-500" v-else>{{ clockinData }}</div>
                        </div>
                    </div>
                </section>
                <section class="p-5 border border-slate-300 rounded" v-if="data.clock_out !== '-'">
                    <div class="font-semibold text-slate-800 text-base mb-3">Clock Out Information</div>
                    <div class="grid grid-cols-1 gap-2">
                        <div v-for="(clockoutData, index) in clockoutInformation" :key="index">
                            <div class="font-medium text-sm text-slate-600 mb-1">{{ index }}</div>
                            <img class="w-64 h-64 mb-1" :src="clockoutData" width="80" height="80" v-if="index === 'Clock Out Image' && clockoutData !== '-'" />
                            <a :href="data.map_address_clock_out" target="_blank" class="font-normal text-sm text-blue-500 hover:text-blue-600"
                                v-else-if="index === 'Clock Out Location'">{{ clockoutData }}</a>
                            <div  v-else-if="index === 'File Clock Out Attachment' && clockoutData !== '-'" v-for="(filesData, filesIndex) in clockoutData" :key="filesIndex">
                                <a :href="filesData" target="_blank" class="font-normal text-sm text-blue-500 hover:text-blue-600">File {{ filesIndex+1 }}</a>
                            </div>
                            <div class="font-normal text-sm text-slate-500" v-else>{{ clockoutData }}</div>
                        </div>
                    </div>
                </section>
            </div>
            
        </template>
        <template v-slot:footer>
            <div class="flex flex-wrap justify-end space-x-2">
                <VButton label="Close" type="default" @click="$emit('close')" />
            </div>
        </template>
    </VDialog>
</template>