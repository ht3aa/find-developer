export type DeveloperProject = {
    id: number;
    title: string;
    description: string | null;
    link: string | null;
    show_project: boolean;
    developer?: { id: number; name: string; slug: string } | null;
};
