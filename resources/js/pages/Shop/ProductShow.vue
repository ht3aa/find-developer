<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';
import { computed } from 'vue';
import Footer from '@/components/Footer.vue';
import Navbar from '@/components/Navbar.vue';
import ProductImageSlider from '@/components/ProductImageSlider.vue';
import SeoHead from '@/components/SeoHead.vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { shop } from '@/routes';
import { asResourceArray } from '@/utils/asResourceArray';

type ShopProduct = {
    id: number;
    name: string;
    slug: string;
    description: string | null;
    category_id: number;
    is_active: boolean;
    category?: {
        id: number;
        name: string;
        slug: string;
        image_url: string | null;
    };
    images?: {
        id: number;
        image_url: string;
        is_featured: boolean;
        is_active: boolean;
    }[];
    prices?: {
        id: number;
        price: string;
        currency: string;
        is_old_price: boolean;
        is_new_price: boolean;
        is_active: boolean;
    }[];
};

const props = defineProps<{
    product: ShopProduct;
    orderEmail: string | null;
}>();

const page = usePage();
const appName = computed(() => (page.props.name as string) || 'Find Developer');

/** All active images (featured first, matching API order). */
function sliderImageUrls(product: ShopProduct): string[] {
    const imgs = asResourceArray<{
        id: number;
        image_url: string;
        is_featured: boolean;
        is_active: boolean;
    }>(product.images);
    return imgs
        .filter((i) => i.is_active)
        .map((i) => i.image_url)
        .filter(Boolean);
}

function formatMoney(value: string, currency: string): string {
    const code = currency === 'USD' || currency === 'IQD' ? currency : 'IQD';
    const n = Number.parseFloat(value);
    if (Number.isNaN(n)) {
        return value;
    }
    return new Intl.NumberFormat(undefined, {
        style: 'currency',
        currency: code,
    }).format(n);
}

function priceDisplay(product: ShopProduct): {
    current: string | null;
    old: string | null;
} {
    const prices = asResourceArray<{
        id: number;
        price: string;
        currency: string;
        is_old_price: boolean;
        is_new_price: boolean;
        is_active: boolean;
    }>(product.prices);
    const newPrice = prices.find((p) => p.is_new_price);
    const oldPrice = prices.find((p) => p.is_old_price);
    const fallback = prices[0];

    const currentRow = newPrice ?? fallback;
    const currentRaw = currentRow?.price ?? null;
    const currentCurrency = currentRow?.currency ?? 'IQD';
    const oldRaw =
        oldPrice && oldPrice.price !== currentRaw ? oldPrice.price : null;
    const oldCurrency = oldPrice?.currency ?? currentCurrency;

    return {
        current: currentRaw ? formatMoney(currentRaw, currentCurrency) : null,
        old: oldRaw ? formatMoney(oldRaw, oldCurrency) : null,
    };
}

const orderMailtoHref = computed((): string => {
    const email =
        props.orderEmail?.trim() || 'ht3aa2001@gmail.com';
    const subject = encodeURIComponent(`Order: ${props.product.name}`);
    const body = encodeURIComponent(
        `Hi,\n\nI would like to order:\n\n${props.product.name}\n(${props.product.slug})\n\n`,
    );
    return `mailto:${email}?subject=${subject}&body=${body}`;
});

const seoDescription = computed(() => {
    const d = props.product.description?.trim();
    if (d) {
        return d.length > 160 ? `${d.slice(0, 157)}…` : d;
    }
    return `View ${props.product.name} in the ${appName.value} shop.`;
});

const seoImage = computed(() => sliderImageUrls(props.product)[0] ?? undefined);
</script>

<template>
    <SeoHead
        :title="product.name"
        :description="seoDescription"
        :image="seoImage"
        :canonical="`/shop/${product.slug}`"
    />
    <div class="flex min-h-screen flex-col bg-background text-foreground">
        <Navbar />

        <main
            class="mx-auto w-full max-w-7xl flex-1 px-4 py-8 sm:px-6 sm:py-10 lg:px-8"
        >
            <div class="mb-6 sm:mb-8">
                <Button variant="ghost" size="sm" class="-ml-2 gap-1" as-child>
                    <Link :href="shop.url()">
                        <ArrowLeft class="size-4" aria-hidden="true" />
                        Back to shop
                    </Link>
                </Button>
            </div>

            <div
                class="grid grid-cols-1 gap-8 lg:grid-cols-2 lg:gap-10 xl:gap-14"
            >
                <!-- Gallery column (Amazon-style: images left) -->
                <div class="min-w-0 lg:sticky lg:top-24 lg:self-start">
                    <div
                        class="overflow-hidden rounded-xl border border-border bg-card shadow-sm"
                    >
                        <ProductImageSlider
                            :images="sliderImageUrls(product)"
                            :alt="product.name"
                            aspect-class="aspect-square w-full"
                            variant="detail"
                        />
                    </div>
                </div>

                <!-- Buy box + copy (right column on large screens) -->
                <div class="min-w-0 space-y-6">
                    <div>
                        <p
                            v-if="product.category"
                            class="text-sm font-medium tracking-wide text-muted-foreground uppercase"
                        >
                            {{ product.category.name }}
                        </p>
                        <h1
                            class="mt-1 text-2xl font-bold tracking-tight text-foreground sm:text-3xl lg:text-4xl"
                        >
                            {{ product.name }}
                        </h1>
                        <div
                            class="mt-4 flex flex-wrap items-baseline gap-3 border-b border-border pb-6"
                        >
                            <span
                                v-if="priceDisplay(product).current"
                                class="text-2xl font-semibold tabular-nums text-primary sm:text-3xl"
                            >
                                {{ priceDisplay(product).current }}
                            </span>
                            <span
                                v-if="priceDisplay(product).old"
                                class="text-lg text-muted-foreground line-through"
                            >
                                {{ priceDisplay(product).old }}
                            </span>
                            <span
                                v-if="!priceDisplay(product).current"
                                class="text-lg text-muted-foreground"
                            >
                                Price on request
                            </span>
                        </div>
                    </div>

                    <div
                        class="rounded-xl border border-border bg-muted/30 p-4 sm:p-5"
                    >
                        <p class="text-sm text-muted-foreground">
                            Send the amount to Qi card
                            <span class="font-medium text-foreground"
                                >5862997060</span
                            >, then email us with the product you want and attach
                            your payment receipt.
                        </p>
                        <p class="mt-2 text-sm text-muted-foreground">
                            Delivery fee between 3,000–5,000 IQD.
                        </p>
                        <div class="mt-4 flex flex-col gap-3 sm:flex-row">
                            <Button
                                size="lg"
                                class="w-full sm:w-auto sm:min-w-[12rem]"
                                as-child
                            >
                                <a :href="orderMailtoHref">Order</a>
                            </Button>
                        </div>
                    </div>

                    <Card v-if="product.description" class="border-border/80">
                        <CardHeader>
                            <CardTitle class="text-lg">Description</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <CardDescription
                                class="text-base leading-relaxed whitespace-pre-wrap text-foreground"
                            >
                                {{ product.description }}
                            </CardDescription>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </main>

        <Footer />
    </div>
</template>
