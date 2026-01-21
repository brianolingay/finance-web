import { type ComponentProps } from 'react';

import { Input } from '@/components/ui/input';
import { cn } from '@/lib/utils';

interface MoneyInputProps extends Omit<ComponentProps<typeof Input>, 'type'> {
    hint?: string;
}

export function MoneyInput({
    className,
    hint,
    min = 0,
    step = 1,
    ...props
}: MoneyInputProps) {
    return (
        <div className="flex flex-col gap-1">
            <Input
                {...props}
                type="number"
                inputMode="numeric"
                min={min}
                step={step}
                className={cn('tabular-nums', className)}
            />
            {hint ? (
                <span className="text-xs text-muted-foreground">{hint}</span>
            ) : null}
        </div>
    );
}
