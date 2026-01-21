import { InertiaLinkProps } from '@inertiajs/react';
import { type ClassValue, clsx } from 'clsx';
import { twMerge } from 'tailwind-merge';

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs));
}

export function toUrl(url: NonNullable<InertiaLinkProps['href']>): string {
    return typeof url === 'string' ? url : url.url;
}

export function decodeHtmlEntities(value: string): string {
    if (typeof window === 'undefined' || typeof DOMParser === 'undefined') {
        return value;
    }

    const parsed = new DOMParser().parseFromString(value, 'text/html');

    return parsed.body.textContent ?? '';
}
