<script setup>
import axios from "axios";
import dayjs from "dayjs"
import { ref, watch } from "vue";
import { bool, object } from "vue-types";
import { notify } from "notiwind";
import VDialog from '@/components/VDialog/index.vue';
import VButton from '@/components/VButton/index.vue';
import VSelect from '@/components/VSelect/index.vue';
import VInput from '@/components/VInput/index.vue';
import VBadge from '@/components/VBadge/index.vue';
import VModalRejectForm from './ModalRejectForm.vue';

const props = defineProps({
    openDialog: bool(),
    updateAction: bool().def(false),
    data: object().def({}),
    additional: object().def({})
})

const emit = defineEmits(['close', 'successSubmit'])

const openModalForm = ref(false)
const itemSelected = ref({})
const employeeSelectHandle = ref()
const isLoading = ref(false);
const downloadFileLoading = ref(false);
const detailInformation = ref([]);
const formError = ref({})
const form = ref({})

const openForm = () => {
    if (props.updateAction) {
        initData()
    } else {
        form.value = ref({})
    }
}

const closeForm = () => {
    form.value = ref({})
    formError.value = ref({})
    if (document.getElementById("resignFile")) {
        document.getElementById("resignFile").value = null
    }

    if (!props.updateAction) {
        employeeSelectHandle.value.clearSelected()
    }
}

const handleDate = () => {
    if (form.value.submission_date) {
        formError.value.date = ''
        form.value.date = dayjs(form.value.submission_date).format('YYYY-MM-DD')
    }

    if (form.value.end_date) {
        formError.value.end_contract = ''
        form.value.end_contract = dayjs(form.value.end_date).format('YYYY-MM-DD')
    }
}

const fileSelected = (evt) => {
    formError.value.file = ''
    form.value.file = evt.target.files[0];
}

