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
import VSwitch from '@/components/VSwitch/index.vue';
import VButton from '@/components/VButton/index.vue';

const breadcrumb = [
    {
        name: "Dashboard",
        active: false,
        to: route('dashboard.index')
    },
    {
        name: "Payroll Settings",
        active: false,
        to: route('settings.payroll.general.index')
    },
    {
        name: "General",
        active: true,
        to: route('settings.payroll.general.index')
    },
]

const submitLoading = ref(false)
const formError = ref({})
const form = reactive({
    payroll_istaxable: props.additional.data.payroll_istaxable === "0" ? false : true,
})

const submit = () => {
    submitLoading.value = true
    axios.post(route('settings.payroll.general.update'), {
        payroll_istaxable: form.payroll_istaxable
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
        const metaError = res.response.data.meta.error
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
    }).finally(() => submitLoading.value = false)
}

const discard = () => {
    form.payroll_istaxable = props.additional.data.payroll_istaxable === "0" ? false : true
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
        <h1 class="text-2xl md:text-3xl text-slate-800 font-bold">Payroll Settings</h1>
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
                    <!-- Run Payroll Related -->
                    <section>
                        <h3 class="text-xl leading-snug text-slate-800 font-bold mb-1">Payroll Setting</h3>
                        <div class="text-sm">Here you can change payroll configuration.</div>
                        <div class="sm:flex sm:items-center space-y-4 sm:space-y-0 sm:space-x-4 mt-5">
                            <div class="sm:w-1/3">
                                <VSwitch v-model="form.payroll_istaxable" label="Taxable" type="block" :tooltip="true">
                                    <template v-slot:tooltip>
                                        <div class="text-xs">
                                            <div class="font-semibold text-slate-800 mb-1">Pajak.</div>
                                            <div class="mb-0.5">On : PTKP Employee dan NPWP Branch wajib diisi. Slip
                                                gaji karyawan akan dikurangi pajak</div>
                                            <div>Off : PTKP Employee dan NPWP Branch wajib diisi. Slip gaji karyawan
                                                tidak dikurangi pajak</div>
                                        </div>
                                    </template>
                                </VSwitch>
                            </div>
                        </div>
                    </section>

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