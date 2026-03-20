<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import Footer from '@/components/Footer.vue';
import Hero from '@/components/Hero.vue';
import Navbar from '@/components/Navbar.vue';
import SeoHead from '@/components/SeoHead.vue';

const page = usePage();
const appName = computed(() => (page.props.name as string) || 'Find Developer');
const supportEmail = 'ht3aa2001@gmail.com';
</script>

<template>
    <SeoHead
        title="Privacy policy"
        :description="`How ${appName} collects, uses, and protects your personal information.`"
        canonical="/privacy-policy"
    />
    <Head />
    <div class="flex min-h-screen flex-col bg-background text-foreground">
        <Navbar />

        <Hero
            badge="Legal"
            title="Privacy policy"
            :description="`Last updated: ${new Date().getFullYear()}. How we handle data on ${appName}.`"
        />

        <article
            class="mx-auto w-full max-w-3xl px-4 pb-16 sm:px-6 lg:px-8"
        >
            <div
                class="prose prose-neutral dark:prose-invert max-w-none prose-headings:scroll-mt-24 prose-a:text-primary"
            >
                <p>
                    This privacy policy describes how {{ appName }} (“we”, “us”)
                    processes personal information when you use our website and
                    related services (the “Services”). By using the Services,
                    you agree to this policy. If you do not agree, please do not
                    use the Services.
                </p>

                <h2>Information we collect</h2>
                <ul>
                    <li>
                        <strong>Account and profile data:</strong> When you
                        register or sign in, we collect information such as your
                        name, email address, password (stored securely hashed),
                        account type, and optional profile fields you choose to
                        provide (for example LinkedIn URL).
                    </li>
                    <li>
                        <strong>Developer portfolio content:</strong> If you
                        create or maintain a developer profile, we store the
                        information you submit or upload—such as skills,
                        experience, projects, blog posts, availability, and
                        compensation preferences—to display your portfolio and
                        power discovery features.
                    </li>
                    <li>
                        <strong>Hackathon participation:</strong> We process
                        data related to events you join, teams, votes, and
                        subscriptions you opt into for hackathon updates.
                    </li>
                    <li>
                        <strong>Messaging:</strong> When you use in-app
                        messaging, we store message content and related metadata
                        needed to deliver conversations between users.
                    </li>
                    <li>
                        <strong>Newsletter:</strong> If you subscribe to our
                        newsletter, we store your email address to send updates
                        you requested.
                    </li>
                    <li>
                        <strong>Technical data:</strong> Like most websites, we
                        receive standard server and device information (for
                        example IP address, browser type, and cookies or similar
                        technologies) to operate the Services, maintain
                        security, and understand aggregate usage.
                    </li>
                </ul>

                <h2>How we use information</h2>
                <p>We use personal information to:</p>
                <ul>
                    <li>Provide, operate, and improve the Services;</li>
                    <li>
                        Authenticate users, enforce account settings, and
                        protect against fraud or abuse;
                    </li>
                    <li>
                        Display public developer profiles, blogs, badges,
                        hackathon pages, and related content you choose to make
                        available;
                    </li>
                    <li>
                        Send transactional or service-related messages (for
                        example verification, security notices, or responses you
                        initiate);
                    </li>
                    <li>
                        Send marketing emails only where you have opted in (such
                        as newsletter subscription);
                    </li>
                    <li>Comply with law and respond to lawful requests.</li>
                </ul>

                <h2>Legal bases (where applicable)</h2>
                <p>
                    Depending on your region, we may rely on one or more of:
                    performance of a contract, legitimate interests (such as
                    securing and improving the Services), consent (where
                    required), or legal obligation.
                </p>

                <h2>Sharing of information</h2>
                <p>
                    We do not sell your personal information. We may share data
                    with:
                </p>
                <ul>
                    <li>
                        <strong>Service providers</strong> who assist us with
                        hosting, storage, email delivery, analytics, or similar
                        functions, subject to appropriate safeguards;
                    </li>
                    <li>
                        <strong>Other users</strong> as needed to provide the
                        Services—for example, public profile fields visible on
                        the site, or messages you send through the platform;
                    </li>
                    <li>
                        <strong>Authorities</strong> when required by law or to
                        protect rights, safety, and security.
                    </li>
                </ul>

                <h2>Retention</h2>
                <p>
                    We retain information for as long as your account is active,
                    as needed to provide the Services, and as required to meet
                    legal, accounting, or reporting obligations. You may request
                    deletion of your account subject to any exceptions we must
                    keep under law.
                </p>

                <h2>Security</h2>
                <p>
                    We implement technical and organizational measures designed
                    to protect personal information. No method of transmission or
                    storage is completely secure; we encourage strong passwords
                    and careful sharing of account credentials.
                </p>

                <h2>Your choices and rights</h2>
                <p>
                    Depending on where you live, you may have rights to access,
                    correct, delete, or export certain personal data, or to
                    object to or restrict certain processing. To exercise these
                    rights or ask questions about this policy, contact us at
                    <a :href="`mailto:${supportEmail}`">{{ supportEmail }}</a>.
                </p>

                <h2>Children</h2>
                <p>
                    The Services are not directed at children under the age
                    required for consent in your jurisdiction. We do not
                    knowingly collect personal information from children.
                </p>

                <h2>International transfers</h2>
                <p>
                    Your information may be processed in countries other than
                    your own. Where required, we use appropriate safeguards for
                    cross-border transfers.
                </p>

                <h2>Changes to this policy</h2>
                <p>
                    We may update this privacy policy from time to time. We will
                    post the revised version on this page and update the “last
                    updated” reference when we do so. Continued use of the
                    Services after changes constitutes your acceptance of the
                    updated policy, to the extent permitted by law.
                </p>

                <h2>Contact</h2>
                <p>
                    Questions about privacy or this policy:
                    <a :href="`mailto:${supportEmail}`">{{ supportEmail }}</a>.
                </p>
            </div>
        </article>

        <Footer />
    </div>
</template>
