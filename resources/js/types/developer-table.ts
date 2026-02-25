export type DeveloperTableRow = {
    id: number;
    name: string;
    slug: string | null;
    email: string;
    job_title: string | null;
    years_of_experience: number;
    status: string;
    status_label: string;
    is_available: boolean;
    profile_url: string | null;
};
