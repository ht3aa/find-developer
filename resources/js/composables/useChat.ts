import { nextTick, ref, watch, type Ref } from 'vue';

export function useChat(messagesContainer: Ref<HTMLElement | null>) {
    const isAtBottom = ref(true);

    function scrollToBottom(smooth = false) {
        nextTick(() => {
            if (!messagesContainer.value) return;
            messagesContainer.value.scrollTo({
                top: messagesContainer.value.scrollHeight,
                behavior: smooth ? 'smooth' : 'instant',
            });
        });
    }

    function onScroll() {
        if (!messagesContainer.value) return;
        const { scrollTop, scrollHeight, clientHeight } =
            messagesContainer.value;
        isAtBottom.value = scrollHeight - scrollTop - clientHeight < 50;
    }

    function watchMessages(messages: Ref<unknown[]>) {
        watch(
            () => messages.value.length,
            () => {
                if (isAtBottom.value) {
                    scrollToBottom(true);
                }
            },
        );
    }

    return {
        isAtBottom,
        scrollToBottom,
        onScroll,
        watchMessages,
    };
}

export function formatRelativeTime(dateString: string): string {
    const date = new Date(dateString);
    const now = new Date();
    const diffMs = now.getTime() - date.getTime();
    const diffMins = Math.floor(diffMs / 60000);
    const diffHours = Math.floor(diffMs / 3600000);
    const diffDays = Math.floor(diffMs / 86400000);

    if (diffMins < 1) return 'Just now';
    if (diffMins < 60) return `${diffMins}m ago`;
    if (diffHours < 24) return `${diffHours}h ago`;
    if (diffDays < 7) return `${diffDays}d ago`;

    return date.toLocaleDateString(undefined, {
        month: 'short',
        day: 'numeric',
    });
}

export function formatMessageTime(dateString: string): string {
    return new Date(dateString).toLocaleTimeString(undefined, {
        hour: '2-digit',
        minute: '2-digit',
    });
}

export function formatFileSize(bytes: number): string {
    if (bytes < 1024) return `${bytes} B`;
    if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(1)} KB`;
    return `${(bytes / (1024 * 1024)).toFixed(1)} MB`;
}

export function isImageType(fileType: string): boolean {
    return fileType.startsWith('image/');
}

export function getInitials(name: string): string {
    return name
        .split(' ')
        .map((n) => n[0])
        .join('')
        .toUpperCase()
        .slice(0, 2);
}
