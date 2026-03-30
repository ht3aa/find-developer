/**
 * Laravel JsonResource collections nested in `resolve()` may be plain arrays or
 * `{ data: [...] }`. Normalize to a real array for Vue.
 */
export function asResourceArray<T>(value: unknown): T[] {
    if (value == null) {
        return [];
    }
    if (Array.isArray(value)) {
        return value as T[];
    }
    if (typeof value === 'object' && value !== null && 'data' in value) {
        const inner = (value as { data: unknown }).data;
        if (Array.isArray(inner)) {
            return inner as T[];
        }
    }
    return [];
}
