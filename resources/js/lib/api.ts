export type DeveloperFilters = {
    search?: string;
    jobTitle?: string[];
    skill?: string[];
    yearsMin?: string;
    yearsMax?: string;
    sort?: string;
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
    if (filters.yearsMin) params.set('filter[years_min]', filters.yearsMin);
    if (filters.yearsMax) params.set('filter[years_max]', filters.yearsMax);
    if (filters.sort && filters.sort !== 'name') params.set('sort', filters.sort);
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
        yearsMin: filter.years_min ?? '',
        yearsMax: filter.years_max ?? '',
        sort: params.get('sort') ?? 'name',
    };
}

export function updateUrlWithFilters(filters: DeveloperFilters): void {
    const params = new URLSearchParams();
    if (filters.search?.trim()) params.set('filter[search]', filters.search.trim());
    const jobTitleVal = toFilterValue(filters.jobTitle);
    if (jobTitleVal) params.set('filter[job_title.name]', jobTitleVal);
    const skillVal = toFilterValue(filters.skill);
    if (skillVal) params.set('filter[skill]', skillVal);
    if (filters.yearsMin) params.set('filter[years_min]', filters.yearsMin);
    if (filters.yearsMax) params.set('filter[years_max]', filters.yearsMax);
    if (filters.sort && filters.sort !== 'name') params.set('sort', filters.sort);
    const q = params.toString();
    const newUrl = q ? `${window.location.pathname}?${q}` : window.location.pathname;
    window.history.replaceState({}, '', newUrl);
}
