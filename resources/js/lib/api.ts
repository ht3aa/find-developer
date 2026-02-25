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
};

function toFilterValue(val: string | string[] | null | undefined): string {
    if (val == null) return '';
    return Array.isArray(val) ? val.filter(Boolean).join(',') : String(val);
}

function parseFilterArray(val: string | null | undefined): string[] {
    if (val == null || val === '') return [];
    return val.includes(',') ? val.split(',').map((s) => s.trim()).filter(Boolean) : [val];
}

export function buildDevelopersApiUrl(base: string, filters: DeveloperFilters): string {
    const params = new URLSearchParams();
    if (filters.search?.trim()) params.set('filter[search]', filters.search.trim());
    const jobTitleVal = toFilterValue(filters.jobTitle);
    if (jobTitleVal) params.set('filter[job_title.name]', jobTitleVal);
    const skillVal = toFilterValue(filters.skill);
    if (skillVal) params.set('filter[skill]', skillVal);
    const badgeVal = toFilterValue(filters.badge);
    if (badgeVal) params.set('filter[badge]', badgeVal);
    const availabilityTypeVal = toFilterValue(filters.availabilityType);
    if (availabilityTypeVal) params.set('filter[availability_type]', availabilityTypeVal);
    const hasUrlsVal = toFilterValue(filters.hasUrls);
    if (hasUrlsVal) params.set('filter[has_urls]', hasUrlsVal);
    if (filters.isAvailable && filters.isAvailable !== 'all') params.set('filter[is_available]', filters.isAvailable);
    if (filters.isRecommended && filters.isRecommended !== 'all') params.set('filter[is_recommended]', filters.isRecommended);
    if (filters.yearsMin) params.set('filter[years_min]', filters.yearsMin);
    if (filters.yearsMax) params.set('filter[years_max]', filters.yearsMax);
    const q = params.toString();
    return q ? `${base}?${q}` : base;
}

export function parseFiltersFromUrl(): DeveloperFilters {
    const params = new URLSearchParams(window.location.search);
    const filter: Record<string, string> = {};
    for (const [k, v] of params.entries()) {
        const m = k.match(/^filter\[(.+)\]$/);
        if (m) filter[m[1]] = v;
    }
    return {
        search: filter.search ?? '',
        jobTitle: parseFilterArray(filter['job_title.name']),
        skill: parseFilterArray(filter.skill),
        badge: parseFilterArray(filter.badge),
        availabilityType: parseFilterArray(filter.availability_type),
        hasUrls: parseFilterArray(filter.has_urls),
        isAvailable: filter.is_available ?? 'all',
        isRecommended: filter.is_recommended ?? 'all',
        yearsMin: filter.years_min ?? '',
        yearsMax: filter.years_max ?? '',
    };
}

export function updateUrlWithFilters(filters: DeveloperFilters): void {
    const params = new URLSearchParams();
    if (filters.search?.trim()) params.set('filter[search]', filters.search.trim());
    const jobTitleVal = toFilterValue(filters.jobTitle);
    if (jobTitleVal) params.set('filter[job_title.name]', jobTitleVal);
    const skillVal = toFilterValue(filters.skill);
    if (skillVal) params.set('filter[skill]', skillVal);
    const badgeVal = toFilterValue(filters.badge);
    if (badgeVal) params.set('filter[badge]', badgeVal);
    const availabilityTypeVal = toFilterValue(filters.availabilityType);
    if (availabilityTypeVal) params.set('filter[availability_type]', availabilityTypeVal);
    const hasUrlsVal = toFilterValue(filters.hasUrls);
    if (hasUrlsVal) params.set('filter[has_urls]', hasUrlsVal);
    if (filters.isAvailable && filters.isAvailable !== 'all') params.set('filter[is_available]', filters.isAvailable);
    if (filters.isRecommended && filters.isRecommended !== 'all') params.set('filter[is_recommended]', filters.isRecommended);
    if (filters.yearsMin) params.set('filter[years_min]', filters.yearsMin);
    if (filters.yearsMax) params.set('filter[years_max]', filters.yearsMax);
    const q = params.toString();
    const newUrl = q ? `${window.location.pathname}?${q}` : window.location.pathname;
    window.history.replaceState({}, '', newUrl);
}
