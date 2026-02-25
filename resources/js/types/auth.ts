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

/** Abilities shared from Laravel Gate (e.g. viewAny(Developer::class) -> viewAnyDeveloper). */
export type AuthCan = {
    viewAnyDeveloper?: boolean;
    viewDeveloperProfile?: boolean;
    viewAnyDeveloperCompany?: boolean;
    viewAnyDeveloperProject?: boolean;
    viewAnyBadge?: boolean;
    viewAnyUser?: boolean;
    viewAnyRole?: boolean;
    viewActivityLog?: boolean;
    // Table row actions
    updateDeveloper?: boolean;
    viewDeveloper?: boolean;
    updateUser?: boolean;
    deleteUser?: boolean;
    updateRole?: boolean;
    deleteRole?: boolean;
    updateBadge?: boolean;
    deleteBadge?: boolean;
    updateDeveloperCompany?: boolean;
    deleteDeveloperCompany?: boolean;
    updateDeveloperProject?: boolean;
    deleteDeveloperProject?: boolean;
};

export type Auth = {
    user: User;
    /** Permission names the user has (from Spatie, via roles/direct). */
    permissions?: string[];
    /** Laravel Gate/policy results for sidebar and UI. */
    can?: AuthCan;
};

export type TwoFactorConfigContent = {
    title: string;
    description: string;
    buttonText: string;
};
