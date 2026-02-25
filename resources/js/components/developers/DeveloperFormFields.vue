<script setup lang="ts">
import { computed, ref } from 'vue';
import FileUpload from '@/components/FileUpload.vue';
import InputError from '@/components/InputError.vue';
import SearchableSelect from '@/components/SearchableSelect.vue';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Separator } from '@/components/ui/separator';

const availabilityTypeOptions = [
    { value: 'full-time', label: 'Full-time' },
    { value: 'part-time', label: 'Part-time' },
    { value: 'freelance', label: 'Freelance' },
    { value: 'hybrid', label: 'Hybrid' },
    { value: 'remote', label: 'Remote' },
    { value: 'remote-full-time', label: 'Remote Full-time' },
    { value: 'hybrid-full-time', label: 'Hybrid Full-time' },
];

const statusOptions = [
    { value: 'pending', label: 'Pending' },
    { value: 'approved', label: 'Approved' },
    { value: 'rejected', label: 'Rejected' },
    { value: 'experience_changed', label: 'Experience Changed' },
];

const props = withDefaults(
    defineProps<{
        modelValue: Record<string, unknown>;
        errors?: Record<string, string>;
        jobTitles: { id: number; name: string }[];
        users?: { id: number; name: string; email: string }[];
        showUserSelect?: boolean;
        showAdminFields?: boolean;
    }>(),
    {
        errors: () => ({}),
        users: () => [],
        showUserSelect: false,
        showAdminFields: false,
    },
);

const emit = defineEmits<{
    (e: 'update:modelValue', value: Record<string, unknown>): void;
}>();

const formData = computed({
    get: () => props.modelValue,
    set: (v) => emit('update:modelValue', v),
});

const jobTitleSelectOpen = ref(false);
const skillSelectOpen = ref(false);
const availabilityTypeSelectOpen = ref(false);
const statusSelectOpen = ref(false);
const cvFile = ref<File | null>(null);
const cvUploadRef = ref<InstanceType<typeof FileUpload> | null>(null);

const jobTitleModel = computed({
    get: () => {
        const name = (formData.value.job_title as { name?: string })?.name;
        return props.jobTitles.find((j) => j.name === name)?.id.toString() ?? null;
    },
    set: (v: string | null) => {
        const next = { ...formData.value };
        if (!v) {
            next.job_title = { name: '' };
        } else {
            const j = props.jobTitles.find((x) => x.id.toString() === v);
            if (j) next.job_title = { name: j.name };
        }
        emit('update:modelValue', next);
    },
});

const skillsModel = computed({
    get: () =>
        ((formData.value.skills as { name: string }[]) ?? []).map((s) => s.name),
    set: (v: string[] | string | null) => {
        const arr = Array.isArray(v) ? v : v ? [v] : [];
        emit('update:modelValue', { ...formData.value, skills: arr.map((name) => ({ name: String(name) })) });
    },
});

function onJobTitleOpenChange(open: boolean): void {
    jobTitleSelectOpen.value = open;
    if (open) {
        skillSelectOpen.value = false;
        availabilityTypeSelectOpen.value = false;
        statusSelectOpen.value = false;
    }
}

function onSkillOpenChange(open: boolean): void {
    skillSelectOpen.value = open;
    if (open) {
        jobTitleSelectOpen.value = false;
        availabilityTypeSelectOpen.value = false;
        statusSelectOpen.value = false;
    }
}

function onAvailabilityTypeOpenChange(open: boolean): void {
    availabilityTypeSelectOpen.value = open;
    if (open) {
        jobTitleSelectOpen.value = false;
        skillSelectOpen.value = false;
        statusSelectOpen.value = false;
    }
}

function onStatusOpenChange(open: boolean): void {
    statusSelectOpen.value = open;
    if (open) {
        jobTitleSelectOpen.value = false;
        skillSelectOpen.value = false;
        availabilityTypeSelectOpen.value = false;
    }
}

function onUserSelect(userId: string | null): void {
    const next = { ...formData.value };
    next.user_id = userId ? Number(userId) : null;
    if (userId) {
        const user = props.users.find((u) => u.id.toString() === userId);
        if (user) {
            next.name = user.name;
            next.email = user.email;
        }
    }
    emit('update:modelValue', next);
}

defineExpose({ cvFile, cvUploadRef, clearCv: () => { cvFile.value = null; cvUploadRef.value?.clear(); } });
</script>

