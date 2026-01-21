export function formatCents(amountCents: number, currency = 'USD') {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency,
        maximumFractionDigits: 2,
    }).format(amountCents / 100);
}

export function parseCents(value: string): number {
    const normalized = value.replace(/[^0-9-]/g, '');
    const parsed = Number.parseInt(normalized, 10);

    return Number.isNaN(parsed) ? 0 : parsed;
}
