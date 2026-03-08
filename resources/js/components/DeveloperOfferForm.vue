<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { Send } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import SearchableSelect from '@/components/SearchableSelect.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { availabilityTypeOptions } from '@/utils/developerEnums';

const props = defineProps<{
    open: boolean;
    storeUrl: string;
    selectedDeveloperIds: number[];
}>();

const selectedCount = computed(() => props.selectedDeveloperIds.length);

const emit = defineEmits<{
    (e: 'update:open', value: boolean): void;
    (e: 'success'): void;
}>();

const form = ref({
    company_name: '',
    job_title_id: '',
    message: '',
    salary_range: '',
    work_type: '',
    contact_email: '',
});
const jobTitleOpen = ref(false);
const workTypeOpen = ref(false);
const submitting = ref(false);
const errors = ref<Record<string, string>>({});

function close(): void {
    emit('update:open', false);
    form.value = {
        company_name: '',
        job_title_id: '',
        message: '',
        salary_range: '',
        work_type: '',
        contact_email: '',
    };
    errors.value = {};
}

function submit(): void {
    if (!props.storeUrl || props.selectedDeveloperIds.length === 0) return;
    submitting.value = true;
    errors.value = {};
    router.post(
        props.storeUrl,
        {
            developer_ids: props.selectedDeveloperIds,
            company_name: form.value.company_name,
            job_title_id: form.value.job_title_id
                ? parseInt(form.value.job_title_id, 10)
                : null,
            message: form.value.message,
            salary_range: form.value.salary_range || null,
            work_type: form.value.work_type || null,
            contact_email: form.value.contact_email,
        },
        {
            preserveScroll: true,
            onFinish: () => {
                submitting.value = false;
            },
            onSuccess: () => {
                close();
                emit('success');
            },
            onError: (errs) => {
                errors.value = errs as Record<string, string>;
            },
        },
    );
}
</script>

<template>
    <Dialog :open="open" @update:open="emit('update:open', $event)">
        <DialogContent class="max-h-[90vh] overflow-y-auto sm:max-w-lg">
            <DialogHeader>
                <DialogTitle>Send offer to developers</DialogTitle>
                <DialogDescription>
                    You are sending an offer to {{ selectedCount }} developer{{
                        selectedCount === 1 ? '' : 's'
                    }}. Fill in the details below.
                </DialogDescription>
            </DialogHeader>
            <form class="space-y-4" novalidate @submit.prevent="submit">
                <div class="space-y-2">
                    <Label for="offer-company">Company name</Label>
                    <Input
                        id="offer-company"
                        v-model="form.company_name"
                        type="text"
                        placeholder="Acme Inc."
                    />
                    <p
                        v-if="errors.company_name"
                        class="text-sm text-destructive"
                    >
                        {{ errors.company_name }}
                    </p>
                </div>
                <div class="space-y-2">
                    <Label for="offer-job-title">Position (job title)</Label>
                    <SearchableSelect
                        id="offer-job-title"
                        v-model="form.job_title_id"
                        :open="jobTitleOpen"
                        options-url="/api/job-titles?for_form=1"
                        placeholder="e.g. Backend Developer"
                        @update:open="jobTitleOpen = $event"
                    />
                    <p
                        v-if="errors.job_title_id"
                        class="text-sm text-destructive"
                    >
                        {{ errors.job_title_id }}
                    </p>
                </div>
                <div class="space-y-2">
                    <Label for="offer-message">Message</Label>
                    <textarea
                        id="offer-message"
                        v-model="form.message"
                        placeholder="Describe the opportunity..."
                        rows="4"
                        class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-none"
                    />
                    <p v-if="errors.message" class="text-sm text-destructive">
                        {{ errors.message }}
                    </p>
                </div>
                <div class="space-y-2">
                    <Label for="offer-salary">Salary range (optional)</Label>
                    <Input
                        id="offer-salary"
                        v-model="form.salary_range"
                        type="text"
                        placeholder="e.g. $80k - $120k/year"
                    />
                    <p
                        v-if="errors.salary_range"
                        class="text-sm text-destructive"
                    >
                        {{ errors.salary_range }}
                    </p>
                </div>
                <div class="space-y-2">
                    <Label for="offer-work-type">Work type (optional)</Label>
                    <SearchableSelect
                        id="offer-work-type"
                        v-model="form.work_type"
                        :open="workTypeOpen"
                        :options="availabilityTypeOptions"
                        placeholder="e.g. Remote, Full-time"
                        @update:open="workTypeOpen = $event"
                    />
                    <p v-if="errors.work_type" class="text-sm text-destructive">
                        {{ errors.work_type }}
                    </p>
                </div>
                <div class="space-y-2">
                    <Label for="offer-contact">Contact email</Label>
                    <Input
                        id="offer-contact"
                        v-model="form.contact_email"
                        type="email"
                        placeholder="hr@company.com"
                    />
                    <p
                        v-if="errors.contact_email"
                        class="text-sm text-destructive"
                    >
                        {{ errors.contact_email }}
                    </p>
                </div>
                <DialogFooter>
                    <Button type="button" variant="outline" @click="close">
                        Cancel
                    </Button>
                    <Button type="submit" :disabled="submitting">
                        <Send v-if="!submitting" class="size-4" />
                        <span>{{
                            submitting ? 'Sending...' : 'Send offer'
                        }}</span>
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
