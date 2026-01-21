import { type ReactNode } from 'react';

import InputError from '@/components/input-error';
import { Label } from '@/components/ui/label';
import { cn } from '@/lib/utils';

interface FormFieldProps {
    label: string;
    htmlFor?: string;
    description?: string;
    error?: string;
    className?: string;
    children: ReactNode;
}

export function FormField({
    label,
    htmlFor,
    description,
    error,
    className,
    children,
}: FormFieldProps) {
    return (
        <div className={cn('flex flex-col gap-2', className)}>
            <Label htmlFor={htmlFor}>{label}</Label>
            {children}
            {description ? (
                <p className="text-xs text-muted-foreground">
                    {description}
                </p>
            ) : null}
            <InputError message={error} />
        </div>
    );
}
