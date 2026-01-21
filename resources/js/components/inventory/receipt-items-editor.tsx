import { Trash2 } from 'lucide-react';

import { MoneyInput } from '@/components/forms/money-input';
import { EmptyState } from '@/components/tables/empty-state';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { formatCents } from '@/lib/money';

interface ReceiptProduct {
    id: number;
    name: string;
    sku: string | null;
    unit_cost_cents: number | null;
}

interface ReceiptItem {
    product_id: number;
    name: string;
    unit_cost_cents: number;
    quantity: number;
}

interface ReceiptItemsEditorProps {
    products: ReceiptProduct[];
    items: ReceiptItem[];
    onAdd: (product: ReceiptProduct) => void;
    onUpdate: (index: number, patch: Partial<ReceiptItem>) => void;
    onRemove: (index: number) => void;
    error?: string;
}

export function ReceiptItemsEditor({
    products,
    items,
    onAdd,
    onUpdate,
    onRemove,
    error,
}: ReceiptItemsEditorProps) {
    return (
        <div className="flex flex-col gap-4">
            <div className="grid gap-3 sm:grid-cols-2">
                {products.map((product) => (
                    <Button
                        key={product.id}
                        variant="outline"
                        type="button"
                        className="flex h-auto flex-col items-start gap-1 rounded-xl p-3 text-left"
                        onClick={() => onAdd(product)}
                    >
                        <span className="text-sm font-semibold">
                            {product.name}
                        </span>
                        <span className="text-xs text-muted-foreground">
                            {product.sku ?? 'No SKU'}
                        </span>
                        <span className="text-sm font-medium">
                            {formatCents(product.unit_cost_cents ?? 0)}
                        </span>
                    </Button>
                ))}
            </div>

            {items.length === 0 ? (
                <EmptyState
                    title="No items added"
                    description="Select products to receive into inventory."
                />
            ) : (
                <div className="space-y-3">
                    {items.map((item, index) => (
                        <div
                            key={`${item.product_id}-${index}`}
                            className="rounded-lg border border-border p-3"
                        >
                            <div className="flex items-center justify-between">
                                <div className="flex flex-col gap-1">
                                    <span className="text-sm font-semibold">
                                        {item.name}
                                    </span>
                                    <span className="text-xs text-muted-foreground">
                                        {formatCents(item.unit_cost_cents)} each
                                    </span>
                                </div>
                                <Button
                                    type="button"
                                    variant="ghost"
                                    size="icon"
                                    onClick={() => onRemove(index)}
                                >
                                    <Trash2 className="h-4 w-4" />
                                </Button>
                            </div>
                            <div className="mt-3 grid gap-3 sm:grid-cols-2">
                                <div className="flex flex-col gap-2">
                                    <Label>Qty</Label>
                                    <MoneyInput
                                        name={`items[${index}][quantity]`}
                                        value={item.quantity}
                                        min={1}
                                        onChange={(event) =>
                                            onUpdate(index, {
                                                quantity: Number(event.target.value),
                                            })
                                        }
                                    />
                                </div>
                                <div className="flex flex-col gap-2">
                                    <Label>Unit cost (cents)</Label>
                                    <MoneyInput
                                        name={`items[${index}][unit_cost_cents]`}
                                        value={item.unit_cost_cents}
                                        onChange={(event) =>
                                            onUpdate(index, {
                                                unit_cost_cents: Number(
                                                    event.target.value,
                                                ),
                                            })
                                        }
                                    />
                                </div>
                            </div>
                            <input
                                type="hidden"
                                name={`items[${index}][product_id]`}
                                value={item.product_id}
                            />
                        </div>
                    ))}
                </div>
            )}

            {error ? (
                <p className="text-sm text-red-600 dark:text-red-400">
                    {error}
                </p>
            ) : null}
        </div>
    );
}
