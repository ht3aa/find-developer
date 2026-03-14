export interface ChatUserSummary {
    id: number;
    name: string;
    email: string;
}

export interface MessageAttachment {
    id: number;
    file_name: string;
    file_url: string;
    file_type: string;
    file_size: number;
}

export interface ChatMessage {
    id: number;
    conversation_id: number;
    user: ChatUserSummary;
    body: string | null;
    attachments: MessageAttachment[];
    is_own: boolean;
    created_at: string;
}

export interface ChatLastMessage {
    id: number;
    body: string | null;
    user: { id: number; name: string };
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
