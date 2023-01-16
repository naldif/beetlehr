<script setup>
import axios from "axios";
import { ref } from "vue";
import { bool, object } from "vue-types";
import { notify } from "notiwind";
import VDialog from '@/components/VDialog/index.vue';
import VButton from '@/components/VButton/index.vue';
import VModalRejectForm from './ModalReasonForm.vue';
import VBadge from '@/components/VBadge/index.vue';

const props = defineProps({
    openDialog: bool(),
    data: object().def({}),
    additional: object().def({})
})

const emit = defineEmits(['close', 'successSubmit'])

const itemSelected = ref({})
const openModalForm = ref(false)
const actionType = ref('')
const isLoading = ref(false);

const getStatusValue = (value) => {
    if (value === 'awaiting') {
        return 'warning';
    } else if (value === 'approved') {
        return 'success';
    } else if (value === 'rejected') {
        return 'danger';
    } else {
        return 'primary';
    }
}
</script>

<template>
    <VDialog :showModal="openDialog" title="Detail Approval" size="xl">
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
            <!-- Basic Information Section -->
            <section class="mb-4">
                <label class="block text-sm font-semibold text-slate-800 mb-4">
                    Basic Information
                </label>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <div class="font-medium text-sm text-slate-600 mb-1">Type</div>
                        <div class="font-normal text-sm text-slate-500 capitalize">
                            {{ data.type?.replace(/_/g, " ") }}
                        </div>
                    </div>
                    <div class="capitalize">
                        <div class="font-medium text-sm text-slate-600 mb-1">Status</div>
                        <VBadge :text="data.status" :color="getStatusValue(data.status)" size="sm" />
                    </div>
                    <div>
                        <div class="font-medium text-sm text-slate-600 mb-1">Created By</div>
                        <div class="font-normal text-sm text-slate-500 capitalize">
                            {{ data.created_by }}
                        </div>
                    </div>
                    <div>
                        <div class="font-medium text-sm text-slate-600 mb-1">Created At</div>
                        <div class="font-normal text-sm text-slate-500 capitalize">
                            {{ data.created_at }}
                        </div>
                    </div>
                    <div>
                        <div class="font-medium text-sm text-slate-600 mb-1">Branch</div>
                        <div class="font-normal text-sm text-slate-500 capitalize">
                            {{ data.branch }}
                        </div>
                    </div>
                </div>
            </section>

            <!-- Leave Information -->
            <section class="mb-4" id="leave-detail" v-if="data.type === 'create_leave'">
                <label class="block text-sm font-semibold text-slate-800 mb-4">
                    Detail Information
                </label>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <div class="font-medium text-sm text-slate-600 mb-1">Requester Name</div>
                        <div class="font-normal text-sm text-slate-500 capitalize">
                            {{ data.meta_data.requester_name }}
                        </div>
                    </div>
                    <div>
                        <div class="font-medium text-sm text-slate-600 mb-1">Start Date</div>
                        <div class="font-normal text-sm text-slate-500 capitalize">
                            {{ data.meta_data.start_date }}
                        </div>
                    </div>
                    <div>
                        <div class="font-medium text-sm text-slate-600 mb-1">End Date</div>
                        <div class="font-normal text-sm text-slate-500 capitalize">
                            {{ data.meta_data.end_date }}
                        </div>
                    </div>
                    <div>
                        <div class="font-medium text-sm text-slate-600 mb-1">Duration</div>
                        <div class="font-normal text-sm text-slate-500 capitalize">
                            {{ data.meta_data.duration }}
                        </div>
                    </div>
                    <div>
                        <div class="font-medium text-sm text-slate-600 mb-1">Reason</div>
                        <div class="font-normal text-sm text-slate-500 capitalize">
                            {{ data.meta_data.reason }}
                        </div>
                    </div>
                </div>
            </section>

            <!-- Attendance Information -->
            <section class="mb-4" id="attendance-detail" v-if="data.type === 'attendance_without_schedule'">
                <label class="block text-sm font-semibold text-slate-800 mb-4">
                    Detail Information
                </label>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <div class="font-medium text-sm text-slate-600 mb-1">Date</div>
                        <div class="font-normal text-sm text-slate-500 capitalize">
                            {{ data.meta_data.date }}
                        </div>
                    </div>
                    <div>
                        <div class="font-medium text-sm text-slate-600 mb-1">Total Work Hours</div>
                        <div class="font-normal text-sm text-slate-500 capitalize">
                            {{ data.meta_data.total_work_hours }}
                        </div>
                    </div>
                    <div>
                        <div class="font-medium text-sm text-slate-600 mb-1">Clock In</div>
                        <div class="font-normal text-sm text-slate-500 capitalize">
                            {{ data.meta_data.clock_in }}
                        </div>
                    </div>
                    <div>
                        <div class="font-medium text-sm text-slate-600 mb-1">Clock Out</div>
                        <div class="font-normal text-sm text-slate-500 capitalize">
                            {{ data.meta_data.clock_out }}
                        </div>
                    </div>
                    <div>
                        <div class="font-medium text-sm text-slate-600 mb-1">Notes Clock In</div>
                        <div class="font-normal text-sm text-slate-500 capitalize">
                            {{ data.meta_data.notes_clock_in }}
                        </div>
                    </div>
                    <div>
                        <div class="font-medium text-sm text-slate-600 mb-1">Notes Clock Out</div>
                        <div class="font-normal text-sm text-slate-500 capitalize">
                            {{ data.meta_data.notes_clock_out }}
                        </div>
                    </div>
                    <div>
                        <div class="font-medium text-sm text-slate-600 mb-1">Address Clock In</div>
                        <div class="font-normal text-sm text-slate-500 capitalize">
                            {{ data.meta_data.address_clock_in }}
                        </div>
                    </div>
                    <div>
                        <div class="font-medium text-sm text-slate-600 mb-1">Address Clock Out</div>
                        <div class="font-normal text-sm text-slate-500 capitalize">
                            {{ data.meta_data.address_clock_out }}
                        </div>
                    </div>
                    <div>
                        <div class="font-medium text-sm text-slate-600 mb-1">Image Clock In</div>
                        <img class="w-64 h-64 mb-1" :src="data.meta_data.image_clock_in" width="80" height="80" />
                    </div>
                    <div>
                        <div class="font-medium text-sm text-slate-600 mb-1">Image Clock Out</div>
                        <img class="w-64 h-64 mb-1" :src="data.meta_data.image_clock_out" width="80" height="80" />
                    </div>
                </div>
            </section>
        </template>
        <template v-slot:footer>
            <div class="flex flex-wrap justify-end space-x-2">
                <VButton label="Reject" :is-loading="isLoading" type="danger" @click="openModalForm = true, itemSelected = data, actionType = 'reject'" v-if="data.status === 'awaiting'"/>
                <VButton label="Approve" :is-loading="isLoading" type="success" @click="openModalForm = true, itemSelected = data, actionType = 'approve'" v-if="data.status === 'awaiting'"/>
            </div>
        </template>
    </VDialog>
    <VModalRejectForm :data="itemSelected" :open-dialog="openModalForm" @close="itemSelected = ref({}), openModalForm = false" @successSubmit="$emit('close'), $emit('successSubmit'), form = ref({})" :action="actionType"/>
</template>

<style>
.dp__select {
    color: #4F8CF6 !important;
}

.date_error {
    --dp-border-color: #dc3545 !important;
}
</style>