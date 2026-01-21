export { formatCents } from './format';

export function parseCents(value: string): number {
    const normalized = value.replace(/[^0-9-]/g, '');
    const parsed = Number.parseInt(normalized, 10);

    return Number.isNaN(parsed) ? 0 : parsed;
}
