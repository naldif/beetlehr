<script>
export default {
    layout: AppLayout
}
</script>
<script setup>
import axios from "axios";
import { notify } from "notiwind";
import { reactive, ref } from "vue";
import { bool, object, string } from "vue-types";
import { Head } from "@inertiajs/inertia-vue3";
import AppLayout from '@/layouts/apps.vue';
import { Inertia } from '@inertiajs/inertia'
import VBreadcrumb from '@/components/VBreadcrumb/index.vue';
import VSidebarSetting from '@/components/VSidebarSetting/index.vue';
import VSwitch from '@/components/VSwitch/index.vue';
import VInput from '@/components/VInput/index.vue';
import VButton from '@/components/VButton/index.vue';
import VLabel from '@/components/VLabel/index.vue';

const breadcrumb = [
    {
        name: "Dashboard",
        active: false,
        to: route('dashboard.index')
    },
    {
        name: "Attendance Settings",
        active: false,
        to: route('settings.attendance.general.index')
    },
    {
        name: "Attendance",
        active: true,
        to: route('settings.attendance.index')
    },
]


const isSelectClockoutTypeMinutes = ref(true)
const isSelectClockoutTypeFixed = ref(true)
const submitLoading = ref(false)
const formError = ref({})
const form = reactive({
    tolerance_notification: props.additional.data.tolerance_notification,
    tolerance_clock_in: props.additional.data.tolerance_clock_in,
    tolerance_clock_out: props.additional.data.tolerance_clock_out,
    is_absent_force_clock_out: props.additional.data.is_absent_force_clock_out === "1" ? true : false,
    time_for_force_clockout_type: props.additional.data.time_for_force_clockout_type,
    time_for_force_clockout_fixed: props.additional.data.time_for_force_clockout_fixed,
    time_for_force_clockout_minutes: props.additional.data.time_for_force_clockout_minutes,
})

if (props.additional.data.time_for_force_clockout_type == 'fixed') {
    selectClockoutTypeFixed();
} else if (props.additional.data.time_for_force_clockout_type == 'minutes') {
    selectClockoutTypeMinutes();
}

function selectClockoutTypeMinutes() {
    isSelectClockoutTypeMinutes.value = false
    isSelectClockoutTypeFixed.value = true
    form.time_for_force_clockout_type = 'minutes'
}

function selectClockoutTypeFixed() {
    isSelectClockoutTypeMinutes.value = true
    isSelectClockoutTypeFixed.value = false
    form.time_for_force_clockout_type = 'fixed'
}

