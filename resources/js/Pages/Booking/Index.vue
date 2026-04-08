<script setup>
import { ref, computed, watch } from 'vue';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';

const props = defineProps({ shop: Object });

const step = ref(1);
const totalSteps = 6;

// Form state
const selectedService = ref(null);
const selectedAddons = ref([]);
const selectedStaff = ref(null);
const selectedDate = ref(null);
const selectedTime = ref(null);
const customerForm = ref({ name: '', email: '', phone: '' });
const recurrence = ref({ type: 'none', end_date: '' });

// UI state
const slots = ref([]);
const loadingSlots = ref(false);
const submitting = ref(false);
const error = ref('');
const bookingResult = ref(null);

// Computed
const servicesByCategory = computed(() => {
    const cats = {};
    (props.shop.service_categories || []).forEach(cat => {
        cats[cat.id] = { ...cat, services: [] };
    });
    (props.shop.services || []).forEach(svc => {
        if (cats[svc.service_category_id]) {
            cats[svc.service_category_id].services.push(svc);
        }
    });
    return Object.values(cats).filter(c => c.services.length > 0);
});

const serviceAddons = computed(() => {
    if (!selectedService.value) return [];
    return selectedService.value.add_ons || [];
});

const activeStaff = computed(() => {
    return (props.shop.staff || []).filter(s => s.status === 'active');
});

const totalPrice = computed(() => {
    let price = selectedService.value?.price || 0;
    selectedAddons.value.forEach(a => { price += a.price; });
    return parseFloat(price);
});

const totalDuration = computed(() => {
    let dur = selectedService.value?.duration_minutes || 0;
    selectedAddons.value.forEach(a => { dur += a.duration_minutes; });
    return dur;
});

const addonDuration = computed(() => {
    return selectedAddons.value.reduce((sum, a) => sum + a.duration_minutes, 0);
});

// Calendar
const calendarDays = computed(() => {
    const days = [];
    const today = new Date();
    for (let i = 0; i < 30; i++) {
        const d = new Date(today);
        d.setDate(d.getDate() + i);
        days.push({
            date: d,
            key: formatDate(d),
            day: d.getDate(),
            dayName: d.toLocaleDateString('en-US', { weekday: 'short' }),
            monthName: d.toLocaleDateString('en-US', { month: 'short' }),
            isToday: i === 0,
        });
    }
    return days;
});

const calendarWeeks = computed(() => {
    // Group into weeks starting from first day
    const weeks = [];
    let currentWeek = [];
    calendarDays.value.forEach((day, i) => {
        currentWeek.push(day);
        if (currentWeek.length === 7 || i === calendarDays.value.length - 1) {
            weeks.push(currentWeek);
            currentWeek = [];
        }
    });
    return weeks;
});

function formatDate(d) {
    return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
}

function formatDisplayDate(dateStr) {
    const d = new Date(dateStr + 'T12:00:00');
    return d.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' });
}

function formatTime(t) {
    const [h, m] = t.split(':');
    const hour = parseInt(h);
    const ampm = hour >= 12 ? 'PM' : 'AM';
    const displayHour = hour % 12 || 12;
    return `${displayHour}:${m} ${ampm}`;
}

// Fetch time slots
watch(selectedDate, async (val) => {
    if (!val || !selectedStaff.value || !selectedService.value) return;
    loadingSlots.value = true;
    slots.value = [];
    selectedTime.value = null;
    try {
        const res = await axios.get(`/api/v1/availability/${props.shop.id}`, {
            params: {
                staff_id: selectedStaff.value.id,
                date: val,
                service_id: selectedService.value.id,
                addon_duration: addonDuration.value,
            },
        });
        slots.value = res.data.slots || [];
    } catch (e) {
        slots.value = [];
    } finally {
        loadingSlots.value = false;
    }
});

// Step validation
const canProceed = computed(() => {
    switch (step.value) {
        case 1: return !!selectedService.value;
        case 2: return true; // add-ons optional
        case 3: return !!selectedStaff.value;
        case 4: return !!selectedDate.value && !!selectedTime.value;
        case 5: return customerForm.value.name && customerForm.value.email && customerForm.value.phone;
        case 6: return true;
        default: return false;
    }
});

function nextStep() {
    if (step.value < totalSteps) step.value++;
}

function prevStep() {
    if (step.value > 1) step.value--;
}

function selectService(svc) {
    selectedService.value = svc;
    selectedAddons.value = [];
}

function toggleAddon(addon) {
    const idx = selectedAddons.value.findIndex(a => a.id === addon.id);
    if (idx >= 0) selectedAddons.value.splice(idx, 1);
    else selectedAddons.value.push(addon);
}

