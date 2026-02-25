import type { Component } from 'vue';
import * as outlineIcons from '@heroicons/vue/24/outline';
import * as solidIcons from '@heroicons/vue/24/solid';
import * as lucideIcons from 'lucide-vue-next';

/**
 * Icon format: "prefix:icon-name"
 * - li:rocket           = Lucide Icons
 * - hi:arrow-down-left = Heroicons outline
 * - hi2:arrow-down-left = Heroicons solid (or hs:)
 */
function kebabToPascal(kebab: string): string {
    return kebab
        .split('-')
        .map((part) => part.charAt(0).toUpperCase() + part.slice(1).toLowerCase())
        .join('');
}

function getHeroiconFromMap(
    map: Record<string, Component>,
    iconName: string,
): Component | null {
    const key = `${kebabToPascal(iconName)}Icon`;
    return (map[key] as Component) ?? null;
}

function getLucideIconFromMap(
    map: Record<string, Component>,
    iconName: string,
): Component | null {
    const key = kebabToPascal(iconName);
    return (map[key] as Component) ?? null;
}

export function resolveBadgeIcon(
    iconString: string | null | undefined,
): Component | null {
    if (!iconString || typeof iconString !== 'string') {
        return null;
    }

    const parts = iconString.split(':');
    if (parts.length !== 2) {
        return null;
    }

    const [prefix, iconName] = parts;

    if (prefix === 'li') {
        return getLucideIconFromMap(lucideIcons, iconName);
    }

    const map = prefix === 'hi2' || prefix === 'hs' ? solidIcons : outlineIcons;
    return getHeroiconFromMap(map, iconName);
}
