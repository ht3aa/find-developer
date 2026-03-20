import { DEVELOPER_FILTER_PRESETS } from '@/lib/developerFilterPresets';

/**
 * Dynamic “job title + experience band” rows (OR’d on the server).
 * Bands match DeveloperRepository::applyRoleBandsOr() / preset definitions.
 */

export type ExperienceLevel = 'junior' | 'mid' | 'senior' | '';

export type UiRoleBandRow = {
    clientId: string;
    jobTitle: string;
    level: ExperienceLevel;
};

export type ApiRoleBand = {
    job_title: string;
    years_min: number | null;
    years_max: number | null;
};

let rowIdSeq = 0;

export function createRoleBandRow(
    partial?: Partial<Pick<UiRoleBandRow, 'jobTitle' | 'level'>>,
): UiRoleBandRow {
    rowIdSeq += 1;
    return {
        clientId: `rb-${rowIdSeq}-${Date.now()}`,
        jobTitle: partial?.jobTitle ?? '',
        level: partial?.level ?? '',
    };
}

export function levelToApiBand(
    jobTitle: string,
    level: Exclude<ExperienceLevel, ''>,
): ApiRoleBand {
    switch (level) {
        case 'junior':
            return { job_title: jobTitle, years_min: null, years_max: 2 };
        case 'mid':
            return { job_title: jobTitle, years_min: 3, years_max: 5 };
        case 'senior':
            return { job_title: jobTitle, years_min: 6, years_max: null };
    }
}

export function rowsToApiBands(rows: UiRoleBandRow[]): ApiRoleBand[] {
    return rows
        .filter((r) => r.jobTitle.trim() !== '' && r.level !== '')
        .map((r) =>
            levelToApiBand(
                r.jobTitle.trim(),
                r.level as Exclude<ExperienceLevel, ''>,
            ),
        );
}

export function apiBandToRow(band: ApiRoleBand): UiRoleBandRow {
    let level: ExperienceLevel = '';
    if (band.years_min === null && band.years_max === 2) {
        level = 'junior';
    } else if (band.years_min === 3 && band.years_max === 5) {
        level = 'mid';
    } else if (band.years_min === 6 && band.years_max === null) {
        level = 'senior';
    }
    return createRoleBandRow({ jobTitle: band.job_title, level });
}

/**
 * Base64 (UTF-8 safe) so Spatie QueryBuilder does not split the value on commas.
 */
export function encodeRoleBandsForFilter(bands: ApiRoleBand[]): string {
    const json = JSON.stringify(bands);
    const utf8 = encodeURIComponent(json).replace(
        /%([0-9A-F]{2})/g,
        (_, hex: string) => String.fromCharCode(parseInt(hex, 16)),
    );
    return btoa(utf8);
}

export function decodeRoleBandsFromFilter(raw: string): ApiRoleBand[] | null {
    if (!raw?.trim()) {
        return null;
    }
    try {
        const json = decodeURIComponent(
            Array.from(atob(raw), (c) => `%${`00${c.charCodeAt(0).toString(16)}`.slice(-2)}`).join(
                '',
            ),
        );
        return parseRoleBandsJson(json);
    } catch {
        return parseRoleBandsJson(raw);
    }
}

export function parseRoleBandsJson(raw: string): ApiRoleBand[] | null {
    try {
        const parsed = JSON.parse(raw) as unknown;
        if (!Array.isArray(parsed)) {
            return null;
        }
        const out: ApiRoleBand[] = [];
        for (const item of parsed) {
            if (!item || typeof item !== 'object') {
                continue;
            }
            const o = item as Record<string, unknown>;
            const title =
                typeof o.job_title === 'string' ? o.job_title.trim() : '';
            if (!title) {
                continue;
            }
            const ymin = o.years_min;
            const ymax = o.years_max;
            out.push({
                job_title: title,
                years_min:
                    ymin === null || ymin === undefined || ymin === ''
                        ? null
                        : Number(ymin),
                years_max:
                    ymax === null || ymax === undefined || ymax === ''
                        ? null
                        : Number(ymax),
            });
        }
        return out.length > 0 ? out : null;
    } catch {
        return null;
    }
}

export function presetIdsToApiBands(ids: string[]): ApiRoleBand[] {
    const out: ApiRoleBand[] = [];
    for (const id of ids) {
        const p = DEVELOPER_FILTER_PRESETS.find((x) => x.id === id);
        const title = p?.jobTitles[0];
        if (!title) {
            continue;
        }
        const ymin =
            p.yearsMin === '' ? null : Number.parseInt(p.yearsMin, 10);
        const ymax =
            p.yearsMax === '' ? null : Number.parseInt(p.yearsMax, 10);
        out.push({
            job_title: title,
            years_min:
                ymin === null || Number.isNaN(ymin) ? null : ymin,
            years_max:
                ymax === null || Number.isNaN(ymax) ? null : ymax,
        });
    }
    return out;
}
