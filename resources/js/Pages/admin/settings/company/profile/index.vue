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
import VInput from '@/components/VInput/index.vue';
import VRadio from '@/components/VRadio/index.vue';
import VButton from '@/components/VButton/index.vue';

const breadcrumb = [
    {
        name: "Dashboard",
        active: false,
        to: route('dashboard.index')
    },
    {
        name: "Company Settings",
        active: false,
        to: route('settings.company.profile.index')
    },
    {
        name: "Profile",
        active: true,
        to: route('settings.company.profile.index')
    },
]
const statusOptions = [
    {
        label: "Healthy",
        value: "healthy"
    },
    {
        label: "Maintenance",
        value: "maintenance"
    }
]

const pictureButtonLoading = ref(false)
const submitLoading = ref(false)
const selectedFile = ref({})
const fileInputKey = ref(0)
const formError = ref({})
const form = reactive({
    name : props.additional.data.name,
    company_name : props.additional.data.company_name,
    address : props.additional.data.address,
    phone_number : props.additional.data.phone_number,
    email: props.additional.data.email,
    status: props.additional.data.status
})

const fileSelected = (evt) => {
    selectedFile.value = evt.target.files[0];
    fileInputKey.value++
    pictureButtonLoading.value = true
    updatePicture()
}

const updatePicture = async () => {
    const fd = new FormData();
    if (Object.keys(selectedFile.value).length < 1) {
        fd.append("file", selectedFile.value, selectedFile.value.name);
    }

    axios.post(route('settings.company.profile.uploadpicture'), fd)
        .then((res) => {
            selectedFile.value = ref({})
            notify({
                type: "success",
                group: "top",
                text: res.data.meta.message
            }, 2500)
            Inertia.reload()
        }).catch((res) => {
            notify({
                type: "error",
                group: "top",
                text: res.response.data.message
            }, 2500)
        }).finally(() => pictureButtonLoading.value = false)
}

const submit = () => {
    submitLoading.value = true
    axios.post(route('settings.company.profile.updateprofile'), {
            name: form.name,
            company_name: form.company_name,
            address: form.address,
            phone_number: form.phone_number,
            email: form.email,
            status: form.status
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
    form.name = props.additional.data.name,
    form.company_name = props.additional.data.company_name,
    form.address = props.additional.data.address,
    form.phone_number = props.additional.data.phone_number,
    form.email = props.additional.data.email,
    form.status = props.additional.data.status
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
        <h1 class="text-2xl md:text-3xl text-slate-800 font-bold">Company Settings</h1>
    </div>
    <!-- Content -->
    <div class="bg-white shadow-lg rounded-sm mb-8">
        <div class="flex flex-col md:flex-row md:-mr-px">
            <VSidebarSetting :module="additional.menu" />
            <div class="grow">
                <!-- Panel Header -->
                <div class="border-b">
                    <h2 class="text-2xl text-slate-800 font-bold p-6">Profile</h2>
                </div>

                <!-- Panel Content -->
                <div class="p-6 space-y-6">
                    <!-- Company Picture -->
                    <section>
                        <div class="flex items-center">
                            <div class="mr-4">
                                <img class="w-20 h-20 rounded-full" :src="additional.data.logo" width="80" height="80" alt="User upload" />
                            </div>
                            <button :is-loading="pictureButtonLoading" class="btn-sm bg-blue-500 hover:bg-blue-600 text-white" @click="$refs.file.click()">Change </button>
                            <input id="logo" accept=".jpg, .jpeg, .png" class="sr-only hidden" name="logo" type="file" @change="fileSelected"
                            :key="fileInputKey" ref="file" />
                        </div>
                    </section>

                    <!-- Company Information -->
                    <section>
                        <h3 class="text-xl leading-snug text-slate-800 font-bold mb-1">Company Information</h3>
                        <div class="text-sm">Here you can complete your company information.</div>
                        <div class="sm:flex sm:items-center space-y-4 sm:space-y-0 sm:space-x-4 mt-5">
                            <div class="sm:w-1/3">
                                <VInput placeholder="Input Name" label="Name" :required="true" v-model="form.name"
                                    :errorMessage="formError.name" @update:modelValue="formError.name = ''" />
                            </div>
                            <div class="sm:w-1/3">
                                <VInput placeholder="Input Company Name" label="Company Name" :required="true" v-model="form.company_name"
                                    :errorMessage="formError.company_name" @update:modelValue="formError.company_name = ''" />
                            </div>
                            <div class="sm:w-1/3">
                                <VInput placeholder="Input Address" label="Address" :required="true" v-model="form.address"
                                    :errorMessage="formError.address" @update:modelValue="formError.address = ''" />
                            </div>
                        </div>
                        <div class="sm:flex sm:items-center space-y-4 sm:space-y-0 sm:space-x-4 mt-5">
                            <div class="sm:w-1/3">
                                <VInput placeholder="Input Phone Number" label="Phone Number" :required="true" v-model="form.phone_number"
                                    :errorMessage="formError.phone_number" @update:modelValue="formError.phone_number = ''" />
                            </div>
                            <div class="sm:w-1/3">
                                <VInput placeholder="Input Email" label="Email" :required="true" v-model="form.email"
                                    :errorMessage="formError.email" @update:modelValue="formError.email = ''" />
                            </div>
                            <div class="sm:w-1/3">
                                <VRadio label="Input Status" name="status-app" :required="true" v-model="form.status" :errorMessage="formError.status" :options="statusOptions" @update:modelValue="formError.status = ''" />
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