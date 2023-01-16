<script>
export default {
    layout: AppLayout
}
</script>
<script setup>
import { onMounted } from "vue";
import { object, string } from "vue-types";
import AppLayout from '@/layouts/apps.vue';
import { Head } from "@inertiajs/inertia-vue3";
import VBreadcrumb from '@/components/VBreadcrumb/index.vue';
import VSidebarSetting from '@/components/VSidebarSetting/index.vue';
import VBranch from '@/Pages/admin/settings/payroll/payrollComponent/branch/index.vue';
import VEmployee from '@/Pages/admin/settings/payroll/payrollComponent/employee/index.vue';
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue'

const breadcrumb = [
    {
        name: "Dashboard",
        active: false,
        to: route('dashboard.index')
    },
    {
        name: "Payroll Settings",
        active: false,
        to: route('settings.payroll.components.index')
    },
    {
        name: "Payroll Components",
        active: false,
        to: route('settings.payroll.components.index')
    },
    {
        name: "Setting Value Component",
        active: true,
        to: route('settings.payroll.components.setvalue', { 'id': props.additional.data.id })
    },
]
const props = defineProps({
    additional: object(),
    title: string()
})

onMounted(() => {
});
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
            <div class="grow overflow-scroll">
                <!-- Panel Header -->
                <h2 class="text-2xl text-slate-800 font-bold p-6">{{ additional.data.name }}</h2>
                <div>
                    <TabGroup>
                        <TabList class="px-6">
                            <Tab
                                class="mb-3 mr-3 cursor-pointer ui-selected:border-b ui-selected:border-blue-500 ui-selected:text-blue-500  ui-not-selected:text-slate-500">
                                Branch
                            </Tab>
                            <Tab class="mb-3 mr-3 cursor-pointer
                                ui-selected:border-b ui-selected:border-blue-500 ui-selected:text-blue-500
                                ui-not-selected:text-slate-500
                            ">
                                Employee
                            </Tab>
                        </TabList>
                        <TabPanels>
                            <TabPanel class="px-6 py-4">
                                <VBranch :additional="additional.data" />
                            </TabPanel>
                            <TabPanel class="px-6 py-4">
                                <VEmployee :additional="additional.data" :options="additional" />
                            </TabPanel>
                        </TabPanels>
                    </TabGroup>
                </div>
            </div>
        </div>
    </div>
</template>