function selectStaff(s) {
    selectedStaff.value = s;
    selectedDate.value = null;
    selectedTime.value = null;
    slots.value = [];
}

function selectDate(day) {
    selectedDate.value = day.key;
}

function selectTime(t) {
    selectedTime.value = t;
}

async function confirmBooking() {
    submitting.value = true;
    error.value = '';
    try {
        const res = await axios.post(`/api/v1/book/${props.shop.id}`, {
            service_id: selectedService.value.id,
            staff_id: selectedStaff.value.id,
            date: selectedDate.value,
            time: selectedTime.value,
            addon_ids: selectedAddons.value.map(a => a.id),
            customer_name: customerForm.value.name,
            customer_email: customerForm.value.email,
            customer_phone: customerForm.value.phone,
            recurrence_type: recurrence.value.type,
            recurrence_end_date: recurrence.value.type !== 'none' ? recurrence.value.end_date : null,
        });
        bookingResult.value = res.data;
        step.value = 7; // success
    } catch (e) {
        error.value = e.response?.data?.message || 'Something went wrong. Please try again.';
    } finally {
        submitting.value = false;
    }
}

const stepLabels = ['Service', 'Add-ons', 'Stylist', 'Date & Time', 'Your Info', 'Confirm'];
</script>

<template>
    <Head :title="`Book — ${shop.name}`" />

    <div class="bk-page">
        <!-- Decorative top stripe -->
        <div class="bk-stripe"></div>

        <div class="bk-container">
            <!-- Header -->
            <header class="bk-header">
                <div class="bk-logo-mark"></div>
                <h1 class="bk-shop-name">{{ shop.name }}</h1>
                <p class="bk-shop-address" v-if="shop.address">{{ shop.address }}<span v-if="shop.city">, {{ shop.city }}</span></p>
            </header>

            <!-- Progress -->
            <div class="bk-progress" v-if="step <= 6">
                <div class="bk-progress-track">
                    <div class="bk-progress-fill" :style="{ width: ((step - 1) / (totalSteps - 1)) * 100 + '%' }"></div>
                </div>
                <div class="bk-steps">
                    <button
                        v-for="(label, i) in stepLabels"
                        :key="i"
                        class="bk-step-dot"
                        :class="{ active: step === i + 1, completed: step > i + 1 }"
                        @click="i + 1 < step ? step = i + 1 : null"
                        :disabled="i + 1 > step"
                    >
                        <span class="bk-dot-circle">
                            <svg v-if="step > i + 1" viewBox="0 0 16 16" fill="currentColor" class="bk-check-icon"><path d="M13.78 4.22a.75.75 0 0 1 0 1.06l-7.25 7.25a.75.75 0 0 1-1.06 0L2.22 9.28a.75.75 0 0 1 1.06-1.06L6 10.94l6.72-6.72a.75.75 0 0 1 1.06 0Z"/></svg>
                            <span v-else>{{ i + 1 }}</span>
                        </span>
                        <span class="bk-dot-label">{{ label }}</span>
                    </button>
                </div>
            </div>

            <!-- Error banner -->
            <div v-if="error" class="bk-error">
                <svg viewBox="0 0 20 20" fill="currentColor" class="bk-error-icon"><path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16ZM8.28 7.22a.75.75 0 0 0-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 1 0 1.06 1.06L10 11.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L11.06 10l1.72-1.72a.75.75 0 0 0-1.06-1.06L10 8.94 8.28 7.22Z" clip-rule="evenodd"/></svg>
                {{ error }}
            </div>

            <!-- Step 1: Select Service -->
            <transition name="bk-slide" mode="out-in">
                <div v-if="step === 1" key="step1" class="bk-step-content">
                    <h2 class="bk-step-title">Choose Your Service</h2>
                    <p class="bk-step-sub">Select the service you'd like to book.</p>

                    <div v-for="cat in servicesByCategory" :key="cat.id" class="bk-category-group">
                        <h3 class="bk-category-name">{{ cat.name }}</h3>
                        <div class="bk-service-list">
                            <button
                                v-for="svc in cat.services"
                                :key="svc.id"
                                class="bk-service-card"
                                :class="{ selected: selectedService?.id === svc.id }"
                                @click="selectService(svc)"
                            >
                                <div class="bk-service-info">
                                    <span class="bk-service-name">{{ svc.name }}</span>
                                    <span class="bk-service-desc" v-if="svc.description">{{ svc.description }}</span>
                                </div>
                                <div class="bk-service-meta">
                                    <span class="bk-service-price">${{ parseFloat(svc.price).toFixed(0) }}</span>
                                    <span class="bk-service-duration">{{ svc.duration_minutes }} min</span>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Add-ons -->
                <div v-else-if="step === 2" key="step2" class="bk-step-content">
                    <h2 class="bk-step-title">Enhance Your Visit</h2>
                    <p class="bk-step-sub">Optional add-ons for {{ selectedService?.name }}.</p>

                    <div v-if="serviceAddons.length === 0" class="bk-empty-state">
                        <p>No add-ons available for this service.</p>
                    </div>

                    <div class="bk-addon-list">
                        <button
                            v-for="addon in serviceAddons"
                            :key="addon.id"
                            class="bk-addon-card"
                            :class="{ selected: selectedAddons.some(a => a.id === addon.id) }"
                            @click="toggleAddon(addon)"
                        >
                            <div class="bk-addon-check">
                                <svg v-if="selectedAddons.some(a => a.id === addon.id)" viewBox="0 0 16 16" fill="currentColor"><path d="M13.78 4.22a.75.75 0 0 1 0 1.06l-7.25 7.25a.75.75 0 0 1-1.06 0L2.22 9.28a.75.75 0 0 1 1.06-1.06L6 10.94l6.72-6.72a.75.75 0 0 1 1.06 0Z"/></svg>
                            </div>
                            <div class="bk-addon-info">
                                <span class="bk-addon-name">{{ addon.name }}</span>
                            </div>
                            <div class="bk-addon-meta">
                                <span class="bk-addon-price">+${{ parseFloat(addon.price).toFixed(0) }}</span>
                                <span class="bk-addon-duration">+{{ addon.duration_minutes }} min</span>
                            </div>
                        </button>
                    </div>
                </div>

                <!-- Step 3: Select Stylist -->
                <div v-else-if="step === 3" key="step3" class="bk-step-content">
                    <h2 class="bk-step-title">Choose Your Stylist</h2>
                    <p class="bk-step-sub">Pick the professional you'd like to see.</p>

                    <div class="bk-staff-grid">
                        <button
                            v-for="s in activeStaff"
                            :key="s.id"
                            class="bk-staff-card"
                            :class="{ selected: selectedStaff?.id === s.id }"
                            @click="selectStaff(s)"
                        >
                            <div class="bk-staff-avatar">
                                <img v-if="s.avatar_url || s.photo_path" :src="s.avatar_url || s.photo_path" :alt="s.name" />
                                <span v-else class="bk-staff-initials">{{ s.name.split(' ').map(n => n[0]).join('') }}</span>
                            </div>
                            <span class="bk-staff-name">{{ s.name }}</span>
                            <span class="bk-staff-title" v-if="s.title">{{ s.title }}</span>
                            <span class="bk-staff-exp" v-if="s.years_experience">{{ s.years_experience }}+ yrs experience</span>
                        </button>
                    </div>
                </div>

                <!-- Step 4: Date & Time -->
                <div v-else-if="step === 4" key="step4" class="bk-step-content">
                    <h2 class="bk-step-title">Pick a Date & Time</h2>
                    <p class="bk-step-sub">Select when you'd like your appointment.</p>

                    <!-- Scrollable date strip -->
                    <div class="bk-date-strip-wrapper">
                        <div class="bk-date-strip">
                            <button
                                v-for="day in calendarDays"
                                :key="day.key"
                                class="bk-date-chip"
                                :class="{ selected: selectedDate === day.key, today: day.isToday }"
                                @click="selectDate(day)"
                            >
                                <span class="bk-date-dow">{{ day.dayName }}</span>
                                <span class="bk-date-num">{{ day.day }}</span>
                                <span class="bk-date-month">{{ day.monthName }}</span>
                            </button>
                        </div>
                    </div>

                    <!-- Time slots -->
                    <div v-if="selectedDate" class="bk-time-section">
                        <h3 class="bk-time-heading">{{ formatDisplayDate(selectedDate) }}</h3>

                        <div v-if="loadingSlots" class="bk-loading">
                            <div class="bk-spinner"></div>
                            <span>Finding available times...</span>
                        </div>

                        <div v-else-if="slots.length === 0" class="bk-empty-state">
                            <p>No available times on this date. Please try another day.</p>
                        </div>

                        <div v-else class="bk-time-grid">
                            <button
                                v-for="t in slots"
                                :key="t"
                                class="bk-time-chip"
                                :class="{ selected: selectedTime === t }"
                                @click="selectTime(t)"
                            >
                                {{ formatTime(t) }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Step 5: Customer Info -->
                <div v-else-if="step === 5" key="step5" class="bk-step-content">
                    <h2 class="bk-step-title">Your Information</h2>
                    <p class="bk-step-sub">We'll send your confirmation details here.</p>

                    <div class="bk-form-group">
                        <label class="bk-label">Full Name</label>
                        <input v-model="customerForm.name" type="text" class="bk-input" placeholder="Jane Doe" />
                    </div>
                    <div class="bk-form-group">
                        <label class="bk-label">Email</label>
                        <input v-model="customerForm.email" type="email" class="bk-input" placeholder="jane@example.com" />
                    </div>
                    <div class="bk-form-group">
                        <label class="bk-label">Phone</label>
                        <input v-model="customerForm.phone" type="tel" class="bk-input" placeholder="(555) 123-4567" />
                    </div>

                    <div class="bk-recurrence-section">
                        <label class="bk-label">Repeat This Appointment?</label>
                        <div class="bk-recurrence-options">
                            <button
                                v-for="opt in [{ value: 'none', label: 'Just Once' }, { value: 'weekly', label: 'Weekly' }, { value: 'biweekly', label: 'Every 2 Weeks' }, { value: 'monthly', label: 'Monthly' }]"
                                :key="opt.value"
                                class="bk-recurrence-btn"
                                :class="{ selected: recurrence.type === opt.value }"
                                @click="recurrence.type = opt.value"
                            >
                                {{ opt.label }}
                            </button>
                        </div>
                        <div v-if="recurrence.type !== 'none'" class="bk-form-group" style="margin-top: 12px;">
                            <label class="bk-label">Repeat Until</label>
                            <input v-model="recurrence.end_date" type="date" class="bk-input" />
                        </div>
                    </div>
                </div>

                <!-- Step 6: Confirmation -->
                <div v-else-if="step === 6" key="step6" class="bk-step-content">
                    <h2 class="bk-step-title">Confirm Your Booking</h2>
                    <p class="bk-step-sub">Review the details below and confirm.</p>

                    <div class="bk-summary">
                        <div class="bk-summary-row">
                            <span class="bk-summary-label">Service</span>
                            <span class="bk-summary-value">{{ selectedService?.name }}</span>
                        </div>
                        <div v-if="selectedAddons.length" class="bk-summary-row">
                            <span class="bk-summary-label">Add-ons</span>
                            <span class="bk-summary-value">{{ selectedAddons.map(a => a.name).join(', ') }}</span>
                        </div>
                        <div class="bk-summary-row">
                            <span class="bk-summary-label">Stylist</span>
                            <span class="bk-summary-value">{{ selectedStaff?.name }}</span>
                        </div>
                        <div class="bk-summary-row">
                            <span class="bk-summary-label">Date</span>
                            <span class="bk-summary-value">{{ formatDisplayDate(selectedDate) }}</span>
                        </div>
                        <div class="bk-summary-row">
                            <span class="bk-summary-label">Time</span>
                            <span class="bk-summary-value">{{ formatTime(selectedTime) }}</span>
                        </div>
                        <div class="bk-summary-row">
                            <span class="bk-summary-label">Duration</span>
                            <span class="bk-summary-value">{{ totalDuration }} minutes</span>
                        </div>
                        <div v-if="recurrence.type !== 'none'" class="bk-summary-row">
                            <span class="bk-summary-label">Repeats</span>
                            <span class="bk-summary-value" style="text-transform: capitalize;">{{ recurrence.type }} until {{ recurrence.end_date }}</span>
                        </div>
                        <div class="bk-summary-divider"></div>
                        <div class="bk-summary-row">
                            <span class="bk-summary-label">Name</span>
                            <span class="bk-summary-value">{{ customerForm.name }}</span>
                        </div>
                        <div class="bk-summary-row">
                            <span class="bk-summary-label">Email</span>
                            <span class="bk-summary-value">{{ customerForm.email }}</span>
                        </div>
                        <div class="bk-summary-row">
                            <span class="bk-summary-label">Phone</span>
                            <span class="bk-summary-value">{{ customerForm.phone }}</span>
                        </div>
                        <div class="bk-summary-divider"></div>
                        <div class="bk-summary-row bk-summary-total">
                            <span class="bk-summary-label">Total</span>
                            <span class="bk-summary-value">${{ totalPrice.toFixed(2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Step 7: Success -->
                <div v-else-if="step === 7" key="step7" class="bk-step-content bk-success">
                    <div class="bk-success-icon">
                        <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><circle cx="24" cy="24" r="20"/><path d="M15 25l6 6 12-12"/></svg>
                    </div>
                    <h2 class="bk-step-title">You're All Set!</h2>
                    <p class="bk-step-sub">A confirmation has been sent to <strong>{{ customerForm.email }}</strong>.</p>

                    <div class="bk-summary" style="margin-top: 24px;">
                        <div class="bk-summary-row">
                            <span class="bk-summary-label">Service</span>
                            <span class="bk-summary-value">{{ selectedService?.name }}</span>
                        </div>
                        <div class="bk-summary-row">
                            <span class="bk-summary-label">Stylist</span>
                            <span class="bk-summary-value">{{ selectedStaff?.name }}</span>
                        </div>
                        <div class="bk-summary-row">
                            <span class="bk-summary-label">Date & Time</span>
                            <span class="bk-summary-value">{{ formatDisplayDate(selectedDate) }} at {{ formatTime(selectedTime) }}</span>
                        </div>
                        <div class="bk-summary-divider"></div>
                        <div class="bk-summary-row bk-summary-total">
                            <span class="bk-summary-label">Total</span>
                            <span class="bk-summary-value">${{ totalPrice.toFixed(2) }}</span>
                        </div>
                    </div>
                </div>
            </transition>

            <!-- Navigation -->
            <div class="bk-nav" v-if="step <= 6">
                <button v-if="step > 1" class="bk-btn bk-btn-back" @click="prevStep">
                    <svg viewBox="0 0 20 20" fill="currentColor" style="width:16px;height:16px;"><path fill-rule="evenodd" d="M17 10a.75.75 0 0 1-.75.75H5.612l4.158 3.96a.75.75 0 1 1-1.04 1.08l-5.5-5.25a.75.75 0 0 1 0-1.08l5.5-5.25a.75.75 0 1 1 1.04 1.08L5.612 9.25H16.25A.75.75 0 0 1 17 10Z" clip-rule="evenodd"/></svg>
                    Back
                </button>
                <div v-else></div>

                <button
                    v-if="step < 6"
                    class="bk-btn bk-btn-next"
                    :disabled="!canProceed"
                    @click="nextStep"
                >
                    Continue
                    <svg viewBox="0 0 20 20" fill="currentColor" style="width:16px;height:16px;"><path fill-rule="evenodd" d="M3 10a.75.75 0 0 1 .75-.75h10.638L10.23 5.29a.75.75 0 1 1 1.04-1.08l5.5 5.25a.75.75 0 0 1 0 1.08l-5.5 5.25a.75.75 0 1 1-1.04-1.08l4.158-3.96H3.75A.75.75 0 0 1 3 10Z" clip-rule="evenodd"/></svg>
                </button>

                <button
                    v-if="step === 6"
                    class="bk-btn bk-btn-confirm"
                    :disabled="submitting"
                    @click="confirmBooking"
                >
                    <span v-if="submitting">Booking...</span>
                    <span v-else>Confirm Booking</span>
                </button>
            </div>
        </div>

        <!-- Footer -->
        <footer class="bk-footer">
            <p>Powered by <strong>{{ shop.company?.name || 'ClipFlow' }}</strong></p>
        </footer>
    </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&family=DM+Sans:wght@400;500;600&display=swap');

/* ─── Foundation ─── */
.bk-page {
    --gold: #D4A853;
    --gold-light: #f5e6c4;
    --gold-dark: #b8902e;
    --ink: #1a1a1a;
    --ink-soft: #555;
    --ink-muted: #999;
    --surface: #ffffff;
    --surface-warm: #faf8f5;
    --surface-hover: #f5f2ed;
    --border: #e8e4de;
    --border-light: #f0ece6;
    --radius: 12px;
    --radius-sm: 8px;

    font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, sans-serif;
    background: var(--surface-warm);
    min-height: 100vh;
    color: var(--ink);
    -webkit-font-smoothing: antialiased;
}

.bk-stripe {
    height: 4px;
    background: linear-gradient(90deg, var(--gold-dark), var(--gold), var(--gold-light));
}

.bk-container {
    max-width: 640px;
    margin: 0 auto;
    padding: 32px 20px 120px;
}

/* ─── Header ─── */
.bk-header {
    text-align: center;
    margin-bottom: 32px;
}

.bk-logo-mark {
    width: 48px;
    height: 48px;
    margin: 0 auto 16px;
    background: var(--gold);
    border-radius: 50%;
    position: relative;
}
.bk-logo-mark::after {
    content: '';
    position: absolute;
    inset: 6px;
    border: 2px solid var(--surface);
    border-radius: 50%;
}

.bk-shop-name {
    font-family: 'Cormorant Garamond', Georgia, serif;
    font-size: 32px;
    font-weight: 600;
    letter-spacing: -0.01em;
    color: var(--ink);
    margin: 0 0 4px;
}

.bk-shop-address {
    font-size: 14px;
    color: var(--ink-muted);
    margin: 0;
}

/* ─── Progress ─── */
.bk-progress {
    margin-bottom: 36px;
}

.bk-progress-track {
    height: 2px;
    background: var(--border);
    border-radius: 2px;
    margin-bottom: 12px;
    overflow: hidden;
}

.bk-progress-fill {
    height: 100%;
    background: var(--gold);
    border-radius: 2px;
    transition: width 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.bk-steps {
    display: flex;
    justify-content: space-between;
}

.bk-step-dot {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    background: none;
    border: none;
    cursor: pointer;
    padding: 0;
    transition: opacity 0.2s;
}
.bk-step-dot:disabled { cursor: default; opacity: 0.4; }

.bk-dot-circle {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: 600;
    border: 2px solid var(--border);
    color: var(--ink-muted);
    background: var(--surface);
    transition: all 0.3s;
}

.bk-step-dot.active .bk-dot-circle {
    border-color: var(--gold);
    background: var(--gold);
    color: white;
}

.bk-step-dot.completed .bk-dot-circle {
    border-color: var(--gold);
    background: var(--gold);
    color: white;
}

.bk-check-icon { width: 14px; height: 14px; }

.bk-dot-label {
    font-size: 10px;
    font-weight: 500;
    color: var(--ink-muted);
    text-transform: uppercase;
    letter-spacing: 0.05em;
}
.bk-step-dot.active .bk-dot-label { color: var(--gold-dark); }
.bk-step-dot.completed .bk-dot-label { color: var(--gold-dark); }

/* ─── Step Content ─── */
.bk-step-content {
    min-height: 300px;
}

.bk-step-title {
    font-family: 'Cormorant Garamond', Georgia, serif;
    font-size: 26px;
    font-weight: 600;
    color: var(--ink);
    margin: 0 0 6px;
}

.bk-step-sub {
    font-size: 14px;
    color: var(--ink-muted);
    margin: 0 0 28px;
}

/* ─── Transitions ─── */
.bk-slide-enter-active { animation: bk-slide-in 0.3s ease-out; }
.bk-slide-leave-active { animation: bk-slide-out 0.2s ease-in; }

@keyframes bk-slide-in {
    from { opacity: 0; transform: translateX(24px); }
    to { opacity: 1; transform: translateX(0); }
}
@keyframes bk-slide-out {
    from { opacity: 1; transform: translateX(0); }
    to { opacity: 0; transform: translateX(-24px); }
}

/* ─── Error ─── */
.bk-error {
    display: flex;
    align-items: center;
    gap: 8px;
    background: #fef2f2;
    color: #b91c1c;
    border: 1px solid #fecaca;
    border-radius: var(--radius-sm);
    padding: 12px 16px;
    font-size: 14px;
    margin-bottom: 20px;
}
.bk-error-icon { width: 18px; height: 18px; flex-shrink: 0; }

/* ─── Services ─── */
.bk-category-group { margin-bottom: 24px; }

.bk-category-name {
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: var(--gold-dark);
    margin: 0 0 10px;
    padding-bottom: 6px;
    border-bottom: 1px solid var(--border-light);
}

.bk-service-list { display: flex; flex-direction: column; gap: 8px; }

.bk-service-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 18px;
    background: var(--surface);
    border: 1.5px solid var(--border);
    border-radius: var(--radius);
    cursor: pointer;
    transition: all 0.2s;
    text-align: left;
    width: 100%;
}
.bk-service-card:hover { border-color: var(--gold); background: var(--surface-warm); }
.bk-service-card.selected { border-color: var(--gold); background: var(--gold-light); box-shadow: 0 0 0 1px var(--gold); }

.bk-service-info { display: flex; flex-direction: column; gap: 2px; }
.bk-service-name { font-weight: 600; font-size: 15px; color: var(--ink); }
.bk-service-desc { font-size: 13px; color: var(--ink-muted); }

.bk-service-meta { display: flex; flex-direction: column; align-items: flex-end; gap: 2px; flex-shrink: 0; margin-left: 16px; }
.bk-service-price { font-weight: 700; font-size: 16px; color: var(--ink); }
.bk-service-duration { font-size: 12px; color: var(--ink-muted); }

/* ─── Addons ─── */
.bk-addon-list { display: flex; flex-direction: column; gap: 8px; }

.bk-addon-card {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 14px 18px;
    background: var(--surface);
    border: 1.5px solid var(--border);
    border-radius: var(--radius);
    cursor: pointer;
    transition: all 0.2s;
    text-align: left;
    width: 100%;
}
.bk-addon-card:hover { border-color: var(--gold); }
.bk-addon-card.selected { border-color: var(--gold); background: var(--gold-light); }

.bk-addon-check {
    width: 22px;
    height: 22px;
    border-radius: 6px;
    border: 2px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: all 0.2s;
    color: white;
}
.bk-addon-card.selected .bk-addon-check { background: var(--gold); border-color: var(--gold); }
.bk-addon-check svg { width: 14px; height: 14px; }

.bk-addon-info { flex: 1; }
.bk-addon-name { font-weight: 500; font-size: 15px; color: var(--ink); }

.bk-addon-meta { display: flex; flex-direction: column; align-items: flex-end; gap: 1px; flex-shrink: 0; }
.bk-addon-price { font-weight: 600; font-size: 14px; color: var(--gold-dark); }
.bk-addon-duration { font-size: 12px; color: var(--ink-muted); }

/* ─── Staff ─── */
.bk-staff-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 12px;
}

.bk-staff-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    padding: 24px 16px;
    background: var(--surface);
    border: 1.5px solid var(--border);
    border-radius: var(--radius);
    cursor: pointer;
    transition: all 0.2s;
    text-align: center;
}
.bk-staff-card:hover { border-color: var(--gold); }
.bk-staff-card.selected { border-color: var(--gold); background: var(--gold-light); box-shadow: 0 0 0 1px var(--gold); }

.bk-staff-avatar {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background: var(--surface-warm);
    border: 2px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}
.bk-staff-card.selected .bk-staff-avatar { border-color: var(--gold); }
.bk-staff-avatar img { width: 100%; height: 100%; object-fit: cover; }
.bk-staff-initials { font-family: 'Cormorant Garamond', serif; font-size: 22px; font-weight: 600; color: var(--gold-dark); }

.bk-staff-name { font-weight: 600; font-size: 15px; color: var(--ink); }
.bk-staff-title { font-size: 12px; color: var(--ink-muted); margin-top: -4px; }
.bk-staff-exp { font-size: 11px; color: var(--gold-dark); font-weight: 500; }

/* ─── Date Strip ─── */
.bk-date-strip-wrapper {
    overflow-x: auto;
    margin: 0 -20px 24px;
    padding: 0 20px;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
}
.bk-date-strip-wrapper::-webkit-scrollbar { display: none; }

.bk-date-strip {
    display: flex;
    gap: 8px;
    padding: 4px 0;
}

.bk-date-chip {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 2px;
    padding: 10px 14px;
    background: var(--surface);
    border: 1.5px solid var(--border);
    border-radius: var(--radius);
    cursor: pointer;
    transition: all 0.2s;
    flex-shrink: 0;
    min-width: 62px;
}
.bk-date-chip:hover { border-color: var(--gold); }
.bk-date-chip.selected { border-color: var(--gold); background: var(--gold); color: white; }
.bk-date-chip.today { border-color: var(--gold-dark); }

.bk-date-dow { font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--ink-muted); }
.bk-date-chip.selected .bk-date-dow { color: rgba(255,255,255,0.8); }
.bk-date-num { font-size: 20px; font-weight: 700; line-height: 1; color: var(--ink); }
.bk-date-chip.selected .bk-date-num { color: white; }
.bk-date-month { font-size: 10px; color: var(--ink-muted); font-weight: 500; }
.bk-date-chip.selected .bk-date-month { color: rgba(255,255,255,0.8); }

/* ─── Time ─── */
.bk-time-section { margin-top: 8px; }

.bk-time-heading {
    font-family: 'Cormorant Garamond', serif;
    font-size: 18px;
    font-weight: 600;
    margin: 0 0 16px;
    color: var(--ink);
}

.bk-time-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
    gap: 8px;
}

