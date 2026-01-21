import { type ReactNode } from 'react';

import { cn } from '@/lib/utils';

interface PageHeaderProps {
    title: string;
    description?: string;
    eyebrow?: string;
    actions?: ReactNode;
    className?: string;
}

export function PageHeader({
    title,
    description,
    eyebrow,
    actions,
    className,
}: PageHeaderProps) {
    return (
        <header
            className={cn(
                'flex flex-col gap-3 border-b border-border/70 pb-4',
                className,
            )}
        >
            {eyebrow ? (
                <span className="text-xs font-semibold uppercase tracking-[0.18em] text-muted-foreground">
                    {eyebrow}
                </span>
            ) : null}
            <div className="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                <div className="space-y-1">
                    <h1 className="text-2xl font-semibold tracking-tight">
                        {title}
                    </h1>
                    {description ? (
                        <p className="text-sm text-muted-foreground">
                            {description}
                        </p>
                    ) : null}
                </div>
                {actions ? (
                    <div className="flex items-center gap-2">{actions}</div>
                ) : null}
            </div>
        </header>
    );
}