const submit = () => {
    submitLoading.value = true
    axios.post(route('settings.attendance.update'), {
        tolerance_notification: form.tolerance_notification,
        tolerance_clock_in: form.tolerance_clock_in,
        tolerance_clock_out: form.tolerance_clock_out,
        is_absent_force_clock_out: form.is_absent_force_clock_out,
        time_for_force_clockout_type: form.time_for_force_clockout_type,
        time_for_force_clockout_fixed: form.time_for_force_clockout_fixed ? form.time_for_force_clockout_fixed.hours + ':' + form.time_for_force_clockout_fixed.minutes : '',
        time_for_force_clockout_minutes: form.time_for_force_clockout_minutes,
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
    form.tolerance_notification = props.additional.data.tolerance_notification,
        form.tolerance_clock_in = props.additional.data.tolerance_clock_in,
        form.tolerance_clock_out = props.additional.data.tolerance_clock_out,
        form.is_absent_force_clock_out = props.additional.data.is_absent_force_clock_out === "1" ? true : false,
        form.time_for_force_clockout_type = props.additional.data.time_for_force_clockout_type,
        form.time_for_force_clockout_fixed = props.additional.data.time_for_force_clockout_fixed,
        form.time_for_force_clockout_minutes = props.additional.data.time_for_force_clockout_minutes

}


const props = defineProps({
    additional: object(),
    title: string(),

})


</script>

<template>

    <Head :title="title"></Head>
    <VBreadcrumb :routes="breadcrumb" />
    <div class="mb-4 sm:mb-6 flex justify-between items-center">
        <h1 class="text-2xl md:text-3xl text-slate-800 font-bold">Attendance Settings</h1>
    </div>
    <!-- Content -->
    <div class="bg-white shadow-lg rounded-sm mb-8">
        <div class="flex flex-col md:flex-row md:-mr-px">
            <VSidebarSetting :module="additional.menu" />
            <div class="grow">
                <!-- Panel Header -->
                <div class="border-b">
                    <h2 class="text-2xl text-slate-800 font-bold p-6">Attendance</h2>
                </div>

                <!-- Panel Content -->
                <div class="p-6 space-y-6">

                    <!-- Attendance Information -->
                    <section>
                        <div class="sm:flex sm:items-center space-y-4 sm:space-y-0 sm:space-x-4">
                            <div class="sm:w-2/3">
                                <h3 class="text-xl leading-snug text-slate-800 font-bold mb-1">Time Notification</h3>
                                <div class="text-sm text-slate-500">Fill it in to get a push notification reminder
                                    before clock in. The reminder formula is 3 times the interval before the shift start
                                    time according to the time entered.</div>
                                <div class="sm:flex sm:items-center space-y-4 sm:space-y-0 sm:space-x-4 mt-5">

                                    <VInput placeholder="Insert Time for Notification" type="number"
                                        label="Time for Notification (Minutes)" :required="true"
                                        v-model="form.tolerance_notification"
                                        :errorMessage="formError.tolerance_notification"
                                        @update:modelValue="formError.tolerance_notification = ''" :tooltip="true">

                                        <template v-slot:tooltip>
                                            <div class="text-xs text-white">
                                                <div class="font-semibold text-white-800 mb-1">Example.</div>
                                                <div class="mb-0.5">Start Shift : 10.00
                                                    <br />
                                                    Time for Notification : 5 Minutes
                                                    <br />
                                                    *Notification will appear every 09.45 and 09.55
                                                </div>
                                            </div>
                                        </template>
                                    </VInput>
                                </div>
                            </div>
                        </div>

                        <div class="sm:flex sm:items-center space-y-4 sm:space-y-0 sm:space-x-4 mt-10">
                            <div class="sm:w-2/3">
                                <h3 class="text-xl leading-snug text-slate-800 font-bold mb-1">Tolerance Clock in &
                                    Clock Out</h3>
                                <div class="text-sm text-slate-500">Minimum and Maximum time limit to be able to clock
                                    in and clock out.</div>

                                <div class="sm:flex sm:items-center space-y-4 sm:space-y-0 sm:space-x-4 mt-5">
                                    <div class="sm:w-1/2">
                                        <VInput placeholder="Insert Tolerance Clock in" type="number"
                                            label="Tolerance Clock in (Minutes)" :required="true"
                                            v-model="form.tolerance_clock_in"
                                            :errorMessage="formError.tolerance_clock_in"
                                            @update:modelValue="formError.tolerance_clock_in = ''" :tooltip="true">

                                            <template v-slot:tooltip>
                                                <div class="text-xs text-white">
                                                    <div class="font-semibold text-white-800 mb-1">Example.</div>
                                                    <div class="mb-0.5">Start Shift : 10.00
                                                        <br />
                                                        Tolerance Clockin : 30 Minutes
                                                        <br />
                                                        *Employees can clock in after 09.30
                                                    </div>
                                                </div>
                                            </template>
                                        </VInput>
                                    </div>
                                    <div class="sm:w-1/2">
                                        <VInput placeholder="Insert Tolerance Clock out" type="number"
                                            label="Tolerance Clock out (Minutes)" :required="true"
                                            v-model="form.tolerance_clock_out"
                                            :errorMessage="formError.tolerance_clock_out"
                                            @update:modelValue="formError.tolerance_clock_out = ''" :tooltip="true">

                                            <template v-slot:tooltip>
                                                <div class="text-xs text-white">
                                                    <div class="font-semibold text-white-800 mb-1">Example.</div>
                                                    <div class="mb-0.5">End Shift : 10.00
                                                        <br />
                                                        Tolerance Clockout : 30 Minutes
                                                        <br />
                                                        *Employees can’t clock out after 10.30
                                                    </div>
                                                </div>
                                            </template>
                                        </VInput>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="sm:flex sm:items-center space-y-4 sm:space-y-0 sm:space-x-4 mt-10">
                            <div class="sm:w-2/3">
                                <h3 class="text-xl leading-snug text-slate-800 font-bold mb-1">Force Clock Out Count as
                                    Absent <span class="text-rose-500">*</span></h3>
                                <div class="text-sm text-slate-500 mb-2">Turn it on if you want employees who forget the
                                    clockout to be considered absent.</div>
                                <VSwitch v-model="form.is_absent_force_clock_out" label="Action" type="block"
                                    :tooltip="false" />
                            </div>
                        </div>

                        <div class="sm:flex sm:items-center space-y-4 sm:space-y-0 sm:space-x-4 mt-10">
                            <div class="sm:w-2/3">
                                <h3 class="text-xl leading-snug text-slate-800 font-bold mb-1">Time for Force Clock Out
                                </h3>
                                <div class="text-sm text-slate-500">Fill in "<b>Fixed Hours</b>" for All employees will
                                    force clockout at a predetermined hour or Fill in "<b>After Shift</b>" for force
                                    clock out adjusts the time after the shift is complete.
                                    <br> (click the title to choose one, Which one)
                                </div>

                                <div class="sm:flex sm:items-center space-y-4 sm:space-y-0 sm:space-x-4 mt-5">
                                    <div class="sm:w-1/2" v-on:click="selectClockoutTypeFixed">
                                        <VLabel label="Fixed Hours" :tooltip="true" placeholder="">
                                            <template v-slot:tooltip>
                                                <div class="text-xs text-white">
                                                    <div class="font-semibold text-white-800 mb-1">Equalize time force
                                                        clock out in 1 day</div>
                                                    <div class="mb-0.5">All employees will force clockout at a
                                                        predetermined hour
                                                    </div>
                                                </div>
                                            </template>
                                        </VLabel>
                                        <Datepicker v-model="form.time_for_force_clockout_fixed"
                                            @update:modelValue="formError.time_for_force_clockout_fixed = ''"
                                            time-picker position="left" :clearable="true" placeholder="00:00"
                                            :class="{ 'date_error': formError.time_for_force_clockout_fixed }"
                                            :disabled="isSelectClockoutTypeFixed" />
                                        <div class="text-xs mt-1" :class="[{
                                            'text-rose-500': formError.time_for_force_clockout_fixed,
                                        }]" v-if="formError.time_for_force_clockout_fixed">
                                            {{ formError.time_for_force_clockout_fixed }}
                                        </div>
                                    </div>
                                    <div class="sm:w-1/2">
                                        <VInput placeholder="Insert Time After Shift" type="number"
                                            label="After Shift (Minutes)" :required="false"
                                            v-model="form.time_for_force_clockout_minutes"
                                            :errorMessage="formError.time_for_force_clockout_minutes"
                                            @update:modelValue="formError.time_for_force_clockout_minutes = ''"
                                            :tooltip="true" v-on:click="selectClockoutTypeMinutes"
                                            :disabled="isSelectClockoutTypeMinutes">

                                            <template v-slot:tooltip>
                                                <div class="text-xs text-white">
                                                    <div class="font-semibold text-white-800 mb-1">Force clock out
                                                        adjusts the time after the shift is complete</div>
                                                    <div class="mb-0.5">End Shift : 03.00
                                                        <br />
                                                        Minutes after shift : 30 Minutes
                                                        <br />
                                                        *then the employee will clockout automatically in 03.30
                                                    </div>
                                                </div>
                                            </template>
                                        </VInput>
                                    </div>
                                </div>
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