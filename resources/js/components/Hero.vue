<script setup lang="ts">
import { router, usePage } from '@inertiajs/vue3';
import { ArrowDown, Loader2 } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import DotGrid from '@/components/animations/DotGrid.vue';
import Duck from '@/components/Duck.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';

const props = withDefaults(
    defineProps<{
        badge?: string;
        title?: string;
        description?: string;
        primaryActionLabel?: string;
        primaryActionHref?: string;
        successMessage?: string;
        /** When set, shows the newsletter signup field in the hero. */
        newsletterStoreUrl?: string;
    }>(),
    {
        badge: 'Find developers',
        title: 'Find the right developer for your project',
        description:
            'Browse vetted developers, filter by skills and experience, and connect with the best match for your team.',
    },
);

const page = usePage();
const errors = computed(() => (page.props.errors as Record<string, string> | undefined) ?? {});
const email = ref('');
const submitting = ref(false);

function scrollToSearch(): void {
    document
        .querySelector(props.primaryActionHref ?? '')
        ?.scrollIntoView({ behavior: 'smooth' });
}

function submitNewsletter(): void {
    if (!props.newsletterStoreUrl || !email.value.trim()) return;
    submitting.value = true;
    router.post(props.newsletterStoreUrl, { email: email.value.trim() }, {
        preserveScroll: true,
        onFinish: () => { submitting.value = false; },
    });
}
</script>

<template>
    <section
        class="relative overflow-hidden border-b border-border bg-gradient-to-b from-background via-muted/30 to-background"
    >
        <!-- Decorative background elements -->
        <div
            class="pointer-events-none absolute inset-0 overflow-hidden"
            aria-hidden="true"
        >
    <DotGrid
      :dot-size="3"
      :gap="100"
      base-color="#C4C2BC"
      active-color="#F59E0B"
      :proximity="140"
      :speed-trigger="80"
      :shock-radius="220"
      :shock-strength="4"
      :max-speed="4000"
      :resistance="800"
      :return-duration="1.8"
      class-name="custom-dot-grid"
    />
            <div
                class="absolute -left-40 -top-40 size-80 rounded-full bg-primary/10 blur-3xl"
            />
            <div
                class="absolute -right-40 top-1/2 size-96 -translate-y-1/2 rounded-full bg-secondary/10 blur-3xl"
            />
            <div
                class="absolute bottom-0 left-1/2 -translate-x-1/2 size-[500px] rounded-full bg-primary/5 blur-3xl"
            />
        </div>

        <!-- Decorative ducks (click to quack) -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="pointer-events-auto absolute left-[8%] top-[15%] rotate-[-15deg]">
                <div class="hero-duck hero-duck-1">
                    <Duck size="size-14" class="opacity-90" />
                </div>
            </div>
            <div class="pointer-events-auto absolute right-[12%] top-[25%] rotate-[20deg]">
                <div class="hero-duck hero-duck-2">
                    <Duck size="size-10" class="opacity-85" />
                </div>
            </div>
            <div class="pointer-events-auto absolute left-[15%] bottom-[20%] rotate-[10deg]">
                <div class="hero-duck hero-duck-3">
                    <Duck size="size-12" class="opacity-80" />
                </div>
            </div>
            <div class="pointer-events-auto absolute right-[8%] bottom-[25%] rotate-[-20deg]">
                <div class="hero-duck hero-duck-4">
                    <Duck size="size-11" class="opacity-85" />
                </div>
            </div>
        </div>

        <div class="relative mx-auto max-w-5xl px-4 py-20 sm:px-6 sm:py-24 lg:px-8 lg:py-32">
            <div class="flex flex-col items-center text-center">
                <!-- Success message -->
                <div
                    v-if="props.successMessage"
                    class="mb-6 w-full max-w-2xl rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-800 dark:border-green-800 dark:bg-green-950/50 dark:text-green-200"
                >
                    {{ props.successMessage }}
                </div>
                <!-- Badge -->
                <div
                    v-if="props.badge != null"
                    class="mb-6 flex items-center justify-center gap-2"
                >
                    <Duck size="size-8" class="rotate-[-12deg] shrink-0" />
                    <Badge
                        variant="secondary"
                        class="rounded-full border border-primary/20 bg-primary/5 px-4 py-1.5 text-sm font-medium text-primary shadow-sm"
                    >
                        {{ props.badge }}
                    </Badge>
                    <Duck size="size-8" class="rotate-[12deg] shrink-0" />
                </div>

                <!-- Title -->
                <h1
                    v-if="props.title != null"
                    class="text-3xl font-bold tracking-tight sm:text-4xl md:text-5xl lg:text-6xl"
                >
                    {{ props.title }}
                </h1>
                <p
                    v-if="props.description != null"
                    class="mt-4 max-w-2xl text-base text-muted-foreground sm:text-lg md:text-xl"
                >
                    {{ props.description }}
                </p>

                <!-- CTA button -->
                <Button
                    v-if="props.primaryActionLabel != null"
                    size="lg"
                    class="mt-10 gap-2 rounded-xl text-base shadow-lg transition-all duration-200 hover:shadow-xl"
                    @click="scrollToSearch"
                >
                    {{ props.primaryActionLabel }}
                    <ArrowDown class="size-4" aria-hidden="true" />
                </Button>

                <!-- Newsletter signup -->
                <div
                    v-if="props.newsletterStoreUrl"
                    class="mt-12 w-full max-w-md"
                >
                    <p class="mb-3 text-sm font-medium text-muted-foreground">
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
                            :aria-describedby="errors?.email ? 'newsletter-email-error' : undefined"
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
                        class="mt-2 text-sm text-destructive"
                        role="alert"
                    >
                        {{ errors.email }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Bottom accent gradient -->
        <div
            class="absolute inset-x-0 bottom-0 h-1 bg-gradient-to-r from-transparent via-primary/60 to-transparent opacity-80"
        />
    </section>
</template>

<style scoped>
@keyframes float {
    0%,
    100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-10px);
    }
}

.hero-duck {
    animation: float 3s ease-in-out infinite;
}

.hero-duck-1 {
    animation-duration: 2.8s;
    animation-delay: 0s;
}

.hero-duck-2 {
    animation-duration: 3.2s;
    animation-delay: 0.4s;
}

.hero-duck-3 {
    animation-duration: 3s;
    animation-delay: 0.7s;
}

.hero-duck-4 {
    animation-duration: 2.6s;
    animation-delay: 0.2s;
}
</style>
