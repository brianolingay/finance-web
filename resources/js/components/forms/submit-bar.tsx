import { type ReactNode } from 'react';

import { Button } from '@/components/ui/button';
import { cn } from '@/lib/utils';

interface SubmitBarProps {
    label?: string;
    value?: ReactNode;
    actionLabel: string;
    processing?: boolean;
    disabled?: boolean;
    className?: string;
}

export function SubmitBar({
    label,
    value,
    actionLabel,
    processing,
    disabled,
    className,
}: SubmitBarProps) {
    return (
        <div
            className={cn(
                'flex flex-col gap-3 rounded-lg border border-border/70 bg-muted/40 px-4 py-3 sm:flex-row sm:items-center sm:justify-between',
                className,
            )}
        >
            {label ? (
                <div className="flex items-center justify-between gap-4 text-sm">
                    <span className="text-muted-foreground">{label}</span>
                    <span className="text-base font-semibold text-foreground">
                        {value}
                    </span>
                </div>
            ) : null}
            <Button type="submit" disabled={disabled || processing}>
                {processing ? 'Working...' : actionLabel}
            </Button>
        </div>
    );
}
