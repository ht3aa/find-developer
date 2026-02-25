<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { Spinner } from '@/components/ui/spinner';
import AuthBase from '@/layouts/AuthLayout.vue';
import { logout } from '@/routes';
import { send } from '@/routes/verification';

defineProps<{
    status?: string;
}>();
</script>

<template>
    <AuthBase
        title="Verify email"
        description="Please verify your email address by clicking on the link we just emailed to you."
    >
        <Head title="Email verification" />

        <div
            v-if="status === 'verification-link-sent'"
            class="rounded-md border border-green-200 bg-green-50 px-3 py-2 text-center text-sm font-medium text-green-800 dark:border-green-800 dark:bg-green-950/50 dark:text-green-200"
        >
            A new verification link has been sent to the email address you
            provided during registration.
        </div>

        <Form
            v-bind="send.form()"
            class="flex flex-col gap-6 text-center"
            v-slot="{ processing }"
        >
            <Button :disabled="processing" variant="secondary" class="w-full">
                <Spinner v-if="processing" />
                Resend verification email
            </Button>

            <Separator class="my-2" />

            <TextLink
                :href="logout()"
                as="button"
                class="mx-auto block text-sm text-muted-foreground"
            >
                Log out
            </TextLink>
        </Form>
    </AuthBase>
</template>
