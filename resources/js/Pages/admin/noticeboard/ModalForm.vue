<script setup>
import axios from "axios";
import dayjs from "dayjs"
import { ref } from "vue";
import { bool, object } from "vue-types";
import { notify } from "notiwind";
import VDialog from '@/components/VDialog/index.vue';
import VButton from '@/components/VButton/index.vue';
import VSelect from '@/components/VSelect/index.vue';
import VInput from '@/components/VInput/index.vue';

const props = defineProps({
    openDialog: bool(),
    updateAction: bool().def(false),
    data: object().def({}),
    additional: object().def({})
})

const emit = defineEmits(['close', 'successSubmit'])

const description = ref('')
const placementSelectHandle = ref()
const typeSelectHandle = ref()
const isLoading = ref(false);
const downloadFileLoading = ref(false);
const formError = ref({})
const form = ref({})
const typeOptions = ref({
    'description': 'Description',
    'document': 'Document',
})

const openForm = () => {
    if(props.updateAction){
        form.value = Object.assign(form.value, props.data)
        form.value.start_date = new Date(props.data.start_date_db)
        form.value.end_date = new Date(props.data.end_date_db)
        description.value = props.data.description === 'null' || props.data.description === null ? '' : props.data.description
        handleDate()
    }else{
        form.value = ref({})
    }
}

const closeForm = () => {
    form.value = ref({})
    formError.value = ref({})
    if (document.getElementById("noticeBoardFile")) {
        document.getElementById("noticeBoardFile").value = null
    }

    if(!props.updateAction){
        placementSelectHandle.value.clearSelected()
    }
}

const handleDate = () => {
    if (form.value.start_date) {
        formError.value.start = ''
        form.value.start = dayjs(form.value.start_date).format('YYYY-MM-DD HH:mm')
    }

    if (form.value.end_date) {
        formError.value.end = ''
        form.value.end = dayjs(form.value.end_date).format('YYYY-MM-DD HH:mm')
    }
}

const fileSelected = (evt) => {
    formError.value.file = ''
    form.value.file = evt.target.files[0];
}

const submit = async () => {
    const fd = new FormData();
    if (form.value.file != null) {
        fd.append("file", form.value.file, form.value.file.name);
    }

    Object.keys(form.value).forEach(key => {
        fd.append(key, form.value[key]);
    })

    fd.append('description', description.value);

    if (props.updateAction) {
        update(fd)
    } else {
        create(fd)
    }
}

const handleDownloadFile = async () => {
    downloadFileLoading.value = true
    axios.get(route('leave.downloadfile', { 'id': props.data.file_id }), { responseType: 'blob' })
        .then((res) => {
            const url = window.URL.createObjectURL(new Blob([res.data]));
            const link = document.createElement("a");
            link.href = url;
            let timestamp = Math.floor(new Date().getTime() / 1000)
            link.setAttribute("download", 'notice_board_file_' + timestamp + '.' + props.data.file_ext);
            document.body.appendChild(link);
            link.click();
            link.remove();
        }).catch((res) => {
            notify({
                type: "error",
                group: "top",
                text: 'Something Wrong !'
            }, 2500)
        }).finally(() => downloadFileLoading.value = false)
}

const update = async (fd) => {
    isLoading.value = true
    axios.post(route('notice-board.update', { 'id': props.data.id }), fd)
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

const create = async (fd) => {
    isLoading.value = true
    axios.post(route('notice-board.create'), fd)
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
</script>

<template>
    <VDialog :showModal="openDialog" :title="updateAction ? 'Update Notice Board' : 'Create Notice Board'" @opened="openForm" @closed="closeForm" size="xl">
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
            <div class="grid grid-cols-2 gap-3">
                <div class="col-span-2">
                    <VSelect placeholder="Select Branch" :required="true" v-model="form.branch_id" :options="additional.branches_filter"
                        label="Branch" :errorMessage="formError.branch_id" @update:modelValue="formError.branch_id = ''" ref="placementSelectHandle" />
                </div>
                <div class="col-span-2">
                    <VSelect placeholder="Select Type" :required="true" v-model="form.type" :options="typeOptions"
                        label="Type" :errorMessage="formError.type" @update:modelValue="formError.type = '', formError.description = '', formError.file = ''" ref="typeSelectHandle" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-600 mb-1">
                        Start Date <span class="text-rose-500">*</span> 
                    </label>
                    <Datepicker v-model="form.start_date" @update:modelValue="handleDate" :enableTimePicker="true" position="left"
                        :clearable="false" format="dd MMMM yyyy - HH:ii" previewFormat="dd MMMM yyyy - HH:ii" placeholder="Select Start Date" 
                        :class="{'date_error' : formError.start}"/>
                    <div class="text-xs mt-1" :class="[{
                        'text-rose-500': formError.start,
                    }]" v-if="formError.start">
                        {{ formError.start }}
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-600 mb-1">
                        End Date <span class="text-rose-500">*</span> 
                    </label>
                    <Datepicker v-model="form.end_date" @update:modelValue="handleDate" :enableTimePicker="true" position="left"
                        :clearable="false" format="dd MMMM yyyy - HH:ii" previewFormat="dd MMMM yyyy - HH:ii" placeholder="Select End Date" 
                        :class="{'date_error' : formError.end}"/>
                    <div class="text-xs mt-1" :class="[{
                        'text-rose-500': formError.end,
                    }]" v-if="formError.end">
                        {{ formError.end }}
                    </div>
                </div>
                <div class="col-span-2">
                    <VInput placeholder="Insert Title" label="Title" :required="true" v-model="form.title"
                        :errorMessage="formError.title" @update:modelValue="formError.title = ''" />
                </div>
                <div class="col-span-2" v-if="updateAction">
                    <div v-if="form.type === 'document'">
                        <label class="block text-sm font-medium text-slate-600 mb-1">Download File</label>
                        <VButton label="Download" type="primary" @click="handleDownloadFile" class="mt-auto"
                            :is-loading="downloadFileLoading" v-if="data.file_id" />
                    </div>
                </div>
                <div class="col-span-2">
                    <div v-if="form.type === 'document'">
                        <label class="block text-sm font-medium text-slate-600 mb-1" for="noticeBoardFile">File <span class="text-rose-500" v-if="!updateAction">*</span></label>
                        <input class="block w-full cursor-pointer bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-md"
                            type="file" id="noticeBoardFile" @change="fileSelected" accept="image/x-png, image/jpeg, application/pdf">
                        <div class="text-xs mt-1" :class="[{
                            'text-rose-500': formError.file
                        }]" v-if="formError.file">
                            {{ formError.file }}
                        </div>
                    </div>
                    <div v-else-if="form.type === 'description'">
                        <label class="block text-sm font-medium text-slate-600 mb-1">Description <span class="text-rose-500">*</span></label>
                        <QuillEditor theme="snow" v-model:content="description" contentType="html"/>
                        <div class="text-xs mt-1" :class="[{
                            'text-rose-500': formError.description
                        }]" v-if="formError.description">
                            {{ formError.description }}
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <template v-slot:footer>
            <div class="flex flex-wrap justify-end space-x-2">
                <VButton label="Cancel" type="default" @click="$emit('close')" />
                <VButton :is-loading="isLoading" :label="updateAction ? 'Update' : 'Create'" type="primary" @click="submit" />
            </div>
        </template>
    </VDialog>
</template>

<style>
.dp__select {
    color: #4F8CF6 !important;
}

.date_error {
    --dp-border-color: #dc3545 !important;
}
</style>