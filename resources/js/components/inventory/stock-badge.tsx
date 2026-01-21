import { Badge } from '@/components/ui/badge';

interface StockBadgeProps {
    status: 'priced' | 'unpriced';
}

export function StockBadge({ status }: StockBadgeProps) {
    return (
        <Badge variant={status === 'priced' ? 'secondary' : 'outline'}>
            {status === 'priced' ? 'Priced' : 'Unpriced'}
        </Badge>
    );
}
