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
import VKesehatan from '@/Pages/admin/settings/company/bpjs/kesehatan/index.vue';
import VKetenagakerjaan from '@/Pages/admin/settings/company/bpjs/ketenagakerjaan/index.vue';
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue'

const breadcrumb = [
    {
        name: "Dashboard",
        active: false,
        to: route('dashboard.index')
    },
    {
        name: "Company Settings",
        active: false,
        to: route('settings.company.branch.index')
    },
    {
        name: "Branches",
        active: true,
        to: route('settings.company.branch.index')
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
        <h1 class="text-2xl md:text-3xl text-slate-800 font-bold">Company Settings</h1>
    </div>
    <!-- Content -->
    <div class="bg-white shadow-lg rounded-sm mb-8">
        <div class="flex flex-col md:flex-row md:-mr-px">
            <VSidebarSetting :module="additional.menu" />
            <div class="grow overflow-scroll">
                <!-- Panel Header -->
                <h2 class="text-2xl text-slate-800 font-bold p-6">BPJS</h2>
                <div>
                    <TabGroup>
                        <TabList class="px-6">
                            <Tab
                                class="mb-3 mr-3 cursor-pointer ui-selected:border-b ui-selected:border-blue-500 ui-selected:text-blue-500  ui-not-selected:text-slate-500">
                                Kesehatan
                            </Tab>
                            <Tab class="mb-3 mr-3 cursor-pointer
                                ui-selected:border-b ui-selected:border-blue-500 ui-selected:text-blue-500
                                ui-not-selected:text-slate-500
                            ">
                                Ketenagakerjaan
                            </Tab>
                        </TabList>
                        <TabPanels>
                            <TabPanel class="px-6 py-4">
                                <VKesehatan />
                            </TabPanel>
                            <TabPanel class="px-6 py-4">
                                <VKetenagakerjaan :additional="additional" />
                            </TabPanel>
                        </TabPanels>
                    </TabGroup>
                </div>
            </div>
        </div>
    </div>
</template>