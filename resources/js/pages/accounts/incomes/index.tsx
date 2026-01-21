import { Form, Head } from '@inertiajs/react';
import { useState } from 'react';

import IncomeController from '@/actions/App/Http/Controllers/IncomeController';
import { SummaryCards } from '@/components/finance/summary-cards';
import { DateInput } from '@/components/forms/date-input';
import { FormField } from '@/components/forms/form-field';
import { MoneyInput } from '@/components/forms/money-input';
import { SelectField } from '@/components/forms/select-field';
import { SubmitBar } from '@/components/forms/submit-bar';
import { PageHeader } from '@/components/layout/page-header';
import { DataTable } from '@/components/tables/data-table';
import { EmptyState } from '@/components/tables/empty-state';
import { Pagination } from '@/components/tables/pagination';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
    Sheet,
    SheetContent,
    SheetHeader,
    SheetTitle,
    SheetTrigger,
} from '@/components/ui/sheet';
import AppLayout from '@/layouts/app-layout';

import { formatDate } from '@/lib/format';
import { formatCents } from '@/lib/money';

import { index as incomesIndex } from '@/routes/accounts/incomes';

import { type BreadcrumbItem } from '@/types';
import { type PaginatedResponse } from '@/types/api';
import { type AccountSummary, type Category, type Income } from '@/types/domain';

interface IncomesPageProps {
    account: AccountSummary;
    categories: Category[];
    incomes: PaginatedResponse<Income>;
    totals: {
        total_cents: Record<string, number>;
    };
}

export default function IncomesIndex({
    account,
    categories,
    incomes,
    totals,
}: IncomesPageProps) {
    const summaryItems = Object.entries(totals.total_cents).map(
        ([currency, amountCents]) => ({
            label: `Total incomes (${currency})`,
            amountCents,
            currency,
            tone: 'positive' as const,
        }),
    );

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Incomes',
            href: incomesIndex(account).url,
        },
    ];
    const [categoryId, setCategoryId] = useState('');

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`${account.name} Incomes`} />

            <div className="flex flex-col gap-6 p-6">
                <PageHeader
                    title="Incomes"
                    description="Track incoming money and keep your ledger consistent."
                    actions={
                        <Sheet>
                            <SheetTrigger asChild>
                                <Button>New income</Button>
                            </SheetTrigger>
                            <SheetContent className="flex flex-col gap-6">
                                <SheetHeader>
                                    <SheetTitle>Record income</SheetTitle>
                                </SheetHeader>
                                <Form
                                    {...IncomeController.store.form({
                                        account: account.id,
                                    })}
                                    className="flex flex-col gap-4"
                                    options={{
                                        preserveScroll: true,
                                        onSuccess: () => setCategoryId(''),
                                    }}
                                    resetOnSuccess
                                >
                                    {({ processing, errors }) => (
                                        <>
                                            <FormField
                                                label="Description"
                                                htmlFor="description"
                                                error={errors.description}
                                            >
                                                <Input
                                                    id="description"
                                                    name="description"
                                                    placeholder="Client payment"
                                                />
                                            </FormField>

                                            <SelectField
                                                name="category_id"
                                                label="Category"
                                                value={categoryId}
                                                onValueChange={setCategoryId}
                                                placeholder="Select category"
                                                options={categories.map((category) => ({
                                                    value: String(category.id),
                                                    label: category.name,
                                                }))}
                                                error={errors.category_id}
                                            />

                                            <div className="grid gap-3 sm:grid-cols-2">
                                                <FormField
                                                    label="Amount (cents)"
                                                    htmlFor="amount_cents"
                                                    error={errors.amount_cents}
                                                >
                                                    <MoneyInput
                                                        id="amount_cents"
                                                        name="amount_cents"
                                                        placeholder="0"
                                                        required
                                                    />
                                                </FormField>
                                                <FormField
                                                    label="Currency"
                                                    htmlFor="currency"
                                                    error={errors.currency}
                                                >
                                                    <Input
                                                        id="currency"
                                                        name="currency"
                                                        placeholder="USD"
                                                        defaultValue="USD"
                                                        required
                                                    />
                                                </FormField>
                                            </div>

                                            <FormField
                                                label="Occurred at"
                                                htmlFor="occurred_at"
                                                error={errors.occurred_at}
                                            >
                                                <DateInput
                                                    id="occurred_at"
                                                    name="occurred_at"
                                                />
                                            </FormField>

                                            <SubmitBar
                                                actionLabel="Save income"
                                                processing={processing}
                                            />
                                        </>
                                    )}
                                </Form>
                            </SheetContent>
                        </Sheet>
                    }
                />

                <SummaryCards
                    items={
                        summaryItems.length > 0
                            ? summaryItems
                            : [
                                  {
                                      label: 'Total incomes',
                                      amountCents: 0,
                                      tone: 'positive',
                                  },
                              ]
                    }
                    className="md:grid-cols-1"
                />

                <section className="flex flex-col gap-4">
                    <h2 className="text-lg font-semibold">Recent incomes</h2>
                    <DataTable
                        data={incomes.data}
                        getRowKey={(income) => income.id}
                        emptyState={
                            <EmptyState
                                title="No incomes yet"
                                description="Record the first incoming payment to get started."
                            />
                        }
                        columns={[
                            {
                                key: 'description',
                                label: 'Description',
                                render: (income) => (
                                    <span className="font-medium">
                                        {income.description ?? 'Unlabeled'}
                                    </span>
                                ),
                            },
                            {
                                key: 'amount',
                                label: 'Amount',
                                render: (income) =>
                                    formatCents(
                                        income.amount_cents,
                                        income.currency,
                                    ),
                            },
                            {
                                key: 'date',
                                label: 'Date',
                                render: (income) => (
                                    <span className="text-muted-foreground">
                                        {income.occurred_at
                                            ? formatDate(income.occurred_at)
                                            : '-'}
                                    </span>
                                ),
                            },
                        ]}
                    />

                    <Pagination links={incomes.links} />
                </section>
            </div>
        </AppLayout>
    );
}
