/**
 * Parses salary values from API (may be number or locale-formatted string) for form inputs.
 */
export function parseSalaryForForm(
    value: string | number | null | undefined,
): number | null {
    if (value == null || value === '') {
        return null;
    }
    if (typeof value === 'number' && Number.isFinite(value)) {
        return Math.trunc(value);
    }
    const n = Number(String(value).replace(/,/g, '').trim());

    return Number.isFinite(n) ? Math.trunc(n) : null;
}

/**
 * Formats an integer for display with thousands separators (e.g. 1500000 → "1,500,000").
 */
export function formatSalaryDisplay(
    value: string | number | null | undefined,
): string {
    const n = parseSalaryForForm(value);
    if (n === null) {
        return '';
    }

    return n.toLocaleString('en-US');
}

/**
 * Parses a user-typed salary field: digits only, empty → null.
 */
export function parseSalaryDigitsInput(raw: string): number | null {
    const digits = raw.replace(/\D/g, '');
    if (digits === '') {
        return null;
    }
    const n = Number(digits);

    return Number.isFinite(n) ? Math.trunc(n) : null;
}

export const SALARY_CURRENCY_OPTIONS = [
    { value: 'USD', label: 'US Dollar (USD)' },
    { value: 'IQD', label: 'Iraqi Dinar (IQD)' },
    { value: 'SAR', label: 'Saudi Riyal (SAR)' },
    { value: 'AED', label: 'UAE Dirham (AED)' },
    { value: 'KWD', label: 'Kuwaiti Dinar (KWD)' },
    { value: 'BHD', label: 'Bahraini Dinar (BHD)' },
    { value: 'OMR', label: 'Omani Rial (OMR)' },
    { value: 'QAR', label: 'Qatari Riyal (QAR)' },
    { value: 'JOD', label: 'Jordanian Dinar (JOD)' },
    { value: 'EGP', label: 'Egyptian Pound (EGP)' },
    { value: 'LBP', label: 'Lebanese Pound (LBP)' },
    { value: 'IRR', label: 'Iranian Rial (IRR)' },
    { value: 'YER', label: 'Yemeni Rial (YER)' },
] as const;
