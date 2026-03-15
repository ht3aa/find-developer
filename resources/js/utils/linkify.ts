const ONETIMESECRET_DISPLAY = 'OneTime Secret';

/**
 * Transform OneTimeSecret links to display "OneTime Secret" instead of the raw URL.
 */
export function transformOneTimeSecretLinks(html: string | null): string {
    if (!html) return '';
    return html.replace(
        /<a\s+([^>]*href="[^"]*onetimesecret\.com[^"]*"[^>]*)>([^<]*)<\/a>/gi,
        (_, attrs) => `<a ${attrs}>${ONETIMESECRET_DISPLAY}</a>`,
    );
}