const submit = async () => {
    isLoading.value = true
    const fd = new FormData();
    if (form.value.file != null) {
        fd.append("file", form.value.file, form.value.file.name);
    }

    Object.keys(form.value).forEach(key => {
        fd.append(key, form.value[key]);
    })

    axios.post(route('employment.resign-management.create'), fd)
        .then((res) => {
            emit('close')
            emit('successSubmit')
            form.value = ref({})
            notify({
                type: "success",
                group: "top",
                text: res.data.meta.message
            }, 2500)
        }).catch((res) => {
            // Handle validation errors
            const result = res.response.data
            const metaError = res.response.data.meta?.error
            if (result.hasOwnProperty('errors')) {
                formError.value = ref({});
                Object.keys(result.errors).map((key) => {
                    formError.value[key] = result.errors[key].toString();
                });
            }

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
}

const handleDownloadFile = async (id) => {
    downloadFileLoading.value = true
    axios.get(route('employment.resign-management.downloadfile', { 'id': id }), { responseType: 'blob' })
        .then((res) => {
            console.log(res, 'success')
            const url = window.URL.createObjectURL(new Blob([res.data]));
            const link = document.createElement("a");
            link.href = url;
            let timestamp = Math.floor(new Date().getTime() / 1000)
            link.setAttribute("download", 'resign_file_' + timestamp + '.' + props.data.file_ext);
            document.body.appendChild(link);
            link.click();
            link.remove();
        }).catch((res) => {
            console.log(res)
            notify({
                type: "error",
                group: "top",
                text: 'Something Wrong !'
            }, 2500)
        }).finally(() => downloadFileLoading.value = false)
}

const updateStatus = async (action) => {
    isLoading.value = true
    axios.put(route('employment.resign-management.update', { 'id': props.data.id }), {
        action: action
    }).then((res) => {
        emit('close')
        emit('successSubmit')
        form.value = ref({})
        notify({
            type: "success",
            group: "top",
            text: res.data.meta.message
        }, 2500)
    }).catch((res) => {
        // Handle validation errors
        const result = res.response.data
        const metaError = res.response.data.meta?.error
        if (result.hasOwnProperty('errors')) {
            formError.value = ref({});
            Object.keys(result.errors).map((key) => {
                formError.value[key] = result.errors[key].toString();
            });
        }

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
}

const initData = () => {
    detailInformation.value = [
        {
            label: 'Employee Name',
            value: props.data.name,
            type: 'text'
        },
        {
            label: 'Branch',
            value: props.data.branch,
            type: 'text'
        },
        {
            label: 'Date Submission',
            value: props.data.date_submission,
            type: 'text'
        },
        {
            label: 'End Contract',
            value: props.data.end_contract,
            type: 'text'
        },
        {
            label: 'Resign File',
            value: props.data.file,
            type: 'file'
        },
        {
            label: 'Status',
            value: props.data.status_formatted,
            type: 'badge'
        }
    ]
}

const getTypeValue = (value) => {
    if (value === 'Waiting') {
        return 'warning';
    } else if (value === 'Approved') {
        return 'success';
    } else {
        return 'danger';
    }
}

watch(() => props.data, (newValue, oldValue) => {
    if (newValue) {
        initData()
    }
});
</script>

<template>
    <VDialog :showModal="openDialog" :title="updateAction ? 'Detail Resign Submission' : 'Add Resign Submission'"
        @opened="openForm" @closed="closeForm" size="xl">
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
            <section v-if=updateAction>
                <div class="grid grid-cols-2 gap-6">
                    <div v-for="(data, index) in detailInformation" :key="index">
                        <label class="font-medium text-sm text-slate-600 mb-1">{{ data.label }}</label>
                        <div class="font-normal text-sm text-slate-500">
                            <p v-if="data.type === 'text'">{{ data.value }}</p>
                            <VBadge :text="data.value" :color="getTypeValue(data.value)" size="sm"
                                v-else-if="data.type === 'badge'" />
                            <VButton label="Download File" type="primary" @click="handleDownloadFile(data.value)"
                                class="mt-auto" :is-loading="downloadFileLoading" v-else-if="data.type === 'file'" />
                        </div>
                    </div>
                </div>
            </section>
            <div class="grid grid-cols-2 gap-3" v-else>
                <div class="col-span-2">
                    <VSelect placeholder="Select Employee" :required="true" v-model="form.employee_id"
                        :options="additional.employee_list" label="Select Employee"
                        :errorMessage="formError.employee_id" @update:modelValue="formError.employee_id = ''"
                        ref="employeeSelectHandle" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-600 mb-1">
                        Submission Date of Resignation <span class="text-rose-500">*</span>
                    </label>
                    <Datepicker v-model="form.submission_date" @update:modelValue="handleDate" :enableTimePicker="false"
                        position="left" :clearable="false" format="dd MMMM yyyy" previewFormat="dd MMMM yyyy"
                        placeholder="Select Submission Date" :class="{ 'date_error': formError.date }" />
                    <div class="text-xs mt-1" :class="[{
                        'text-rose-500': formError.date,
                    }]" v-if="formError.date">
                        {{ formError.date }}
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-600 mb-1">
                        End Contract <span class="text-rose-500">*</span>
                    </label>
                    <Datepicker v-model="form.end_date" @update:modelValue="handleDate" :enableTimePicker="false"
                        position="left" :clearable="false" format="dd MMMM yyyy" previewFormat="dd MMMM yyyy"
                        placeholder="Select End Contract Date" :class="{ 'date_error': formError.end_contract }" />
                    <div class="text-xs mt-1" :class="[{
                        'text-rose-500': formError.end_contract,
                    }]" v-if="formError.end_contract">
                        {{ formError.end_contract }}
                    </div>
                </div>
                <div class="col-span-2">
                    <VInput placeholder="Insert Reason" label="Reason" :required="true" v-model="form.reason"
                        :errorMessage="formError.reason" @update:modelValue="formError.reason = ''" />
                </div>
                <div class="col-span-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-1" for="resignFile">Resign File
                            <span class="text-rose-500" v-if="!updateAction">*</span></label>
                        <input
                            class="block w-full cursor-pointer bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-md"
                            type="file" id="resignFile" @change="fileSelected"
                            accept="image/x-png, image/jpeg, application/pdf">
                        <div class="text-xs mt-1" :class="[{
                            'text-rose-500': formError.file
                        }]" v-if="formError.file">
                            {{ formError.file }}
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <template v-slot:footer>
            <div class="flex flex-wrap justify-end space-x-2" v-if="updateAction">
                <VButton label="Reject" :is-loading="isLoading" type="danger"
                    @click="openModalForm = true, itemSelected = data" v-if="data.status === 'waiting'" />
                <VButton label="Approve" :is-loading="isLoading" type="success" @click="updateStatus('approve')"
                    v-if="data.status === 'waiting'" />
            </div>
            <div class="flex flex-wrap justify-end space-x-2" v-else>
                <VButton label="Cancel" type="default" @click="$emit('close')" />
                <VButton :is-loading="isLoading" :label="updateAction ? 'Update' : 'Create'" type="primary"
                    @click="submit" />
            </div>
        </template>
    </VDialog>
    <VModalRejectForm :data="itemSelected" :open-dialog="openModalForm"
        @close="itemSelected = ref({}), openModalForm = false"
        @successSubmit="$emit('close'), $emit('successSubmit'), form = ref({})" />
</template>

<style>
.dp__select {
    color: #4F8CF6 !important;
}

.date_error {
    --dp-border-color: #dc3545 !important;
}
</style>