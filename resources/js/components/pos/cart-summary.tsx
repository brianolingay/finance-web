import { formatCents } from '@/lib/money';

interface CartSummaryProps {
    totalCents: number;
    itemCount: number;
}

export function CartSummary({ totalCents, itemCount }: CartSummaryProps) {
    return (
        <div className="flex items-center justify-between rounded-lg border border-border/70 bg-muted/40 px-4 py-3 text-sm">
            <div className="flex flex-col">
                <span className="text-muted-foreground">Items</span>
                <span className="text-base font-semibold text-foreground">
                    {itemCount}
                </span>
            </div>
            <div className="text-right">
                <span className="text-muted-foreground">Total</span>
                <div className="text-base font-semibold">
                    {formatCents(totalCents)}
                </div>
            </div>
        </div>
    );
}
