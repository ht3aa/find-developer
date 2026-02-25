<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, ThumbsUp } from 'lucide-vue-next';
import Footer from '@/components/Footer.vue';
import InputError from '@/components/InputError.vue';
import Navbar from '@/components/Navbar.vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { home } from '@/routes';
import type { Developer } from '@/types/developer';

const props = defineProps<{
    developer: Developer;
    storeUrl: string;
}>();

const form = useForm({
    recommendation_note: '',
});

function submit(): void {
    form.post(props.storeUrl);
}
</script>

<template>
    <Head :title="`Recommend ${developer.name} | Find Developer`" />
    <div class="flex min-h-screen flex-col bg-background text-foreground">
        <Navbar />

        <main class="flex-1">
            <div class="mx-auto max-w-2xl px-4 py-12">
                <Link
                    :href="developer.profile_url ?? home()"
                    class="mb-6 inline-flex items-center gap-2 text-sm font-medium text-muted-foreground transition-colors hover:text-foreground"
                >
                    <ArrowLeft class="size-4" />
                    Back to {{ developer.name }}'s profile
                </Link>

                <h1 class="text-2xl font-bold">
                    Recommend {{ developer.name }}
                </h1>
                <p class="mt-2 text-muted-foreground">
                    Share your experience working with {{ developer.name }}. Your recommendation will be reviewed before it appears on their profile.
                </p>

                <form
                    class="mt-8 space-y-6"
                    @submit.prevent="submit"
                >
                    <div class="space-y-2">
                        <Label for="recommendation_note">
                            Your recommendation
                        </Label>
                        <textarea
                            id="recommendation_note"
                            v-model="form.recommendation_note"
                            name="recommendation_note"
                            rows="5"
                            maxlength="2000"
                            placeholder="Describe your experience working with this developer..."
                            class="placeholder:text-muted-foreground border-input focus-visible:ring-ring/50 focus-visible:border-ring w-full rounded-lg border bg-transparent px-3 py-2 text-base shadow-xs transition-[color,box-shadow] outline-none focus-visible:ring-[3px] disabled:pointer-events-none disabled:opacity-50 md:text-sm aria-invalid:border-destructive aria-invalid:ring-destructive/20"
                            :aria-invalid="!!form.errors.recommendation_note"
                        />
                        <p class="text-xs text-muted-foreground">
                            {{ form.recommendation_note.length }} / 2000 characters (optional)
                        </p>
                        <InputError :message="form.errors.recommendation_note" />
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <Button
                            type="submit"
                            :disabled="form.processing"
                            class="gap-2"
                        >
                            <ThumbsUp class="size-4" />
                            {{ form.processing ? 'Submitting...' : 'Submit Recommendation' }}
                        </Button>
                        <Button
                            variant="outline"
                            as-child
                        >
                            <Link :href="developer.profile_url ?? home()">
                                Cancel
                            </Link>
                        </Button>
                    </div>
                </form>
            </div>
        </main>

        <Footer />
    </div>
</template>
