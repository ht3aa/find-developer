export type ActivityLogEntry = {
    id: number;
    log_name: string | null;
    description: string;
    subject_type: string | null;
    subject_id: number | null;
    causer_type: string | null;
    causer_id: number | null;
    causer_name: string | null;
    causer_email: string | null;
    event: string | null;
    created_at: string;
};

export type ActivityLogDetail = ActivityLogEntry & {
    subject_type_short: string | null;
    subject_label: string | null;
    causer_type_short: string | null;
    batch_uuid: string | null;
    properties: Record<string, unknown>;
    updated_at: string;
};
