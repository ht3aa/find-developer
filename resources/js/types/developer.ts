export type DeveloperBadge = {
    name: string;
    color?: string | null;
    icon?: string | null;
    icon_html?: string;
};

export type DeveloperJobTitle = {
    name: string;
};

export type DeveloperLocation = {
    label: string;
};

export type DeveloperSkill = {
    name: string;
};

export type DeveloperAvailabilityType = {
    value: string;
    label: string;
};

export type DeveloperRecommendation = {
    note: string | null;
    recommender_name: string;
    recommender_job_title?: string | null;
};

export type WorkExperienceRole = {
    job_title: string | null;
    start_date: string | null;
    end_date: string | null;
    is_current: boolean;
    duration: string | null;
    description: string | null;
};

export type WorkExperienceEntry = {
    company_name: string;
    roles: WorkExperienceRole[];
    total_duration?: string | null;
};

export type DeveloperProject = {
    title: string;
    description?: string | null;
    link?: string | null;
};

export type Developer = {
    id: number;
    user_id?: number | null;
    name: string;
    slug?: string | null;
    email: string;
    years_of_experience: number;
    phone?: string | null;
    expected_salary_from?: number | null;
    expected_salary_to?: number | null;
    currency?: string | null;
    is_available: boolean;
    bio?: string | null;
    portfolio_url?: string | null;
    github_url?: string | null;
    linkedin_url?: string | null;
    cv_path_url?: string | null;
    recommendations_received_count: number;
    recommended_by_us?: boolean;
    youtube_video_id?: string | null;
    badges: DeveloperBadge[];
    job_title: DeveloperJobTitle;
    location?: DeveloperLocation | null;
    skills: DeveloperSkill[];
    availability_type: DeveloperAvailabilityType[];
    profile_url?: string;
    recommend_url?: string | null;
    badges_page_url?: string;
    recommendations?: DeveloperRecommendation[];
    work_experience?: WorkExperienceEntry[];
    projects?: DeveloperProject[];
};
