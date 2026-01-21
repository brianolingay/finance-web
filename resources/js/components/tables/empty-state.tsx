import { type ReactNode } from 'react';

import { PlaceholderPattern } from '@/components/ui/placeholder-pattern';
import { cn } from '@/lib/utils';

interface EmptyStateProps {
    title: string;
    description?: string;
    icon?: ReactNode;
    action?: ReactNode;
    className?: string;
}

export function EmptyState({
    title,
    description,
    icon,
    action,
    className,
}: EmptyStateProps) {
    return (
        <div
            className={cn(
                'relative flex flex-col items-center justify-center gap-2 overflow-hidden rounded-lg border border-dashed border-border px-6 py-10 text-center',
                className,
            )}
        >
            <PlaceholderPattern className="pointer-events-none absolute inset-0 h-full w-full text-muted/50" />
            <div className="relative flex flex-col items-center gap-2">
                {icon ? (
                    <div className="flex h-10 w-10 items-center justify-center rounded-full border border-border bg-background">
                        {icon}
                    </div>
                ) : null}
                <h3 className="text-sm font-semibold text-foreground">
                    {title}
                </h3>
                {description ? (
                    <p className="max-w-xs text-xs text-muted-foreground">
                        {description}
                    </p>
                ) : null}
                {action}
            </div>
        </div>
    );
}
