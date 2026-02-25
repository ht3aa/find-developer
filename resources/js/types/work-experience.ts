export type WorkExperience = {
    id: number;
    company_name: string;
    job_title: string | null;
    parent_id: number | null;
    parent: { id: number; company_name: string; job_title: string | null } | null;
    description: string | null;
    start_date: string;
    end_date: string | null;
    is_current: boolean;
    show_company: boolean;
};

export type WorkExperienceForm = WorkExperience & {
    job_title_id?: number | null;
};
