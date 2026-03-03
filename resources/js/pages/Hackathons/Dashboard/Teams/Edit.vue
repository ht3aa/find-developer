<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { Pencil } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import FileUpload from '@/components/FileUpload.vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { index as hackathonsIndex } from '@/routes/hackathons';
import type { BreadcrumbItem } from '@/types';

const props = defineProps<{
    hackathon: { id: number; title: string };
    team: { id: number; title: string; logo: string | null; logo_url: string | null };
    updateUrl: string;
}>();

const page = usePage();
const formErrors = computed(() => (page.props.errors as Record<string, string>) ?? {});
const flashSuccess = computed(() => (page.props.flash as { success?: string })?.success);

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Hackathons', href: hackathonsIndex().url },
    { title: props.hackathon.title, href: '#' },
    { title: 'Teams', href: `/dashboard/hackathons/${props.hackathon.id}/teams` },
    { title: 'Edit', href: '#' },
];

const title = ref(props.team.title);
const logoFile = ref<File | null>(null);
const logoUploadRef = ref<InstanceType<typeof FileUpload> | null>(null);
const submitting = ref(false);

watch(
    () => props.team,
    (t) => {
        title.value = t.title;
    },
    { immediate: true },
);

function submitForm(): void {
    const payload: Record<string, unknown> = { title: title.value };
    if (logoFile.value) {
        payload.logo = logoFile.value;
    }
    submitting.value = true;
    router.put(props.updateUrl, payload, {
        preserveScroll: true,
        forceFormData: !!logoFile.value,
        onSuccess: () => {
            logoFile.value = null;
            logoUploadRef.value?.clear();
        },
        onFinish: () => {
            submitting.value = false;
        },
    });
}
</script>

<template>
    <Head :title="`Edit ${team.title}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="w-full space-y-6 rounded-xl p-4">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary/10">
                    <Pencil class="h-6 w-6 text-primary" />
                </div>
                <div>
                    <Heading
                        title="Edit team"
                        :description="`Update ${team.title}`"
                    />
                </div>
            </div>

            <Card>
                <form @submit.prevent="submitForm">
                    <CardHeader class="pb-4">
                        <h3 class="text-sm font-medium text-muted-foreground">
                            Team details
                        </h3>
                    </CardHeader>
                    <CardContent class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                        <div class="grid gap-2 lg:col-span-2">
                            <Label for="title">Title <span class="text-destructive">*</span></Label>
                            <Input
                                id="title"
                                v-model="title"
                                name="title"
                                required
                                placeholder="e.g. Team Alpha"
                                class="transition-colors focus-visible:ring-2"
                            />
                            <InputError :message="formErrors.title" />
                        </div>

                        <FileUpload
                            id="logo"
                            ref="logoUploadRef"
                            v-model="logoFile"
                            label="Logo (image, max 2MB)"
                            accept=".jpeg,.jpg,.png,.gif,.webp,image/jpeg,image/png,image/gif,image/webp"
                            :existing-url="team.logo_url ?? null"
                            existing-label="View current logo"
                            :error="formErrors.logo"
                        />

                        <div class="flex flex-wrap items-center gap-3 pt-2 lg:col-span-2">
                            <Button :disabled="submitting" type="submit">
                                {{ submitting ? 'Updating...' : 'Update team' }}
                            </Button>
                            <Button variant="outline" as-child>
                                <Link :href="`/dashboard/hackathons/${hackathon.id}/teams`">
                                    Cancel
                                </Link>
                            </Button>
                            <Transition
                                enter-active-class="transition ease-out duration-200"
                                enter-from-class="opacity-0 translate-y-1"
                                leave-active-class="transition ease-in duration-150"
                                leave-to-class="opacity-0"
                            >
                                <span
                                    v-show="flashSuccess"
                                    class="inline-flex items-center gap-1.5 rounded-md bg-green-500/10 px-2.5 py-1 text-sm font-medium text-green-700 dark:text-green-400"
                                >
                                    <span class="h-1.5 w-1.5 rounded-full bg-green-500" />
                                    {{ flashSuccess }}
                                </span>
                            </Transition>
                        </div>
                    </CardContent>
                </form>
            </Card>
        </div>
    </AppLayout>
</template>