.bk-time-chip {
    padding: 10px 8px;
    background: var(--surface);
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    text-align: center;
    color: var(--ink);
}
.bk-time-chip:hover { border-color: var(--gold); }
.bk-time-chip.selected { border-color: var(--gold); background: var(--gold); color: white; }

/* ─── Loading ─── */
.bk-loading {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 24px 0;
    color: var(--ink-muted);
    font-size: 14px;
}

.bk-spinner {
    width: 20px;
    height: 20px;
    border: 2px solid var(--border);
    border-top-color: var(--gold);
    border-radius: 50%;
    animation: bk-spin 0.6s linear infinite;
}

@keyframes bk-spin { to { transform: rotate(360deg); } }

.bk-empty-state {
    padding: 32px;
    text-align: center;
    color: var(--ink-muted);
    font-size: 14px;
    background: var(--surface);
    border-radius: var(--radius);
    border: 1px dashed var(--border);
}

/* ─── Form ─── */
.bk-form-group { margin-bottom: 18px; }

.bk-label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: var(--ink-soft);
    margin-bottom: 6px;
}

.bk-input {
    width: 100%;
    padding: 12px 16px;
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    font-size: 15px;
    font-family: inherit;
    color: var(--ink);
    background: var(--surface);
    transition: border-color 0.2s;
    box-sizing: border-box;
}
.bk-input:focus { outline: none; border-color: var(--gold); box-shadow: 0 0 0 3px rgba(212, 168, 83, 0.12); }
.bk-input::placeholder { color: var(--ink-muted); }

