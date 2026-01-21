import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { formatCents } from '@/lib/money';
import { cn } from '@/lib/utils';

interface SummaryCardItem {
    label: string;
    amountCents: number;
    currency?: string;
    tone?: 'neutral' | 'positive' | 'negative';
}

interface SummaryCardsProps {
    items: SummaryCardItem[];
    className?: string;
}

const toneStyles: Record<NonNullable<SummaryCardItem['tone']>, string> = {
    neutral: 'text-foreground',
    positive: 'text-emerald-600 dark:text-emerald-400',
    negative: 'text-rose-600 dark:text-rose-400',
};

export function SummaryCards({ items, className }: SummaryCardsProps) {
    return (
        <section className={cn('grid gap-4 md:grid-cols-3', className)}>
            {items.map((item) => (
                <Card key={item.label}>
                    <CardHeader>
                        <CardTitle className="text-sm font-medium text-muted-foreground">
                            {item.label}
                        </CardTitle>
                    </CardHeader>
                    <CardContent
                        className={cn(
                            'text-2xl font-semibold',
                            item.tone ? toneStyles[item.tone] : null,
                        )}
                    >
                        {formatCents(item.amountCents, item.currency)}
                    </CardContent>
                </Card>
            ))}
        </section>
    );
}