<template>
    <div class="space-y-6">
        <div v-if="showUserSelect" class="grid gap-2">
            <Label for="user_id">User</Label>
            <SearchableSelect
                id="user_id"
                :model-value="String(formData.user_id ?? '')"
                :options="users.map((u) => ({ value: String(u.id), label: `${u.name} (${u.email})` }))"
                placeholder="Select a user without a developer profile"
                @update:model-value="onUserSelect"
            />
            <InputError :message="errors?.user_id" />
        </div>

        <div class="space-y-4">
            <div class="grid gap-2">
                <Label for="name">Name</Label>
                <Input
                    id="name"
                    v-model="formData.name"
                    name="name"
                    required
                    placeholder="Developer name"
                    class="transition-colors focus-visible:ring-2"
                />
                <InputError :message="errors?.name" />
            </div>

            <div class="grid gap-2">
                <Label for="email">Email</Label>
                <Input
                    id="email"
                    v-model="formData.email"
                    name="email"
                    type="email"
                    required
                    placeholder="email@example.com"
                    class="transition-colors focus-visible:ring-2"
                />
                <InputError :message="errors?.email" />
            </div>

            <div class="grid gap-2">
                <Label for="phone">Phone</Label>
                <Input
                    id="phone"
                    v-model="formData.phone"
                    name="phone"
                    placeholder="+964..."
                    class="transition-colors focus-visible:ring-2"
                />
            </div>

            <div class="grid gap-2">
                <Label for="job_title">Job title</Label>
                <SearchableSelect
                    id="job_title"
                    v-model="jobTitleModel"
                    :open="jobTitleSelectOpen"
                    :options="jobTitles.map((j) => ({ value: String(j.id), label: j.name }))"
                    placeholder="e.g. Backend Developer"
                    @update:open="onJobTitleOpenChange"
                />
            </div>

            <div class="grid gap-2">
                <Label for="years_of_experience">Years of experience</Label>
                <Input
                    id="years_of_experience"
                    v-model.number="formData.years_of_experience"
                    name="years_of_experience"
                    type="number"
                    min="0"
                    max="100"
                    required
                    class="transition-colors focus-visible:ring-2"
                />
                <InputError :message="errors?.years_of_experience" />
            </div>

            <div class="grid gap-2">
                <Label for="bio">Bio</Label>
                <textarea
                    id="bio"
                    v-model="formData.bio"
                    name="bio"
                    rows="3"
                    class="flex min-h-[80px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                    placeholder="Brief bio..."
                />
            </div>
        </div>

        <Separator />

        <h4 class="text-sm font-medium">URLs</h4>
        <div class="grid gap-2 sm:grid-cols-2">
            <div class="grid gap-2">
                <Label for="portfolio_url">Portfolio</Label>
                <Input
                    id="portfolio_url"
                    v-model="formData.portfolio_url"
                    name="portfolio_url"
                    type="url"
                    placeholder="https://..."
                    class="transition-colors focus-visible:ring-2"
                />
            </div>
            <div class="grid gap-2">
                <Label for="github_url">GitHub</Label>
                <Input
                    id="github_url"
                    v-model="formData.github_url"
                    name="github_url"
                    type="url"
                    placeholder="https://github.com/..."
                    class="transition-colors focus-visible:ring-2"
                />
            </div>
            <div class="grid gap-2">
                <Label for="linkedin_url">LinkedIn</Label>
                <Input
                    id="linkedin_url"
                    v-model="formData.linkedin_url"
                    name="linkedin_url"
                    type="url"
                    placeholder="https://linkedin.com/..."
                    class="transition-colors focus-visible:ring-2"
                />
            </div>
            <div class="grid gap-2">
                <Label for="youtube_url">YouTube</Label>
                <Input
                    id="youtube_url"
                    v-model="formData.youtube_url"
                    name="youtube_url"
                    type="url"
                    placeholder="https://youtube.com/..."
                    class="transition-colors focus-visible:ring-2"
                />
            </div>
        </div>

        <FileUpload
            id="cv"
            ref="cvUploadRef"
            v-model="cvFile"
            label="CV (PDF, max 10MB)"
            accept=".pdf,application/pdf"
            :existing-url="(formData.cv_path_url as string) ?? null"
            existing-label="View current CV"
            :error="errors?.cv"
        />

        <Separator />

        <div class="grid gap-2">
            <Label for="skill_ids">Skills</Label>
            <SearchableSelect
                id="skill_ids"
                v-model="skillsModel"
                :open="skillSelectOpen"
                options-url="/api/skills"
                placeholder="e.g. Laravel, Vue"
                multiple
                :max-options="50"
                @update:open="onSkillOpenChange"
            />
        </div>

        <div class="grid gap-2">
            <Label for="availability_type">Availability type</Label>
            <SearchableSelect
                id="availability_type"
                :model-value="((formData.availability_type ?? []) as { value: string }[]).map((a) => a.value)"
                :open="availabilityTypeSelectOpen"
                :options="availabilityTypeOptions"
                placeholder="e.g. Full-time, Remote"
                multiple
                @update:model-value="
                    (v) => {
                        const arr = Array.isArray(v) ? v : v ? [v] : [];
                        const next = { ...formData };
                        next.availability_type = arr.map((val) => {
                            const opt = availabilityTypeOptions.find((o) => o.value === val);
                            return { value: String(val), label: opt?.label ?? String(val) };
                        });
                        emit('update:modelValue', next);
                    }
                "
                @update:open="onAvailabilityTypeOpenChange"
            />
        </div>

        <div class="flex flex-wrap items-center gap-4">
            <div class="flex items-center space-x-2">
                <input type="hidden" name="is_available" :value="formData.is_available ? '1' : '0'" />
                <Checkbox
                    id="is_available"
                    v-model:checked="formData.is_available"
                    name="is_available"
                    value="1"
                />
                <Label for="is_available">Available</Label>
            </div>

            <template v-if="showAdminFields">
                <div class="flex items-center space-x-2">
                    <input type="hidden" name="recommended_by_us" :value="formData.recommended_by_us ? '1' : '0'" />
                    <Checkbox
                        id="recommended_by_us"
                        v-model:checked="formData.recommended_by_us"
                        name="recommended_by_us"
                        value="1"
                    />
                    <Label for="recommended_by_us">Recommended by us</Label>
                </div>
            </template>
        </div>

        <div v-if="showAdminFields" class="grid gap-2">
            <Label for="status">Status</Label>
            <SearchableSelect
                id="status"
                :model-value="(formData.status as string) ?? 'pending'"
                :open="statusSelectOpen"
                :options="statusOptions"
                placeholder="Select status"
                @update:model-value="(v) => emit('update:modelValue', { ...formData, status: v ?? 'pending' })"
                @update:open="onStatusOpenChange"
            />
            <InputError :message="errors?.status" />
        </div>
    </div>
</template>
