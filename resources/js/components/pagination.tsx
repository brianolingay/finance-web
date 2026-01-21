import { Link } from '@inertiajs/react';

import { cn, decodeHtmlEntities } from '@/lib/utils';

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

export function Pagination({ links }: { links: PaginationLink[] }) {
    if (links.length <= 1) {
        return null;
    }

    return (
        <div className="flex flex-wrap items-center gap-2">
            {links.map((link, index) => (
                <Link
                    key={link.url ?? `page-${index}`}
                    href={link.url ?? '#'}
                    preserveScroll
                    className={cn(
                        'rounded-md border border-border px-3 py-1.5 text-sm transition-colors',
                        link.active
                            ? 'border-foreground text-foreground'
                            : 'text-muted-foreground hover:text-foreground',
                        link.url ? '' : 'pointer-events-none opacity-60',
                    )}
                >
                    {decodeHtmlEntities(link.label)}
                </Link>
            ))}
        </div>
    );
}
