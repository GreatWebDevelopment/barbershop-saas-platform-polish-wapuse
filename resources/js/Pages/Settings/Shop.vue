<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    shop: Object,
});

const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

const defaultHours = {};
days.forEach(day => {
    defaultHours[day] = { open: '09:00', close: '17:00', closed: false };
});

const form = useForm({
    name: props.shop?.name ?? '',
    address: props.shop?.address ?? '',
    city: props.shop?.city ?? '',
    state: props.shop?.state ?? '',
    zip: props.shop?.zip ?? '',
    phone: props.shop?.phone ?? '',
    hours: props.shop?.hours ?? defaultHours,
    logo: null,
});

const logoPreview = ref(props.shop?.logo_path ? `/storage/${props.shop.logo_path}` : null);

const onLogoChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.logo = file;
        logoPreview.value = URL.createObjectURL(file);
    }
};

const submit = () => {
    form.post(route('settings.shop.update'), {
        _method: 'patch',
        forceFormData: true,
    });
};
</script>

<template>
    <Head title="Shop Settings" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-[#f5f0e8]">Shop Settings</h2>
        </template>

        <div class="max-w-3xl">
            <form @submit.prevent="submit" class="space-y-8">
                <!-- Logo -->
                <div class="rounded-xl border border-gray-800 bg-[#1a1a2e] p-6">
                    <h3 class="text-lg font-semibold text-[#f5f0e8] mb-4">Logo</h3>
                    <div class="flex items-center gap-6">
                        <div class="h-20 w-20 rounded-xl bg-[#16162a] border border-gray-700 flex items-center justify-center overflow-hidden">
                            <img v-if="logoPreview" :src="logoPreview" class="h-full w-full object-cover" />
                            <svg v-else class="h-8 w-8 text-gray-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5a2.25 2.25 0 002.25-2.25V6.75a2.25 2.25 0 00-2.25-2.25H3.75A2.25 2.25 0 001.5 6.75v10.5A2.25 2.25 0 003.75 21z" />
                            </svg>
                        </div>
                        <div>
                            <label class="cursor-pointer inline-flex items-center px-4 py-2 border border-[#D4A853] text-[#D4A853] rounded-lg text-sm font-medium hover:bg-[#D4A853] hover:text-[#1a1a2e] transition">
                                Upload logo
                                <input type="file" accept="image/*" class="hidden" @change="onLogoChange" />
                            </label>
                            <p class="mt-1 text-xs text-gray-500">PNG, JPG up to 2MB</p>
                        </div>
                    </div>
                    <InputError class="mt-2" :message="form.errors.logo" />
                </div>

                <!-- Shop Info -->
                <div class="rounded-xl border border-gray-800 bg-[#1a1a2e] p-6">
                    <h3 class="text-lg font-semibold text-[#f5f0e8] mb-4">Shop Information</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-[#f5f0e8] mb-1.5">Shop name</label>
                            <input
                                v-model="form.name"
                                type="text"
                                required
                                class="w-full rounded-lg border border-gray-700 bg-[#16162a] px-4 py-3 text-[#f5f0e8] placeholder-gray-500 focus:border-[#D4A853] focus:ring-1 focus:ring-[#D4A853] transition"
                            />
                            <InputError class="mt-1" :message="form.errors.name" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-[#f5f0e8] mb-1.5">Address</label>
                            <input
                                v-model="form.address"
                                type="text"
                                class="w-full rounded-lg border border-gray-700 bg-[#16162a] px-4 py-3 text-[#f5f0e8] placeholder-gray-500 focus:border-[#D4A853] focus:ring-1 focus:ring-[#D4A853] transition"
                                placeholder="123 Main St"
                            />
                            <InputError class="mt-1" :message="form.errors.address" />
                        </div>

                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-[#f5f0e8] mb-1.5">City</label>
                                <input
                                    v-model="form.city"
                                    type="text"
                                    class="w-full rounded-lg border border-gray-700 bg-[#16162a] px-4 py-3 text-[#f5f0e8] placeholder-gray-500 focus:border-[#D4A853] focus:ring-1 focus:ring-[#D4A853] transition"
                                />
                                <InputError class="mt-1" :message="form.errors.city" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-[#f5f0e8] mb-1.5">State</label>
                                <input
                                    v-model="form.state"
                                    type="text"
                                    maxlength="2"
                                    class="w-full rounded-lg border border-gray-700 bg-[#16162a] px-4 py-3 text-[#f5f0e8] placeholder-gray-500 focus:border-[#D4A853] focus:ring-1 focus:ring-[#D4A853] transition uppercase"
                                />
                                <InputError class="mt-1" :message="form.errors.state" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-[#f5f0e8] mb-1.5">ZIP</label>
                                <input
                                    v-model="form.zip"
                                    type="text"
                                    maxlength="10"
                                    class="w-full rounded-lg border border-gray-700 bg-[#16162a] px-4 py-3 text-[#f5f0e8] placeholder-gray-500 focus:border-[#D4A853] focus:ring-1 focus:ring-[#D4A853] transition"
                                />
                                <InputError class="mt-1" :message="form.errors.zip" />
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-[#f5f0e8] mb-1.5">Phone</label>
                            <input
                                v-model="form.phone"
                                type="tel"
                                class="w-full rounded-lg border border-gray-700 bg-[#16162a] px-4 py-3 text-[#f5f0e8] placeholder-gray-500 focus:border-[#D4A853] focus:ring-1 focus:ring-[#D4A853] transition"
                                placeholder="(555) 123-4567"
                            />
                            <InputError class="mt-1" :message="form.errors.phone" />
                        </div>
                    </div>
                </div>

                <!-- Hours of Operation -->
                <div class="rounded-xl border border-gray-800 bg-[#1a1a2e] p-6">
                    <h3 class="text-lg font-semibold text-[#f5f0e8] mb-4">Hours of Operation</h3>
                    <div class="space-y-3">
                        <div v-for="day in days" :key="day" class="flex items-center gap-4">
                            <div class="w-28 text-sm font-medium text-[#f5f0e8] capitalize">{{ day }}</div>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input
                                    type="checkbox"
                                    :checked="!form.hours[day]?.closed"
                                    @change="form.hours[day] = { ...form.hours[day], closed: !$event.target.checked }"
                                    class="rounded border-gray-600 bg-[#16162a] text-[#D4A853] focus:ring-[#D4A853]"
                                />
                                <span class="text-xs text-gray-400">Open</span>
                            </label>
                            <template v-if="!form.hours[day]?.closed">
                                <input
                                    type="time"
                                    v-model="form.hours[day].open"
                                    class="rounded-lg border border-gray-700 bg-[#16162a] px-3 py-2 text-sm text-[#f5f0e8] focus:border-[#D4A853] focus:ring-1 focus:ring-[#D4A853]"
                                />
                                <span class="text-gray-500 text-sm">to</span>
                                <input
                                    type="time"
                                    v-model="form.hours[day].close"
                                    class="rounded-lg border border-gray-700 bg-[#16162a] px-3 py-2 text-sm text-[#f5f0e8] focus:border-[#D4A853] focus:ring-1 focus:ring-[#D4A853]"
                                />
                            </template>
                            <span v-else class="text-sm text-gray-500">Closed</span>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <PrimaryButton
                        class="bg-[#D4A853] hover:bg-[#c09743] text-[#1a1a2e] font-semibold px-6 py-3 rounded-lg focus:ring-[#D4A853] transition"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                    >
                        Save changes
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
