import { Trash2 } from 'lucide-react';

import { MoneyInput } from '@/components/forms/money-input';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { formatCents } from '@/lib/money';

interface SaleItemRowProps {
    index: number;
    name: string;
    unitPriceCents: number;
    quantity: number;
    onUpdate: (patch: { quantity?: number; unitPriceCents?: number }) => void;
    onRemove: () => void;
    productId: number;
}

export function SaleItemRow({
    index,
    name,
    unitPriceCents,
    quantity,
    onUpdate,
    onRemove,
    productId,
}: SaleItemRowProps) {
    return (
        <div className="rounded-lg border border-border p-3">
            <div className="flex items-center justify-between">
                <div className="flex flex-col gap-1">
                    <span className="text-sm font-semibold">{name}</span>
                    <span className="text-xs text-muted-foreground">
                        {formatCents(unitPriceCents)} each
                    </span>
                </div>
                <Button
                    type="button"
                    variant="ghost"
                    size="icon"
                    onClick={onRemove}
                    aria-label="Remove item"
                >
                    <Trash2 className="h-4 w-4" />
                </Button>
            </div>
            <div className="mt-3 grid gap-3 sm:grid-cols-2">
                <div className="flex flex-col gap-2">
                    <Label>Qty</Label>
                    <MoneyInput
                        name={`items[${index}][quantity]`}
                        value={quantity}
                        min={1}
                        onChange={(event) =>
                            onUpdate({ quantity: Number(event.target.value) })
                        }
                    />
                </div>
                <div className="flex flex-col gap-2">
                    <Label>Unit price (cents)</Label>
                    <MoneyInput
                        name={`items[${index}][unit_price_cents]`}
                        value={unitPriceCents}
                        onChange={(event) =>
                            onUpdate({
                                unitPriceCents: Number(event.target.value),
                            })
                        }
                    />
                </div>
            </div>
            <input
                type="hidden"
                name={`items[${index}][product_id]`}
                value={productId}
            />
        </div>
    );
}
