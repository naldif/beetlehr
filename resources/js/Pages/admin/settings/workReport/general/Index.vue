<script>
export default {
    layout: AppLayout
}
</script>
<script setup>
import axios from "axios";
import { notify } from "notiwind";
import { reactive, ref } from "vue";
import { object, string } from "vue-types";
import { Head } from "@inertiajs/inertia-vue3";
import AppLayout from '@/layouts/apps.vue';
import { Inertia } from '@inertiajs/inertia'
import VBreadcrumb from '@/components/VBreadcrumb/index.vue';
import VSidebarSetting from '@/components/VSidebarSetting/index.vue';
import VButton from '@/components/VButton/index.vue';

const breadcrumb = [
    {
        name: "Dashboard",
        active: false,
        to: route('dashboard.index')
    },
    {
        name: "Systems Settings",
        active: false,
        to: route('settings.leave.general.index')
    },
    {
        name: "General",
        active: true,
        to: route('settings.leave.general.index')
    },
]


const submitLoading = ref(false)
const formError = ref({})
const form = reactive({
    max_time_work_report: props.additional.data.max_time_work_report,
})

const submit = () => {
    submitLoading.value = true
    axios.post(route('settings.work-report.general.updateMaxTime'), {
        max_time_work_report: form.max_time_work_report.hours + ':' + form.max_time_work_report.minutes,
    }).then((res) => {
        notify({
            type: "success",
            group: "top",
            text: res.data.meta.message
        }, 2500)
        Inertia.reload()
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
    }).finally(() => submitLoading.value = false)
}

const discard = () => {
    form.max_time_work_report = props.additional.data.max_time_work_report
}

const props = defineProps({
    additional: object(),
    title: string()
})

</script>

<template>

    <Head :title="title"></Head>
    <VBreadcrumb :routes="breadcrumb" />
    <div class="mb-4 sm:mb-6 flex justify-between items-center">
        <h1 class="text-2xl md:text-3xl text-slate-800 font-bold">Work Report Settings</h1>
    </div>
    <!-- Content -->
    <div class="bg-white shadow-lg rounded-sm mb-8">
        <div class="flex flex-col md:flex-row md:-mr-px">
            <VSidebarSetting :module="additional.menu" />
            <div class="grow">
                <!-- Panel Header -->
                <div class="border-b">
                    <h2 class="text-2xl text-slate-800 font-bold p-6">General</h2>
                </div>

                <!-- Panel Content -->
                <div class="p-6 space-y-6">
                    <div class="sm:flex sm:items-center space-y-4 sm:space-y-0 sm:space-x-4 mt-3">
                        <div class="sm:w-1/2">
                            <h3 class="text-xl leading-snug text-slate-800 font-bold mb-1">Max Time for Work Report
                            </h3>
                            <div class="text-sm text-slate-500 mb-4">Maximum time for Work Report.</div>
                            <label class="block text-sm font-medium text-slate-600 mb-1">Max time for Work Report
                                <span class="text-rose-500">*</span>
                            </label>

                            <Datepicker v-model="form.max_time_work_report"
                                @update:modelValue="formError.max_time_work_report = ''" time-picker position="left"
                                :clearable="true" placeholder="00:00"
                                :class="{ 'date_error': formError.max_time_work_report }" />
                            <div class="text-xs mt-1" :class="[{
                                'text-rose-500': formError.max_time_work_report,
                            }]" v-if="formError.max_time_work_report">
                                {{ formError.max_time_work_report }}
                            </div>

                        </div>

                    </div>


                    <!-- Panel footer -->
                    <footer>
                        <div class="flex flex-col px-6 py-3 border-t border-slate-200">
                            <div class="flex self-end space-x-3">
                                <VButton :is-loading="submitLoading" label="Discard" type="default" @click="discard" />
                                <VButton :is-loading="submitLoading" label="Save" type="primary" @click="submit" />
                            </div>
                        </div>
                    </footer>
                </div>
            </div>
        </div>
    </div>
</template>
