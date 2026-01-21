export function formatCents(amountCents: number, currency = 'USD') {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency,
        maximumFractionDigits: 2,
    }).format(amountCents / 100);
}

export function formatDateTime(value: string) {
    return new Date(value).toLocaleString();
}

export function formatDate(value: string) {
    return new Date(value).toLocaleDateString();
}
