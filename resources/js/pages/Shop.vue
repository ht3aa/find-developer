<script setup lang="ts">
import { Head, router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import Footer from '@/components/Footer.vue';
import Hero from '@/components/Hero.vue';
import Navbar from '@/components/Navbar.vue';
import Pagination from '@/components/Pagination.vue';
import ProductImageSlider from '@/components/ProductImageSlider.vue';
import SeoHead from '@/components/SeoHead.vue';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import shopProduct from '@/routes/shop/product';
import { asResourceArray } from '@/utils/asResourceArray';

type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

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

type Props = {
    products: {
        data: ShopProduct[];
        meta: {
            current_page: number;
            last_page: number;
            per_page: number;
            total: number;
            from: number | null;
            to: number | null;
            links: PaginationLink[];
        };
    };
};

defineProps<Props>();

const page = usePage();
const appName = computed(() => (page.props.name as string) || 'Find Developer');

/** Featured images only if any exist; otherwise all images (for the card slider). */
function sliderImageUrls(product: ShopProduct): string[] {
    const imgs = asResourceArray<{
        id: number;
        image_url: string;
        is_featured: boolean;
        is_active: boolean;
    }>(product.images);
    const featured = imgs.filter((i) => i.is_featured);
    const list = featured.length > 0 ? featured : imgs;
    return list.map((i) => i.image_url).filter(Boolean);
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

function visitProduct(product: ShopProduct, event: MouseEvent): void {
    if ((event.target as HTMLElement).closest('button')) {
        return;
    }
    router.visit(shopProduct.show.url(product.slug));
}

function visitProductOnKeydown(
    product: ShopProduct,
    event: KeyboardEvent,
): void {
    if (event.key !== 'Enter' && event.key !== ' ') {
        return;
    }
    event.preventDefault();
    router.visit(shopProduct.show.url(product.slug));
}
</script>

<template>
    <SeoHead
        title="Shop"
        :description="`The ${appName} shop for developers—browse gear and resources by category.`"
        canonical="/shop"
    />
    <Head />
    <div class="flex min-h-screen flex-col bg-background text-foreground">
        <Navbar />

        <Hero
            badge="For developers"
            title="Shop"
            description="A shop built for developers: browse by category, compare prices at a glance, and find what fits your stack and your project—all in one place."
            primary-action-label="Browse below"
            primary-action-href="#shop-grid"
        />

        <main
            id="shop-grid"
            tabindex="-1"
            class="relative z-0 mx-auto w-full max-w-7xl flex-1 scroll-mt-24 px-4 py-12 sm:px-6 lg:px-8"
        >
            <p
                v-if="products.meta.total === 0"
                class="rounded-lg border border-dashed border-border bg-muted/30 px-6 py-16 text-center text-muted-foreground"
            >
                No products available yet. Check back soon.
            </p>

            <div v-else class="space-y-10">
                <p class="text-sm text-muted-foreground">
                    Showing
                    <span class="font-medium text-foreground">{{
                        products.meta.from ?? 0
                    }}</span>
                    –
                    <span class="font-medium text-foreground">{{
                        products.meta.to ?? 0
                    }}</span>
                    of
                    <span class="font-medium text-foreground">{{
                        products.meta.total
                    }}</span>
                    products
                </p>

                <ul
                    class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
                >
                    <li v-for="product in products.data" :key="product.id">
                        <Card
                            role="link"
                            tabindex="0"
                            class="h-full cursor-pointer overflow-hidden border-border/80 ring-offset-background transition-shadow outline-none hover:shadow-md hover:ring-2 hover:ring-primary/20 focus-visible:ring-2 focus-visible:ring-primary"
                            @click="visitProduct(product, $event)"
                            @keydown="visitProductOnKeydown(product, $event)"
                        >
                            <ProductImageSlider
                                :images="sliderImageUrls(product)"
                                :alt="product.name"
                                aspect-class="aspect-[4/3] w-full"
                                variant="card"
                            />
                            <CardHeader class="space-y-1 pb-2">
                                <p
                                    v-if="product.category"
                                    class="text-xs font-medium tracking-wide text-muted-foreground uppercase"
                                >
                                    {{ product.category.name }}
                                </p>
                                <CardTitle class="text-lg leading-snug">
                                    {{ product.name }}
                                </CardTitle>
                                <CardDescription
                                    v-if="product.description"
                                    class="line-clamp-2"
                                >
                                    {{ product.description }}
                                </CardDescription>
                            </CardHeader>
                            <CardContent class="pt-0">
                                <div
                                    class="flex flex-wrap items-baseline gap-2"
                                >
                                    <span
                                        v-if="priceDisplay(product).current"
                                        class="text-lg font-semibold tabular-nums text-primary"
                                    >
                                        {{ priceDisplay(product).current }}
                                    </span>
                                    <span
                                        v-if="priceDisplay(product).old"
                                        class="text-sm text-muted-foreground line-through"
                                    >
                                        {{ priceDisplay(product).old }}
                                    </span>
                                    <span
                                        v-if="!priceDisplay(product).current"
                                        class="text-sm text-muted-foreground"
                                    >
                                        Price on request
                                    </span>
                                </div>
                            </CardContent>
                        </Card>
                    </li>
                </ul>

                <Pagination
                    v-if="products.meta.links.length > 1"
                    :links="products.meta.links"
                />
            </div>
        </main>

        <Footer />
    </div>
</template>
