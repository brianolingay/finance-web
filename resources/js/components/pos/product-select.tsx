import { Button } from '@/components/ui/button';
import { formatCents } from '@/lib/money';

interface ProductOption {
    id: number;
    name: string;
    sku: string | null;
    unit_price_cents: number | null;
}

interface ProductSelectProps {
    products: ProductOption[];
    onSelect: (product: ProductOption) => void;
}

export function ProductSelect({ products, onSelect }: ProductSelectProps) {
    return (
        <div className="grid gap-3 sm:grid-cols-2">
            {products.map((product) => (
                <Button
                    key={product.id}
                    variant="outline"
                    className="flex h-auto flex-col items-start gap-2 rounded-xl p-4 text-left"
                    type="button"
                    onClick={() => onSelect(product)}
                >
                    <span className="text-sm font-semibold">
                        {product.name}
                    </span>
                    <span className="text-xs text-muted-foreground">
                        {product.sku ?? 'No SKU'}
                    </span>
                    <span className="text-base font-semibold">
                        {formatCents(product.unit_price_cents ?? 0)}
                    </span>
                </Button>
            ))}
        </div>
    );
}
