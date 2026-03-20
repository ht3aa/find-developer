import {
    type ApiRoleBand,
    decodeRoleBandsFromFilter,
    encodeRoleBandsForFilter,
    presetIdsToApiBands,
} from '@/lib/roleBandFilters';

export type DeveloperFilters = {
    search?: string;
    jobTitle?: string[];
    skill?: string[];
    badge?: string[];
    availabilityType?: string[];
    hasUrls?: string[];
    isAvailable?: string;
    isRecommended?: string;
    yearsMin?: string;
    yearsMax?: string;
    /**
     * OR’d job title + experience bands (JSON on the wire). When set, global job title and years filters are omitted.
     */
    roleBands?: ApiRoleBand[];
    /** Comma-separated developer IDs to restrict results. */
    ids?: number[];
};

/** URL + API parse result including hydrated role bands (from `role_bands` JSON or legacy `preset_ids`). */
export type DeveloperFiltersFromUrl = DeveloperFilters & {
    initialRoleBands: ApiRoleBand[];
};

function toFilterValue(val: string | string[] | null | undefined): string {
    if (val == null) return '';
    return Array.isArray(val) ? val.filter(Boolean).join(',') : String(val);
}

function parseFilterArray(val: string | null | undefined): string[] {
    if (val == null || val === '') return [];
    return val.includes(',')
        ? val
              .split(',')
              .map((s) => s.trim())
              .filter(Boolean)
        : [val];
}

export function buildDevelopersApiUrl(
    base: string,
    filters: DeveloperFilters,
): string {
    const params = new URLSearchParams();
    if (filters.search?.trim())
        params.set('filter[search]', filters.search.trim());
    const hasRoleBands = (filters.roleBands?.length ?? 0) > 0;
    if (hasRoleBands) {
        params.set(
            'filter[role_bands]',
            encodeRoleBandsForFilter(filters.roleBands),
        );
    } else {
        const jobTitleVal = toFilterValue(filters.jobTitle);
        if (jobTitleVal) params.set('filter[job_title.name]', jobTitleVal);
    }
    const skillVal = toFilterValue(filters.skill);
    if (skillVal) params.set('filter[skill]', skillVal);
    const badgeVal = toFilterValue(filters.badge);
    if (badgeVal) params.set('filter[badge]', badgeVal);
    const availabilityTypeVal = toFilterValue(filters.availabilityType);
    if (availabilityTypeVal)
        params.set('filter[availability_type]', availabilityTypeVal);
    const hasUrlsVal = toFilterValue(filters.hasUrls);
    if (hasUrlsVal) params.set('filter[has_urls]', hasUrlsVal);
    if (filters.isAvailable && filters.isAvailable !== 'all')
        params.set('filter[is_available]', filters.isAvailable);
    if (filters.isRecommended && filters.isRecommended !== 'all')
        params.set('filter[is_recommended]', filters.isRecommended);
    if (!hasRoleBands) {
        if (filters.yearsMin) params.set('filter[years_min]', filters.yearsMin);
        if (filters.yearsMax) params.set('filter[years_max]', filters.yearsMax);
    }
    if (filters.ids?.length) {
        params.set('filter[ids]', filters.ids.join(','));
    }
    const q = params.toString();
    return q ? `${base}?${q}` : base;
}

