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
        credit_cents: Record<string, number>;
        debit_cents: Record<string, number>;
        net_cents: Record<string, number>;
    };
    transactions: PaginatedResponse<Transaction>;
}

export default function AccountDashboard({
    account,
    totals,
    transactions,
}: AccountDashboardProps) {
    const currencies = Array.from(
        new Set([
            ...Object.keys(totals.credit_cents),
            ...Object.keys(totals.debit_cents),
            ...Object.keys(totals.net_cents),
        ]),
    );

    const summaryItems =
        currencies.length > 0
            ? currencies.flatMap((currency) => {
                  const creditTotal = totals.credit_cents[currency] ?? 0;
                  const debitTotal = totals.debit_cents[currency] ?? 0;
                  const netTotal = totals.net_cents[currency] ?? 0;

                  return [
                      {
                          label: `Total credits (${currency})`,
                          amountCents: creditTotal,
                          currency,
                          tone: 'positive' as const,
                      },
                      {
                          label: `Total debits (${currency})`,
                          amountCents: debitTotal,
                          currency,
                          tone: 'negative' as const,
                      },
                      {
                          label: `Net total (${currency})`,
                          amountCents: netTotal,
                          currency,
                          tone:
                              netTotal >= 0
                                  ? ('positive' as const)
                                  : ('negative' as const),
                      },
                  ];
              })
            : [
                  {
                      label: 'Total credits',
                      amountCents: 0,
                      tone: 'positive' as const,
                  },
                  {
                      label: 'Total debits',
                      amountCents: 0,
                      tone: 'negative' as const,
                  },
                  {
                      label: 'Net total',
                      amountCents: 0,
                      tone: 'positive' as const,
                  },
              ];

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
                    items={summaryItems}
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
