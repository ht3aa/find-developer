<script setup lang="ts">
import { router, usePage } from '@inertiajs/vue3';
import { Loader2 } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';

const props = defineProps<{
    newsletterStoreUrl: string;
}>();

const page = usePage();
const errors = computed(
    () => (page.props.errors as Record<string, string> | undefined) ?? {},
);

const email = ref('');
const submitting = ref(false);

function submitNewsletter(): void {
    if (!props.newsletterStoreUrl || !email.value.trim()) {
        return;
    }
    submitting.value = true;
    router.post(
        props.newsletterStoreUrl,
        { email: email.value.trim() },
        {
            preserveScroll: true,
            onFinish: () => {
                submitting.value = false;
            },
        },
    );
}
</script>

<template>
    <section
        class="border-t border-border bg-muted/20 px-4 py-12 sm:px-6 lg:px-8"
        aria-labelledby="newsletter-spotlight-label"
    >
        <div class="mx-auto w-full max-w-md">
            <p
                id="newsletter-spotlight-label"
                class="mb-3 text-center text-sm font-medium text-muted-foreground"
            >
                Get developers spotlight in your inbox
            </p>
            <form
                class="flex flex-col gap-2 sm:flex-row sm:items-center"
                @submit.prevent="submitNewsletter"
            >
                <Input
                    v-model="email"
                    type="email"
                    name="email"
                    placeholder="you@example.com"
                    autocomplete="email"
                    class="flex-1"
                    :disabled="submitting"
                    :aria-invalid="!!errors?.email"
                    :aria-describedby="
                        errors?.email ? 'newsletter-email-error' : undefined
                    "
                />
                <Button
                    type="submit"
                    class="shrink-0"
                    :disabled="submitting || !email.trim()"
                >
                    <Loader2
                        v-if="submitting"
                        class="size-4 animate-spin"
                        aria-hidden
                    />
                    <span>{{ submitting ? 'Subscribing…' : 'Subscribe' }}</span>
                </Button>
            </form>
            <p
                v-if="errors.email"
                id="newsletter-email-error"
                class="mt-2 text-center text-sm text-destructive"
                role="alert"
            >
                {{ errors.email }}
            </p>
        </div>
    </section>
</template>
