export type DeveloperFilterPresetGroup = 'frontend' | 'backend' | 'fullstack';

export type DeveloperFilterPreset = {
    id: string;
    label: string;
    group: DeveloperFilterPresetGroup;
    /** Values must match `/api/job-titles` labels (used as `filter[job_title.name]`). */
    jobTitles: string[];
    yearsMin: string;
    yearsMax: string;
};

/**
 * Experience bands aligned with quick filters: junior ≤2y, mid 3–5y, senior ≥6y.
 */
export const DEVELOPER_FILTER_PRESETS: DeveloperFilterPreset[] = [
    {
        id: 'fe-junior',
        label: 'Junior',
        group: 'frontend',
        jobTitles: ['Frontend Developer'],
        yearsMin: '',
        yearsMax: '2',
    },
    {
        id: 'fe-mid',
        label: 'Mid',
        group: 'frontend',
        jobTitles: ['Frontend Developer'],
        yearsMin: '3',
        yearsMax: '5',
    },
    {
        id: 'fe-senior',
        label: 'Senior',
        group: 'frontend',
        jobTitles: ['Frontend Developer'],
        yearsMin: '6',
        yearsMax: '',
    },
    {
        id: 'be-junior',
        label: 'Junior',
        group: 'backend',
        jobTitles: ['Backend Developer'],
        yearsMin: '',
        yearsMax: '2',
    },
    {
        id: 'be-mid',
        label: 'Mid',
        group: 'backend',
        jobTitles: ['Backend Developer'],
        yearsMin: '3',
        yearsMax: '5',
    },
    {
        id: 'be-senior',
        label: 'Senior',
        group: 'backend',
        jobTitles: ['Backend Developer'],
        yearsMin: '6',
        yearsMax: '',
    },
    {
        id: 'fs-junior',
        label: 'Junior',
        group: 'fullstack',
        jobTitles: ['Full Stack Developer'],
        yearsMin: '',
        yearsMax: '2',
    },
    {
        id: 'fs-mid',
        label: 'Mid',
        group: 'fullstack',
        jobTitles: ['Full Stack Developer'],
        yearsMin: '3',
        yearsMax: '5',
    },
    {
        id: 'fs-senior',
        label: 'Senior',
        group: 'fullstack',
        jobTitles: ['Full Stack Developer'],
        yearsMin: '6',
        yearsMax: '',
    },
];

export const DEVELOPER_FILTER_PRESET_GROUPS: {
    key: DeveloperFilterPresetGroup;
    title: string;
}[] = [
    { key: 'frontend', title: 'Front-end' },
    { key: 'backend', title: 'Back-end' },
    { key: 'fullstack', title: 'Full-stack' },
];
