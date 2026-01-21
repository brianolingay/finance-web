import { Form, Head } from '@inertiajs/react';
import { useState } from 'react';

import ExpenseController from '@/actions/App/Http/Controllers/ExpenseController';
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

import { index as expensesIndex } from '@/routes/accounts/expenses';

import { type BreadcrumbItem } from '@/types';
import { type PaginatedResponse } from '@/types/api';
import { type AccountSummary, type Category, type Expense } from '@/types/domain';

interface ExpensesPageProps {
    account: AccountSummary;
    categories: Category[];
    expenses: PaginatedResponse<Expense>;
    totals: {
        total_cents: Record<string, number>;
    };
}

export default function ExpensesIndex({
    account,
    categories,
    expenses,
    totals,
}: ExpensesPageProps) {
    const summaryItems = Object.entries(totals.total_cents).map(
        ([currency, amountCents]) => ({
            label: `Total expenses (${currency})`,
            amountCents,
            currency,
            tone: 'negative' as const,
        }),
    );

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Expenses',
            href: expensesIndex(account).url,
        },
    ];
    const [categoryId, setCategoryId] = useState('');

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`${account.name} Expenses`} />

            <div className="flex flex-col gap-6 p-6">
                <PageHeader
                    title="Expenses"
                    description="Track outgoing money and keep your ledger consistent."
                    actions={
                        <Sheet>
                            <SheetTrigger asChild>
                                <Button>New expense</Button>
                            </SheetTrigger>
                            <SheetContent className="flex flex-col gap-6">
                                <SheetHeader>
                                    <SheetTitle>Record expense</SheetTitle>
                                </SheetHeader>
                                <Form
                                    {...ExpenseController.store.form({
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
                                                    placeholder="Office supplies"
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
                                                actionLabel="Save expense"
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
                                      label: 'Total expenses',
                                      amountCents: 0,
                                      tone: 'negative',
                                  },
                              ]
                    }
                    className="md:grid-cols-1"
                />

                <section className="flex flex-col gap-4">
                    <h2 className="text-lg font-semibold">Recent expenses</h2>
                    <DataTable
                        data={expenses.data}
                        getRowKey={(expense) => expense.id}
                        emptyState={
                            <EmptyState
                                title="No expenses yet"
                                description="Record your first expense to keep the ledger moving."
                            />
                        }
                        columns={[
                            {
                                key: 'description',
                                label: 'Description',
                                render: (expense) => (
                                    <span className="font-medium">
                                        {expense.description ?? 'Unlabeled'}
                                    </span>
                                ),
                            },
                            {
                                key: 'amount',
                                label: 'Amount',
                                render: (expense) =>
                                    formatCents(
                                        expense.amount_cents,
                                        expense.currency,
                                    ),
                            },
                            {
                                key: 'date',
                                label: 'Date',
                                render: (expense) => (
                                    <span className="text-muted-foreground">
                                        {expense.occurred_at
                                            ? formatDate(expense.occurred_at)
                                            : '-'}
                                    </span>
                                ),
                            },
                        ]}
                    />

                    <Pagination links={expenses.links} />
                </section>
            </div>
        </AppLayout>
    );
}
