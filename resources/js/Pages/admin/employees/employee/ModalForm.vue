<script setup>
import axios from "axios";
import dayjs from "dayjs";
import debounce from "@/composables/debounce"
import { ref } from "vue";
import { bool, object } from "vue-types";
import { notify } from "notiwind";
import VDialog from '@/components/VDialog/index.vue';
import VButton from '@/components/VButton/index.vue';
import VInput from '@/components/VInput/index.vue';
import VSelect from '@/components/VSelect/index.vue';
import VSwitch from '@/components/VSwitch/index.vue';
import VCheckbox from '@/components/VCheckbox/index.vue';

const props = defineProps({
    openDialog: bool(),
    updateAction: bool().def(false),
    data: object().def({}),
    additional: object().def({})
})

const emit = defineEmits(['close', 'successSubmit'])

const phone = ref()
const employmentStatusSelected = ref('')
const bpjstkComponents = ref({})
const previewPicUrl = ref('')
const stepForm = ref(1)
const isLoading = ref(false)
const formError = ref({})
const form = ref({})

const fileSelected = (evt) => {
    formError.value.picture = ''
    form.value.picture = evt.target.files[0];
    previewPicUrl.value = URL.createObjectURL(evt.target.files[0]);
}

const openForm = () => {
    if (props.updateAction) {
        form.value = Object.assign(form.value, props.data)
        previewPicUrl.value = props.data.previewPicUrl
        form.value.action = 'update'
        phone.value = props.data.phone_number
        handleDate()
        initBpjstkNpp()
        handleEmploymentChange()
    } else {
        form.value = ref({})
    }
}

const closeForm = () => {
    form.value = ref({})
    formError.value = ref({})
    stepForm.value = 1
    if (document.getElementById("profilePic")) {
        document.getElementById("profilePic").value = null
    }
    previewPicUrl.value = ''
    phone.value = ''
}

const nextHandle = () => {
    if (stepForm.value === 1) {
        validateBasicInfo()
    }else if(stepForm.value === 2) {
        validateFinance()
    }else if(stepForm.value === 3) {
        validateEmploymentData()
    }
}

const previousHandle = () => {
    stepForm.value -= 1
}

const handleClose = () => {
    form.value = {}
    emit('close')
}

const handleDate = () => {
    if (form.value.start_contract) {
        formError.value.start_date = ''
        form.value.start_date = dayjs(form.value.start_contract).format('YYYY-MM-DD');
    }

    if (form.value.end_contract) {
        formError.value.end_date = ''
        form.value.end_date = dayjs(form.value.end_contract).format('YYYY-MM-DD');
    }
}

const initBpjstkNpp = () => {
    let components = props.additional.bpjstk_components.find(e => e.id == form.value.bpjstk_setting_id)
    if (components) {
        bpjstkComponents.value = components.components
    }
}

const validatePhone = debounce((string, data) => {
    formError.value.phone_number = ''
    if(!data.valid && form.value.phone_number){
        formError.value.phone_number = 'Please Input Valid Phone Number'
    }else{
        formError.value.phone_number = ''
        form.value.phone_number = data.number
    }

}, 500)

const handleBpjstkSettingChange = () => {
    formError.value.bpjstk_setting_id = ''
    let components = props.additional.bpjstk_components.find(e => e.id == form.value.bpjstk_setting_id)
    bpjstkComponents.value = {}
    form.value.bpjstk_old_age = false
    form.value.bpjstk_life_insurance = false
    form.value.bpjstk_pension_time = false
    if (components) {
        bpjstkComponents.value = components.components
    }
}

const handleEmploymentChange = () => {
    formError.value.employment_status_id = ''
    formError.value.end_date = ''
    let status = props.additional.employment_data.find(e => e.id == form.value.employment_status_id)
    if(status){
        employmentStatusSelected.value = status.pkwt_type
        form.value.pkwt_type = status.pkwt_type

        if(status.pkwt_type === 'pkwtt'){
            form.value.end_contract = null
            form.value.end_date = null
        }
    }else{
        employmentStatusSelected.value = ''
        form.value.pkwt_type = null
        form.value.end_contract = null
        form.value.end_date = null
    }
}

