export type Badge = {
    id: number;
    name: string;
    slug: string;
    description: string | null;
    icon: string | null;
    color: string | null;
    is_active: boolean;
    developers_count?: number;
};
