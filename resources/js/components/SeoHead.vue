<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { computed, onMounted, onBeforeUnmount } from 'vue';

const props = withDefaults(
    defineProps<{
        title: string;
        description?: string | null;
        image?: string | null;
        /** Full canonical URL. If relative, will be resolved against appUrl. */
        canonical?: string | null;
        /** If true, adds noindex, nofollow. */
        noindex?: boolean;
        /** JSON-LD object(s). Will be stringified and output as application/ld+json. */
        jsonLd?: object | object[] | null;
        /** Open Graph type, e.g. "website" or "article". */
        ogType?: string;
    }>(),
    {
        description: null,
        image: null,
        canonical: null,
        noindex: false,
        jsonLd: null,
        ogType: 'website',
    },
);

const page = usePage();
const appUrl = computed(() => (page.props.appUrl as string) || '');
const appName = computed(() => (page.props.name as string) || 'Find Developer');

const fullTitle = computed(() => {
    if (!props.title || props.title === appName.value) return appName.value;
    return `${props.title} | ${appName.value}`;
});

const canonicalUrl = computed(() => {
    const c = props.canonical;
    if (!c) return '';
    if (c.startsWith('http')) return c;
    const base = appUrl.value.replace(/\/$/, '');
    const path = c.startsWith('/') ? c : `/${c}`;
    return `${base}${path}`;
});

const imageUrl = computed(() => {
    const img = props.image;
    if (!img) return null;
    if (img.startsWith('http')) return img;
    const base = appUrl.value.replace(/\/$/, '');
    const path = img.startsWith('/') ? img : `/${img}`;
    return `${base}${path}`;
});

const metaDescription = computed(() => props.description?.slice(0, 160) ?? '');

const jsonLdString = computed(() => {
    if (!props.jsonLd) return '';
    const data = Array.isArray(props.jsonLd) ? props.jsonLd : [props.jsonLd];
    return JSON.stringify(data.length === 1 ? data[0] : data);
});

let jsonLdEl: HTMLScriptElement | null = null;

onMounted(() => {
    if (!jsonLdString.value) return;
    jsonLdEl = document.createElement('script');
    jsonLdEl.type = 'application/ld+json';
    jsonLdEl.textContent = jsonLdString.value;
    document.head.appendChild(jsonLdEl);
});

onBeforeUnmount(() => {
    if (jsonLdEl?.parentNode) {
        jsonLdEl.parentNode.removeChild(jsonLdEl);
        jsonLdEl = null;
    }
});
</script>

<template>
    <Head :title="fullTitle">
        <meta v-if="metaDescription" name="description" :content="metaDescription" />
        <meta v-if="noindex" name="robots" content="noindex, nofollow" />

        <!-- Open Graph -->
        <meta property="og:type" :content="ogType" />
        <meta property="og:site_name" :content="appName" />
        <meta property="og:title" :content="fullTitle" />
        <meta v-if="metaDescription" property="og:description" :content="metaDescription" />
        <meta v-if="canonicalUrl" property="og:url" :content="canonicalUrl" />
        <meta v-if="imageUrl" property="og:image" :content="imageUrl" />

        <!-- Twitter Card -->
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" :content="fullTitle" />
        <meta v-if="metaDescription" name="twitter:description" :content="metaDescription" />
        <meta v-if="imageUrl" name="twitter:image" :content="imageUrl" />

        <!-- Canonical -->
        <link v-if="canonicalUrl" rel="canonical" :href="canonicalUrl" />
    </Head>
</template>
