const BRAZILIAN_LOCALE = 'pt-BR';
const DATE_ONLY_PATTERN = /^(\d{4})-(\d{2})-(\d{2})$/;

function parseLocalDate(value: string | Date): Date {
    if (value instanceof Date) {
        return value;
    }

    const dateOnlyMatch = DATE_ONLY_PATTERN.exec(value);
    if (dateOnlyMatch) {
        const [, year, month, day] = dateOnlyMatch;
        return new Date(Number(year), Number(month) - 1, Number(day));
    }

    return new Date(value);
}

export function formatDate(value: string | Date): string {
    return parseLocalDate(value).toLocaleDateString(BRAZILIAN_LOCALE);
}

export function formatDateTime(value: string | Date): string {
    return parseLocalDate(value).toLocaleString(BRAZILIAN_LOCALE);
}
