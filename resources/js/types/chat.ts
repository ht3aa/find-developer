export interface ChatUserSummary {
    id: number;
    name: string;
    email: string;
    user_type_label?: string;
    developer_slug?: string | null;
}

export interface MessageAttachment {
    id: number;
    file_name: string;
    file_url: string;
    file_type: string;
    file_size: number;
}

export interface MessageReplyTo {
    id: number;
    body: string | null;
    user: { name: string; developer_slug?: string | null };
}

export interface ChatMessage {
    id: number;
    conversation_id: number;
    user: ChatUserSummary;
    body: string | null;
    attachments: MessageAttachment[];
    is_own: boolean;
    created_at: string;
    reply_to?: MessageReplyTo;
}

export interface ChatLastMessage {
    id: number;
    body: string | null;
    user: { id: number; name: string; user_type_label?: string };
    is_own: boolean;
    created_at: string;
}

export interface ChatConversation {
    id: number;
    participant: ChatUserSummary | null;
    last_message: ChatLastMessage | null;
    unread_count: number;
    updated_at: string;
}
