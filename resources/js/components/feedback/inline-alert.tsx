import { type ReactNode } from 'react';

import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';

interface InlineAlertProps {
    title?: string;
    description: ReactNode;
    variant?: 'default' | 'destructive';
}

export function InlineAlert({
    title = 'Heads up',
    description,
    variant = 'default',
}: InlineAlertProps) {
    return (
        <Alert variant={variant}>
            <AlertTitle>{title}</AlertTitle>
            <AlertDescription>{description}</AlertDescription>
        </Alert>
    );
}