.bk-recurrence-section { margin-top: 28px; padding-top: 20px; border-top: 1px solid var(--border-light); }

.bk-recurrence-options {
    display: flex;
    gap: 8px;
    margin-top: 8px;
    flex-wrap: wrap;
}

.bk-recurrence-btn {
    padding: 8px 16px;
    border: 1.5px solid var(--border);
    border-radius: 20px;
    background: var(--surface);
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    color: var(--ink-soft);
}
.bk-recurrence-btn:hover { border-color: var(--gold); }
.bk-recurrence-btn.selected { border-color: var(--gold); background: var(--gold); color: white; }

/* ─── Summary ─── */
.bk-summary {
    background: var(--surface);
    border: 1.5px solid var(--border);
    border-radius: var(--radius);
    padding: 20px;
}

.bk-summary-row {
    display: flex;
    justify-content: space-between;
    align-items: baseline;
    padding: 8px 0;
}

.bk-summary-label { font-size: 13px; color: var(--ink-muted); }
.bk-summary-value { font-size: 14px; font-weight: 500; color: var(--ink); text-align: right; }

.bk-summary-total .bk-summary-label { font-size: 15px; font-weight: 600; color: var(--ink); }
.bk-summary-total .bk-summary-value { font-size: 22px; font-weight: 700; color: var(--gold-dark); font-family: 'Cormorant Garamond', serif; }

