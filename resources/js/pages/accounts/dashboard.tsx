import { Head } from '@inertiajs/react';

import { SummaryCards } from '@/components/finance/summary-cards';
import { TransactionList } from '@/components/finance/transaction-list';
import { PageHeader } from '@/components/layout/page-header';
import { Pagination } from '@/components/tables/pagination';
import AppLayout from '@/layouts/app-layout';
import { dashboard as accountDashboard } from '@/routes/accounts';
import { type BreadcrumbItem } from '@/types';
import { type PaginatedResponse } from '@/types/api';
import { type AccountSummary, type Transaction } from '@/types/domain';

interface AccountDashboardProps {
    account: AccountSummary;
    totals: {
        credit_cents: number;
        debit_cents: number;
        net_cents: number;
    };
    transactions: PaginatedResponse<Transaction>;
}

export default function AccountDashboard({
    account,
    totals,
    transactions,
}: AccountDashboardProps) {
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Dashboard',
            href: accountDashboard(account).url,
        },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`${account.name} Dashboard`} />

            <div className="flex flex-col gap-6 p-6">
                <PageHeader
                    title={account.name}
                    eyebrow={account.type ?? 'Account'}
                    description="Snapshot of cash flow and recent ledger activity."
                />

                <SummaryCards
                    items={[
                        {
                            label: 'Total credits',
                            amountCents: totals.credit_cents,
                            tone: 'positive',
                        },
                        {
                            label: 'Total debits',
                            amountCents: totals.debit_cents,
                            tone: 'negative',
                        },
                        {
                            label: 'Net total',
                            amountCents: totals.net_cents,
                            tone: totals.net_cents >= 0 ? 'positive' : 'negative',
                        },
                    ]}
                />

                <section className="flex flex-col gap-4">
                    <div className="flex items-center justify-between">
                        <h2 className="text-lg font-semibold">
                            Recent transactions
                        </h2>
                        <span className="text-sm text-muted-foreground">
                            {transactions.data.length} shown
                        </span>
                    </div>

                    <TransactionList transactions={transactions.data} />

                    <Pagination links={transactions.links} />
                </section>
            </div>
        </AppLayout>
    );
}