export function parseFiltersFromUrl(): DeveloperFiltersFromUrl {
    const params = new URLSearchParams(window.location.search);
    const filter: Record<string, string> = {};
    for (const [k, v] of params.entries()) {
        const m = k.match(/^filter\[(.+)\]$/);
        if (m) filter[m[1]] = v;
    }
    const idsRaw = filter.ids;
    const ids = idsRaw
        ? idsRaw
              .split(',')
              .map((s) => parseInt(s.trim(), 10))
              .filter((n) => !Number.isNaN(n))
        : undefined;

    const fromJson = filter.role_bands
        ? decodeRoleBandsFromFilter(filter.role_bands)
        : null;
    const presetIds = parseFilterArray(filter.preset_ids);
    const fromPresets =
        fromJson && fromJson.length > 0
            ? null
            : presetIds.length > 0
              ? presetIdsToApiBands(presetIds)
              : null;
    const initialRoleBands: ApiRoleBand[] =
        fromJson && fromJson.length > 0
            ? fromJson
            : fromPresets && fromPresets.length > 0
              ? fromPresets
              : [];

    const useRoleBands = initialRoleBands.length > 0;

    return {
        search: filter.search ?? '',
        jobTitle: useRoleBands ? [] : parseFilterArray(filter['job_title.name']),
        skill: parseFilterArray(filter.skill),
        badge: parseFilterArray(filter.badge),
        availabilityType: parseFilterArray(filter.availability_type),
        hasUrls: parseFilterArray(filter.has_urls),
        isAvailable: filter.is_available ?? 'all',
        isRecommended: filter.is_recommended ?? 'all',
        yearsMin: useRoleBands ? '' : (filter.years_min ?? ''),
        yearsMax: useRoleBands ? '' : (filter.years_max ?? ''),
        ids,
        initialRoleBands,
    };
}

export function updateUrlWithFilters(filters: DeveloperFilters): void {
    const params = new URLSearchParams();
    if (filters.search?.trim())
        params.set('filter[search]', filters.search.trim());
    const hasRoleBands = (filters.roleBands?.length ?? 0) > 0;
    if (hasRoleBands) {
        params.set(
            'filter[role_bands]',
            encodeRoleBandsForFilter(filters.roleBands),
        );
    } else {
        const jobTitleVal = toFilterValue(filters.jobTitle);
        if (jobTitleVal) params.set('filter[job_title.name]', jobTitleVal);
    }
    const skillVal = toFilterValue(filters.skill);
    if (skillVal) params.set('filter[skill]', skillVal);
    const badgeVal = toFilterValue(filters.badge);
    if (badgeVal) params.set('filter[badge]', badgeVal);
    const availabilityTypeVal = toFilterValue(filters.availabilityType);
    if (availabilityTypeVal)
        params.set('filter[availability_type]', availabilityTypeVal);
    const hasUrlsVal = toFilterValue(filters.hasUrls);
    if (hasUrlsVal) params.set('filter[has_urls]', hasUrlsVal);
    if (filters.isAvailable && filters.isAvailable !== 'all')
        params.set('filter[is_available]', filters.isAvailable);
    if (filters.isRecommended && filters.isRecommended !== 'all')
        params.set('filter[is_recommended]', filters.isRecommended);
    if (!hasRoleBands) {
        if (filters.yearsMin) params.set('filter[years_min]', filters.yearsMin);
        if (filters.yearsMax) params.set('filter[years_max]', filters.yearsMax);
    }
    if (filters.ids?.length) {
        params.set('filter[ids]', filters.ids.join(','));
    }
    const q = params.toString();
    const newUrl = q
        ? `${window.location.pathname}?${q}`
        : window.location.pathname;
    window.history.replaceState({}, '', newUrl);
}

/**
 * Returns the absolute page URL with the given filters applied (for sharing or human browsing).
 */
export function getFilteredPageUrl(filters: DeveloperFilters): string {
    const withQuery = buildDevelopersApiUrl('', filters);
    const query = withQuery.startsWith('?') ? withQuery.slice(1) : '';
    return `${window.location.origin}${window.location.pathname}${query ? `?${query}` : ''}`;
}

/** Max per_page for the AI prompt URL so the AI gets all results in one response. */
const AI_PROMPT_PER_PAGE = 500;

/**
 * Returns the absolute API URL that returns filtered developers as JSON (for AI/tools that fetch data).
 * Uses per_page=500 so the AI receives up to 500 developers in a single response.
 */
export function getFilteredApiUrl(filters: DeveloperFilters): string {
    const path = '/api/developers';
    const withQuery = buildDevelopersApiUrl(path, filters);
    const base = `${window.location.origin}${withQuery}`;
    const separator = withQuery.includes('?') ? '&' : '?';
    return `${base}${separator}per_page=${AI_PROMPT_PER_PAGE}`;
}
