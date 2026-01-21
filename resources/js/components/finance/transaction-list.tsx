import { DataTable } from '@/components/tables/data-table';
import { EmptyState } from '@/components/tables/empty-state';
import { Badge } from '@/components/ui/badge';
import { formatDateTime } from '@/lib/format';
import { formatCents } from '@/lib/money';

interface TransactionListItem {
    id: number;
    direction: 'credit' | 'debit';
    amount_cents: number;
    currency: string;
    occurred_at: string;
    source_type: string;
}

interface TransactionListProps {
    transactions: TransactionListItem[];
}

export function TransactionList({ transactions }: TransactionListProps) {
    return (
        <DataTable
            data={transactions}
            getRowKey={(transaction) => transaction.id}
            emptyState={
                <EmptyState
                    title="No transactions yet"
                    description="Once money moves through this account, entries appear here."
                />
            }
            columns={[
                {
                    key: 'direction',
                    label: 'Direction',
                    render: (transaction) => (
                        <Badge
                            variant={
                                transaction.direction === 'credit'
                                    ? 'secondary'
                                    : 'outline'
                            }
                        >
                            {transaction.direction}
                        </Badge>
                    ),
                },
                {
                    key: 'amount',
                    label: 'Amount',
                    render: (transaction) => (
                        <span className="font-medium">
                            {formatCents(
                                transaction.amount_cents,
                                transaction.currency,
                            )}
                        </span>
                    ),
                },
                {
                    key: 'source',
                    label: 'Source',
                    render: (transaction) => (
                        <span className="text-muted-foreground">
                            {transaction.source_type.split('\\').pop()}
                        </span>
                    ),
                },
                {
                    key: 'occurred',
                    label: 'Occurred',
                    render: (transaction) => (
                        <span className="text-muted-foreground">
                            {formatDateTime(transaction.occurred_at)}
                        </span>
                    ),
                },
            ]}
        />
    );
}
