export type DeveloperBlogEntry = {
    id: number;
    title: string;
    slug: string;
    excerpt: string | null;
    status: string;
    status_label: string;
    published_at: string | null;
    created_at: string;
};

export type DeveloperBlogForm = {
    id?: number;
    title: string;
    slug: string;
    excerpt: string | null;
    content: string;
    featured_image: string | null;
    status: string;
    published_at: string | null;
};

export type PublicBlogEntry = {
    id: number;
    title: string;
    slug: string;
    excerpt: string | null;
    published_at: string | null;
    featured_image_url: string | null;
    developer: { name: string; slug: string } | null;
};

export type PublicBlogDetail = PublicBlogEntry & {
    content: string;
};