const validateBasicInfo = async () => {
    isLoading.value = true
    const fd = new FormData();
    if (form.value.picture != null) {
        fd.append("picture", form.value.picture, form.value.picture.name);
    }

    Object.keys(form.value).forEach(key => {
        fd.append(key, form.value[key]);
    })

    axios.post(route('employment.employee.validateinfo'), fd)
        .then((res) => {
            if (stepForm.value < 3) {
                stepForm.value += 1
            }
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

const validateFinance = async () => {
    isLoading.value = true
    const fd = new FormData();
    if (form.value.picture != null) {
        fd.append("picture", form.value.picture, form.value.picture.name);
    }

    Object.keys(form.value).forEach(key => {
        fd.append(key, form.value[key]);
    })

    axios.post(route('employment.employee.validatefinance'), fd)
        .then((res) => {
            if (stepForm.value < 3) {
                stepForm.value += 1
            }
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

const validateEmploymentData = async () => {
    isLoading.value = true
    const fd = new FormData();
    if (form.value.picture != null) {
        fd.append("picture", form.value.picture, form.value.picture.name);
    }

    Object.keys(form.value).forEach(key => {
        fd.append(key, form.value[key]);
    })

    axios.post(route('employment.employee.validateemploymentdata'), fd)
        .then((res) => {
            if (props.updateAction) {
                update()
            } else {
                create()
            }
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

const create = async () => {
    isLoading.value = true
    const fd = new FormData();
    if (form.value.picture != null) {
        fd.append("picture", form.value.picture, form.value.picture.name);
    }

    Object.keys(form.value).forEach(key => {
        fd.append(key, form.value[key]);
    })

    axios.post(route('employment.employee.create'), fd)
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

const update = async () => {
    isLoading.value = true
    const fd = new FormData();
    if (form.value.picture != null) {
        fd.append("picture", form.value.picture, form.value.picture.name);
    }

    Object.keys(form.value).forEach(key => {
        fd.append(key, form.value[key]);
    })

    axios.post(route('employment.employee.update', {'id': form.value.id}), fd)
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
    <VDialog :showModal="openDialog" :title="updateAction ? 'Update Employee' : 'Add Employee'" @opened="openForm"
        @closed="closeForm" size="2xl" :isUseProgress="true">
        <template v-slot:close>
            <button class="text-slate-400 hover:text-slate-500" @click="handleClose">
                <div class="sr-only">Close</div>
                <svg class="w-4 h-4 fill-current">
                    <path
                        d="M7.95 6.536l4.242-4.243a1 1 0 111.415 1.414L9.364 7.95l4.243 4.242a1 1 0 11-1.415 1.415L7.95 9.364l-4.243 4.243a1 1 0 01-1.414-1.415L6.536 7.95 2.293 3.707a1 1 0 011.414-1.414L7.95 6.536z" />
                </svg>
            </button>
        </template>
        <template v-slot:progress>
            <div class="px-4">
                <div class="max-w-md mx-auto w-full">
                    <div class="relative">
                        <div class="absolute left-0 top-1/2 -mt-[11px] w-1/2 h-0.5" :class="{
                            'bg-blue-500': stepForm === 1,
                            'bg-emerald-400': stepForm === 2 || stepForm === 3
                        }" aria-hidden="true"></div>
                        <div class="absolute right-0 top-1/2 -mt-[11px] w-1/2 h-0.5" :class="{
                            'bg-slate-200': stepForm === 1,
                            'bg-blue-500': stepForm === 2,
                            'bg-emerald-400': stepForm === 3
                        }" aria-hidden="true"></div>
                        <ul class="relative flex justify-between w-full">
                            <li>
                                <div class="flex items-center justify-center w-6 h-6 rounded-full text-xs font-semibold text-white"
                                    :class="{
                                        'bg-blue-500': stepForm === 1,
                                        'bg-emerald-400': stepForm === 2 || stepForm === 3
                                    }">1</div>
                            </li>
                            <li>
                                <div class="flex items-center justify-center w-6 h-6 rounded-full text-xs font-semibold text-white"
                                    :class="{
                                        'bg-slate-300': stepForm === 1,
                                        'bg-blue-500': stepForm === 2,
                                        'bg-emerald-400': stepForm === 3
                                    }">2</div>
                            </li>
                            <li>
                                <div class="flex items-center justify-center w-6 h-6 rounded-full text-xs font-semibold text-white"
                                    :class="{
                                        'bg-slate-300': stepForm === 1 || stepForm === 2,
                                        'bg-blue-500': stepForm === 3,
                                    }">3</div>
                            </li>
                        </ul>
                        <ul class="relative flex justify-between w-full">
                            <li>
                                <div class="text-xs font-semibold -ml-[14px] flex items-center justify-center" :class="{
                                    'text-blue-500': stepForm === 1,
                                    'text-emerald-400': stepForm === 2 || stepForm === 3
                                }">Basic Info</div>
                            </li>
                            <li>
                                <div class="text-xs font-semibold ml-5 flex items-center justify-center" :class="{
                                    'text-slate-300': stepForm === 1,
                                    'text-blue-500': stepForm === 2,
                                    'text-emerald-400': stepForm === 3
                                }">Finance</div>
                            </li>
                            <li>
                                <div class="text-xs font-semibold -mr-9 flex items-center justify-center" :class="{
                                    'text-slate-300': stepForm === 1 || stepForm === 2,
                                    'text-blue-500': stepForm === 3,
                                }">Employment Data</div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </template>
        <template v-slot:content>
            <!-- Basic Info Section -->
            <section v-if="stepForm === 1">
                <div class="grid grid-cols-2 gap-3">
                    <VInput placeholder="Insert Name" label="Employee Name" :required="true"
                        v-model="form.employee_name" :errorMessage="formError.employee_name"
                        @update:modelValue="formError.employee_name = ''" />
                    <VInput placeholder="Insert Email" label="Email" :required="true" v-model="form.email"
                        :errorMessage="formError.email" @update:modelValue="formError.email = ''" />
                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-1">Phone Number <span class="text-rose-500">*</span> </label>
                        <vue-tel-input v-model="phone" mode="international" 
                            @on-input="validatePhone"
                            :dropdownOptions="{ showFlags: true, showDialCodeInList: true }"
                            :inputOptions="{ showDialCode: true}"
                            :styleClasses="{
                                'vue-tel-input-error': formError.phone_number
                            }"
                        />
                        <div class="text-xs mt-1" :class="[{
                            'text-rose-500': formError.phone_number
                        }]" v-if="formError.phone_number">
                            {{ formError.phone_number }}
                        </div>
                    </div>
                    
                    <VInput placeholder="Insert Employee External Id" label="Employee External Id" v-model="form.employee_external_id"
                        :errorMessage="formError.employee_external_id" @update:modelValue="formError.employee_external_id = ''"
                        :tooltip="true" tooltipBg="white" :disabled="additional.employee_settings.editable_employee_external_id != 1 && updateAction">
                        <template v-slot:tooltip>
                            <div class="text-xs">
                                <div class="font-semibold text-slate-800 mb-1">Employee External Id.</div>
                                <div class="mb-0.5">Akan di generate automatis apabila dikosongkan</div>
                            </div>
                        </template>
                    </VInput>
                    <VInput placeholder="Insert Password" label="Password" :required="true" v-model="form.password"
                        :errorMessage="formError.password" @update:modelValue="formError.password = ''"
                        type="password" />
                    <VInput placeholder="Confirm Password" label="Confirm Password" :required="true"
                        v-model="form.password_confirmation" :errorMessage="formError.password_confirmation"
                        @update:modelValue="formError.password_confirmation = ''" type="password" />
                    <VSelect placeholder="Choose Designation" :required="true" v-model="form.designation_id"
                        :options="additional.designation_list" label="Designation"
                        :errorMessage="formError.designation_id" @update:modelValue="formError.designation_id = ''" />
                    <VSelect placeholder="Choose Role" :required="true" v-model="form.role_id"
                        :options="additional.role_list" label="Role" :errorMessage="formError.role_id"
                        @update:modelValue="formError.role_id = ''" />
                    <VInput placeholder="Insert Address" label="Address" :required="true" v-model="form.address"
                        :errorMessage="formError.address" @update:modelValue="formError.address = ''" />
                    <VInput placeholder="Insert User Device" label="User Device" v-model="form.user_device"
                        :errorMessage="formError.user_device" @update:modelValue="formError.user_device = ''" />
                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-1" for="profilePic">Profile Picture
                            <span class="text-rose-500" v-if="!updateAction">*</span></label>
                        <img class="w-20 h-20 rounded-full mb-1" :src="previewPicUrl" width="80" height="80"
                            v-if="previewPicUrl" />
                        <input
                            class="block w-full cursor-pointer bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-md"
                            type="file" id="profilePic" accept=".jpg, .jpeg, .png" @change="fileSelected">
                        <div class="text-xs mt-1" :class="[{
                            'text-rose-500': formError.picture
                        }]" v-if="formError.picture">
                            {{ formError.picture }}
                        </div>
                    </div>
                </div>
            </section>

            <!-- Finance Section -->
            <section v-else-if="stepForm === 2">
                <div class="grid grid-cols-2 gap-3">
                    <VInput placeholder="Insert Bank Name" label="Employee Bank Name" :required="true"
                        v-model="form.bank_name" :errorMessage="formError.bank_name"
                        @update:modelValue="formError.bank_name = ''" />
                    <VInput placeholder="Insert Account Name" label="Account Name" :required="true"
                        v-model="form.account_name" :errorMessage="formError.account_name"
                        @update:modelValue="formError.account_name = ''" />
                    <div class="col-span-2">
                        <VInput placeholder="Insert Account Number" label="Account Number" :required="true"
                            v-model="form.account_number" :errorMessage="formError.account_number"
                            @update:modelValue="formError.account_number = ''" />
                    </div>
                    <div>
                        <div class="text-sm font-medium text-slate-600 mb-1">BPJS Kesehatan</div>
                        <VSwitch v-model="form.is_use_bpjsk" :label="form.is_use_bpjsk ? 'Active' : 'Non-active'" id="bpjsk" type="inline"
                            @update:modelValue="formError.bpjsk_setting_id = '', formError.bpjsk_number_card = ''" />

                        <!-- NPP  -->
                        <transition enter-active-class="transition ease-out duration-100 transform"
                            enter-from-class="opacity-0 -translate-y-2" enter-to-class="opacity-100 translate-y-0"
                            leave-active-class="transition ease-out duration-100" leave-from-class="opacity-100"
                            leave-to-class="opacity-0">
                            <section v-if="form.is_use_bpjsk">
                                <VInput placeholder="Insert Card Number" label="Card Number" :required="true"
                                    v-model="form.bpjsk_number_card" :errorMessage="formError.bpjsk_number_card"
                                    @update:modelValue="formError.bpjsk_number_card = ''" class="mt-3" />
                                <VSelect placeholder="Choose NPP" :required="true" v-model="form.bpjsk_setting_id"
                                    :options="additional.bpjsk_npp" label="NPP"
                                    :errorMessage="formError.bpjsk_setting_id"
                                    @update:modelValue="formError.bpjsk_setting_id = ''" class="mt-3" />
                            </section>
                        </transition>

                        <!-- Specific Amount -->
                        <VCheckbox v-model="form.bpjsk_use_specific_amount" label="Use Spesific Amount"
                            class="mt-5 mb-1 cursor-pointer" />
                        <transition enter-active-class="transition ease-out duration-100 transform"
                            enter-from-class="opacity-0 -translate-y-2" enter-to-class="opacity-100 translate-y-0"
                            leave-active-class="transition ease-out duration-100" leave-from-class="opacity-100"
                            leave-to-class="opacity-0">
                            <VInput placeholder="Insert Specific Amount" v-model="form.bpjsk_specific_amount"
                                :errorMessage="formError.bpjsk_specific_amount"
                                @update:modelValue="formError.bpjsk_specific_amount = ''"
                                v-if="form.bpjsk_use_specific_amount" type="number" />
                        </transition>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-slate-600 mb-1">BPJS Ketenagakerjaan</div>
                        <VSwitch v-model="form.is_use_bpjstk" :label="form.is_use_bpjstk ? 'Active' : 'Non-active'" id="bpjstk" type="inline"
                            @update:modelValue="formError.bpjstk_setting_id = '', formError.bpjstk_number_card = ''" />

                        <!-- NPP  -->
                        <transition enter-active-class="transition ease-out duration-100 transform"
                            enter-from-class="opacity-0 -translate-y-2" enter-to-class="opacity-100 translate-y-0"
                            leave-active-class="transition ease-out duration-100" leave-from-class="opacity-100"
                            leave-to-class="opacity-0">
                            <section v-if="form.is_use_bpjstk">
                                <VInput placeholder="Insert Card Number" label="Card Number" :required="true"
                                    v-model="form.bpjstk_number_card" :errorMessage="formError.bpjstk_number_card"
                                    @update:modelValue="formError.bpjstk_number_card = ''" class="mt-3" />
                                <VSelect placeholder="Choose NPP" :required="true" v-model="form.bpjstk_setting_id"
                                    :options="additional.bpjstk_npp" label="NPP"
                                    :errorMessage="formError.bpjstk_setting_id"
                                    @update:modelValue="handleBpjstkSettingChange" class="mt-3" />
                                <div class="mt-3" v-if="Object.keys(bpjstkComponents).length > 0">
                                    <div class="space-y-3">
                                        <VSwitch v-model="form.bpjstk_old_age" label="Jaminan Hari Tua" type="inline"
                                            v-if="bpjstkComponents.old_age" />
                                        <VSwitch v-model="form.bpjstk_life_insurance" label="Jaminan Kematian"
                                            type="inline" v-if="bpjstkComponents.life_insurance" />
                                        <VSwitch v-model="form.bpjstk_pension_time" label="Jaminan Pensiun"
                                            type="inline" v-if="bpjstkComponents.pension_time" />
                                    </div>
                                </div>
                            </section>
                        </transition>

                        <!-- Specific Amount -->
                        <VCheckbox v-model="form.bpjstk_use_specific_amount" label="Use Spesific Amount"
                            class="mt-5 mb-1 cursor-pointer" />
                        <transition enter-active-class="transition ease-out duration-100 transform"
                            enter-from-class="opacity-0 -translate-y-2" enter-to-class="opacity-100 translate-y-0"
                            leave-active-class="transition ease-out duration-100" leave-from-class="opacity-100"
                            leave-to-class="opacity-0">
                            <VInput placeholder="Insert Specific Amount" v-model="form.bpjstk_specific_amount"
                                :errorMessage="formError.bpjstk_specific_amount"
                                @update:modelValue="formError.bpjstk_specific_amount = ''"
                                v-if="form.bpjstk_use_specific_amount" type="number" />
                        </transition>
                    </div>
                </div>
            </section>

            <!-- Employment Data Section -->
            <section v-else-if="stepForm === 3">
                <div class="grid grid-cols-2 gap-3">
                    <div class="col-span-2">
                        <VSelect placeholder="Select Branch" :required="true" v-model="form.branch_id" :options="additional.branch_list" label="Branch"
                            :errorMessage="formError.branch_id" @update:modelValue="formError.branch_id = ''" />
                    </div>
                    <VSelect placeholder="Select Employment List" :required="true" v-model="form.employment_status_id"
                        :options="additional.employment_list" label="Employment Status" :errorMessage="formError.employment_status_id"
                        @update:modelValue="handleEmploymentChange" />
                    <VSelect placeholder="Select Status" :required="additional.employee_settings.payroll_istaxable == 1" v-model="form.ptkp_tax_list_id"
                        :options="additional.ptkp_tax_list" label="PTKP Status" :errorMessage="formError.ptkp_tax_list_id"
                        @update:modelValue="formError.ptkp_tax_list_id = ''" />
                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-1">
                            Begin Contract <span class="text-rose-500">*</span>
                        </label>
                        <Datepicker v-model="form.start_contract" single-picker
                            :enableTimePicker="false" position="left" :clearable="false" format="dd MMMM yyyy" previewFormat="dd MMMM yyyy"
                            placeholder="Insert Begin Contract Date" :class="{ 'date_error': formError.start_date }" @update:modelValue="handleDate"/>
                        <div class="text-xs" :class="[{ 'text-rose-500': formError.start_date}]" v-if="formError.start_date">
                            {{ formError.start_date }}
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-1">
                            End Contract <span class="text-rose-500" v-if="employmentStatusSelected === 'pkwt'">*</span>
                        </label>
                        <Datepicker v-model="form.end_contract" single-picker :enableTimePicker="false" :disabled="employmentStatusSelected === 'pkwt' ? false : true"
                            position="left" :clearable="false" format="dd MMMM yyyy" previewFormat="dd MMMM yyyy"
                            placeholder="Insert End Contract Date" :class="{ 'date_error': formError.end_date }" @update:modelValue="handleDate"/>
                        <div class="text-xs" :class="[{ 'text-rose-500': formError.end_date}]" v-if="formError.end_date">
                            {{ formError.end_date }}
                        </div>
                    </div>
                    <VSelect placeholder="Select Payroll Group" v-model="form.payroll_group_id" :options="additional.payroll_group_list"
                        label="Payroll Group" :errorMessage="formError.payroll_group_id" @update:modelValue="formError.payroll_group_id = ''" />
                    <VSelect placeholder="Select Direct Manager" v-model="form.manager_id" :options="additional.employee_list" label="Direct Manager"
                        @update:modelValue="formError.manager_id = ''" />
                </div>
            </section>

        </template>
        <template v-slot:footer>
            <div class="flex flex-wrap justify-end space-x-2">
                <VButton :is-loading="isLoading" label="Previous" type="default" @click="previousHandle"
                    :disabled="stepForm === 1" />
                <VButton :is-loading="isLoading" :label="stepForm === 3 ? 'Submit' : 'Next'" type="primary"
                    @click="nextHandle" />
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