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
            {links.map((link, index) => {
                const className = cn(
                    'rounded-md border border-border px-3 py-1.5 text-sm transition-colors',
                    link.active
                        ? 'border-foreground text-foreground'
                        : 'text-muted-foreground hover:text-foreground',
                    link.url ? null : 'opacity-60',
                );

                const key = link.url ?? `page-${index}`;

                if (! link.url) {
                    return (
                        <span
                            key={key}
                            aria-disabled="true"
                            className={className}
                        >
                            {decodeHtmlEntities(link.label)}
                        </span>
                    );
                }

                return (
                    <Link
                        key={key}
                        href={link.url}
                        preserveScroll
                        className={className}
                    >
                        {decodeHtmlEntities(link.label)}
                    </Link>
                );
            })}
        </div>
    );
}
