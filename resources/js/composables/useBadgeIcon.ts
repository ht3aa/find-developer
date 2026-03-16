import { type Component, defineAsyncComponent, markRaw } from 'vue';

/**
 * Icon format: "prefix:icon-name"
 * - li:rocket           = Lucide Icons
 * - hi:arrow-down-left  = Heroicons outline
 * - hi2:arrow-down-left = Heroicons solid (or hs:)
 *
 * Icons are loaded on demand via dynamic imports so the full icon
 * libraries are never bundled into the initial payload.
 */

function kebabToPascal(kebab: string): string {
    return kebab
        .split('-')
        .map(
            (part) =>
                part.charAt(0).toUpperCase() + part.slice(1).toLowerCase(),
        )
        .join('');
}

const iconCache = new Map<string, Component>();

async function loadIcon(iconString: string): Promise<Component | null> {
    const parts = iconString.split(':');
    if (parts.length !== 2) return null;

    const [prefix, iconName] = parts;

    if (prefix === 'li') {
        const key = kebabToPascal(iconName);
        const mod = await import('lucide-vue-next');
        return (mod as Record<string, Component>)[key] ?? null;
    }

    const key = `${kebabToPascal(iconName)}Icon`;
    if (prefix === 'hi2' || prefix === 'hs') {
        const mod = await import('@heroicons/vue/24/solid');
        return (mod as Record<string, Component>)[key] ?? null;
    }

    const mod = await import('@heroicons/vue/24/outline');
    return (mod as Record<string, Component>)[key] ?? null;
}

export function resolveBadgeIcon(
    iconString: string | null | undefined,
): Component | null {
    if (!iconString || typeof iconString !== 'string') {
        return null;
    }

    if (iconCache.has(iconString)) {
        return iconCache.get(iconString)!;
    }

    const asyncIcon = defineAsyncComponent(() =>
        loadIcon(iconString).then((icon) => {
            if (icon) {
                iconCache.set(iconString, markRaw(icon));
                return icon;
            }
            return { render: () => null };
        }),
    );

    return asyncIcon;
}
