import { cn } from '@/lib/utils';

interface ColumnHeaderProps {
    label: string;
    className?: string;
}

export function ColumnHeader({ label, className }: ColumnHeaderProps) {
    return (
        <span
            className={cn(
                'text-xs font-semibold uppercase tracking-wide text-muted-foreground',
                className,
            )}
        >
            {label}
        </span>
    );
}
