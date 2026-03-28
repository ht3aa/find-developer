export type Badge = {
    id: number;
    name: string;
    slug: string;
    description: string | null;
    how_to_earn_description: string | null;
    icon: string | null;
    color: string | null;
    is_active: boolean;
    developers_count?: number;
};
