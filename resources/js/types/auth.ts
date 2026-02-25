export type User = {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
    [key: string]: unknown;
};

export type Auth = {
    user: User;
    /** Permission names the user has (from Spatie, via roles/direct). */
    permissions?: string[];
};

export type TwoFactorConfigContent = {
    title: string;
    description: string;
    buttonText: string;
};