.bk-summary-divider {
    height: 1px;
    background: var(--border-light);
    margin: 8px 0;
}

/* ─── Success ─── */
.bk-success { text-align: center; }

.bk-success-icon {
    color: var(--gold);
    margin-bottom: 16px;
}
.bk-success-icon svg { width: 64px; height: 64px; margin: 0 auto; }

.bk-success .bk-summary { text-align: left; }

/* ─── Navigation ─── */
.bk-nav {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 32px;
    padding-top: 24px;
    border-top: 1px solid var(--border-light);
}

.bk-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    border-radius: var(--radius-sm);
    font-size: 14px;
    font-weight: 600;
    font-family: inherit;
    cursor: pointer;
    transition: all 0.2s;
    border: none;
}

.bk-btn-back {
    background: transparent;
    color: var(--ink-muted);
    padding-left: 12px;
}
.bk-btn-back:hover { color: var(--ink); }

.bk-btn-next {
    background: var(--ink);
    color: white;
    margin-left: auto;
}
.bk-btn-next:hover { background: #333; }
.bk-btn-next:disabled { opacity: 0.3; cursor: not-allowed; }

.bk-btn-confirm {
    background: var(--gold);
    color: white;
    margin-left: auto;
    padding: 14px 32px;
    font-size: 15px;
}
.bk-btn-confirm:hover { background: var(--gold-dark); }
.bk-btn-confirm:disabled { opacity: 0.5; cursor: not-allowed; }

/* ─── Footer ─── */
.bk-footer {
    text-align: center;
    padding: 24px;
    font-size: 12px;
    color: var(--ink-muted);
}

/* ─── Mobile tweaks ─── */
@media (max-width: 480px) {
    .bk-container { padding: 24px 16px 120px; }
    .bk-shop-name { font-size: 26px; }
    .bk-step-title { font-size: 22px; }
    .bk-dot-label { display: none; }
    .bk-staff-grid { grid-template-columns: repeat(2, 1fr); }
    .bk-time-grid { grid-template-columns: repeat(3, 1fr); }
}
</style>
