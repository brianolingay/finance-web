import { Form, Head } from '@inertiajs/react';
import { useState } from 'react';

import CashierController from '@/actions/App/Http/Controllers/CashierController';
import CashierSalaryController from '@/actions/App/Http/Controllers/CashierSalaryController';
import AppLayout from '@/layouts/app-layout';
import { index as cashiersIndex } from '@/routes/accounts/cashiers';

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
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';

import { type BreadcrumbItem } from '@/types';
import { type PaginatedResponse } from '@/types/api';
import { type AccountSummary, type Cashier } from '@/types/domain';

interface CashiersPageProps {
    account: AccountSummary;
    cashiers: PaginatedResponse<Cashier>;
}

export default function CashiersIndex({
    account,
    cashiers,
}: CashiersPageProps) {
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Cashiers',
            href: cashiersIndex(account).url,
        },
    ];
    const [cashierId, setCashierId] = useState('');

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`${account.name} Cashiers`} />

            <div className="flex flex-col gap-6 p-6">
                <PageHeader
                    title="Cashiers"
                    description="Invite teammates and run simple salary payouts."
                    actions={
                        <Dialog>
                            <DialogTrigger asChild>
                                <Button>Invite cashier</Button>
                            </DialogTrigger>
                            <DialogContent>
                                <DialogHeader>
                                    <DialogTitle>Invite cashier</DialogTitle>
                                </DialogHeader>
                                <Form
                                    {...CashierController.store.form({
                                        account: account.id,
                                    })}
                                    className="flex flex-col gap-4"
                                    options={{ preserveScroll: true }}
                                    resetOnSuccess
                                >
                                    {({ processing, errors }) => (
                                        <>
                                            <FormField
                                                label="Email"
                                                htmlFor="email"
                                                error={errors.email}
                                            >
                                                <Input
                                                    id="email"
                                                    name="email"
                                                    type="email"
                                                    placeholder="cashier@shop.com"
                                                    required
                                                />
                                            </FormField>
                                            <FormField
                                                label="Display name"
                                                htmlFor="name"
                                                error={errors.name}
                                            >
                                                <Input
                                                    id="name"
                                                    name="name"
                                                    placeholder="Jamie Cashier"
                                                />
                                            </FormField>
                                            <SubmitBar
                                                actionLabel="Send invite"
                                                processing={processing}
                                            />
                                        </>
                                    )}
                                </Form>
                            </DialogContent>
                        </Dialog>
                    }
                />

                <div className="grid gap-6 lg:grid-cols-[360px,1fr]">
                    <Card>
                        <CardHeader>
                            <CardTitle className="text-base font-semibold">
                                Run salary
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            <Form
                                {...CashierSalaryController.store.form({
                                    account: account.id,
                                })}
                                className="flex flex-col gap-4"
                                options={{
                                    preserveScroll: true,
                                    onSuccess: () => setCashierId(''),
                                }}
                            >
                                {({ processing, errors }) => (
                                    <>
                                        <SelectField
                                            name="cashier_id"
                                            label="Cashier"
                                            value={cashierId}
                                            onValueChange={setCashierId}
                                            placeholder="Select cashier"
                                            options={cashiers.data.map(
                                                (cashier) => ({
                                                    value: String(cashier.id),
                                                    label:
                                                        cashier.name ??
                                                        cashier.user.name,
                                                }),
                                            )}
                                            error={errors.cashier_id}
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
                                                    defaultValue="USD"
                                                    required
                                                />
                                            </FormField>
                                        </div>

                                        <FormField
                                            label="Paid at"
                                            htmlFor="paid_at"
                                            error={errors.paid_at}
                                        >
                                            <DateInput
                                                id="paid_at"
                                                name="paid_at"
                                            />
                                        </FormField>

                                        <SubmitBar
                                            actionLabel="Create salary"
                                            processing={processing}
                                            disabled={!cashierId}
                                        />
                                    </>
                                )}
                            </Form>
                        </CardContent>
                    </Card>

                    <section className="flex flex-col gap-4">
                        <DataTable
                            data={cashiers.data}
                            getRowKey={(cashier) => cashier.id}
                            emptyState={
                                <EmptyState
                                    title="No cashiers yet"
                                    description="Invite a cashier to start ringing sales."
                                />
                            }
                            columns={[
                                {
                                    key: 'name',
                                    label: 'Name',
                                    render: (cashier) => (
                                        <span className="font-medium">
                                            {cashier.name ?? cashier.user.name}
                                        </span>
                                    ),
                                },
                                {
                                    key: 'email',
                                    label: 'Email',
                                    render: (cashier) => (
                                        <span className="text-muted-foreground">
                                            {cashier.user.email}
                                        </span>
                                    ),
                                },
                                {
                                    key: 'status',
                                    label: 'Status',
                                    render: (cashier) => (
                                        <span className="text-muted-foreground">
                                            {cashier.status ?? 'active'}
                                        </span>
                                    ),
                                },
                            ]}
                        />

                        <Pagination links={cashiers.links} />
                    </section>
                </div>
            </div>
        </AppLayout>
    );
}
