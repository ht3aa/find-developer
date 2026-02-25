export type UserTableRow = {
    id: number;
    name: string;
    email: string;
    user_type: string;
    user_type_label: string;
    can_access_admin_panel: boolean;
    roles: { id: number; name: string }[];
};

export type UserForm = {
    id?: number;
    name: string;
    email: string;
    user_type: string;
    can_access_admin_panel: boolean;
    linkedin_url?: string | null;
    role_ids: number[];
};

export const userTypeOptions: { value: string; label: string }[] = [
    { value: 'developer', label: 'Developer' },
    { value: 'admin', label: 'Admin' },
    { value: 'hr', label: 'HR' },
];
