import { Form, Head } from '@inertiajs/react';
import { useMemo, useState } from 'react';

import { DateInput } from '@/components/forms/date-input';
import { FormField } from '@/components/forms/form-field';
import { MoneyInput } from '@/components/forms/money-input';
import { SelectField } from '@/components/forms/select-field';
import { SubmitBar } from '@/components/forms/submit-bar';
import { ReceiptItemsEditor } from '@/components/inventory/receipt-items-editor';
import { PageHeader } from '@/components/layout/page-header';
import { DataTable } from '@/components/tables/data-table';
import { EmptyState } from '@/components/tables/empty-state';
import { Pagination } from '@/components/tables/pagination';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';

import AppLayout from '@/layouts/app-layout';

import { formatDate } from '@/lib/format';
import { formatCents } from '@/lib/money';

import { create as receiptsCreate } from '@/routes/accounts/inventory/receipts';

import { type BreadcrumbItem } from '@/types';
import { type PaginatedResponse } from '@/types/api';
import {
    type AccountSummary,
    type GoodsReceiptSummary,
    type Product,
} from '@/types/domain';

import GoodsReceiptController from '@/actions/App/Http/Controllers/GoodsReceiptController';
import InventoryPurchaseController from '@/actions/App/Http/Controllers/InventoryPurchaseController';

interface Supplier {
    id: number;
    name: string;
}

interface ReceiptItemState {
    product_id: number;
    name: string;
    unit_cost_cents: number;
    quantity: number;
}

interface InventoryReceiptsProps {
    account: AccountSummary;
    products: Product[];
    suppliers: Supplier[];
    receipts: PaginatedResponse<GoodsReceiptSummary>;
}

export default function InventoryReceipts({
    account,
    products,
    suppliers,
    receipts,
}: InventoryReceiptsProps) {
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Goods receipts',
            href: receiptsCreate(account).url,
        },
    ];
    const [receiptItems, setReceiptItems] = useState<ReceiptItemState[]>([]);
    const [supplierId, setSupplierId] = useState('');
    const [purchaseReceiptId, setPurchaseReceiptId] = useState('');

    const receiptTotal = useMemo(() => {
        return receiptItems.reduce(
            (sum, item) => sum + item.unit_cost_cents * item.quantity,
            0,
        );
    }, [receiptItems]);

    const addReceiptItem = (product: Product) => {
        setReceiptItems((current) => {
            const existing = current.find(
                (item) => item.product_id === product.id,
            );

            if (existing) {
                return current.map((item) =>
                    item.product_id === product.id
                        ? { ...item, quantity: item.quantity + 1 }
                        : item,
                );
            }

            return [
                ...current,
                {
                    product_id: product.id,
                    name: product.name,
                    unit_cost_cents: product.unit_cost_cents ?? 0,
                    quantity: 1,
                },
            ];
        });
    };

    const updateReceiptItem = (
        index: number,
        patch: Partial<ReceiptItemState>,
    ) => {
        setReceiptItems((current) =>
            current.map((item, itemIndex) =>
                itemIndex === index ? { ...item, ...patch } : item,
            ),
        );
    };

    const removeReceiptItem = (index: number) => {
        setReceiptItems((current) =>
            current.filter((_, itemIndex) => itemIndex !== index),
        );
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`${account.name} Goods Receipts`} />

            <div className="flex flex-col gap-6 p-6">
                <PageHeader
                    title="Goods receipts"
                    description="Log incoming stock and match it to payments."
                />

                <div className="grid gap-6 xl:grid-cols-[1fr,360px]">
                    <Card>
                        <CardHeader>
                            <CardTitle className="text-base font-semibold">
                                Receive goods
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            <Form
                                {...GoodsReceiptController.store.form({
                                    account: account.id,
                                })}
                                className="flex flex-col gap-4"
                                options={{
                                    preserveScroll: true,
                                    onSuccess: () => {
                                        setReceiptItems([]);
                                        setSupplierId('');
                                    },
                                }}
                            >
                                {({ processing, errors }) => (
                                    <>
                                        <div className="grid gap-4 sm:grid-cols-2">
                                            <SelectField
                                                name="supplier_id"
                                                label="Supplier"
                                                value={supplierId}
                                                onValueChange={setSupplierId}
                                                placeholder="Select supplier"
                                                error={errors.supplier_id}
                                                options={suppliers.map((supplier) => ({
                                                    value: String(supplier.id),
                                                    label: supplier.name,
                                                }))}
                                            />
                                            <FormField
                                                label="Received at"
                                                htmlFor="received_at"
                                                error={errors.received_at}
                                            >
                                                <DateInput
                                                    id="received_at"
                                                    name="received_at"
                                                />
                                            </FormField>
                                        </div>

                                        <FormField
                                            label="Reference"
                                            htmlFor="reference"
                                            error={errors.reference}
                                        >
                                            <Input
                                                id="reference"
                                                name="reference"
                                                placeholder="GR-0012"
                                            />
                                        </FormField>

                                        <ReceiptItemsEditor
                                            products={products}
                                            items={receiptItems}
                                            onAdd={addReceiptItem}
                                            onUpdate={updateReceiptItem}
                                            onRemove={removeReceiptItem}
                                            error={
                                                errors.items ||
                                                errors['items.0']
                                            }
                                        />

                                        <SubmitBar
                                            label="Total value"
                                            value={formatCents(receiptTotal)}
                                            actionLabel="Save receipt"
                                            processing={processing}
                                            disabled={receiptItems.length === 0}
                                        />
                                    </>
                                )}
                            </Form>
                        </CardContent>
                    </Card>

                    <div className="flex flex-col gap-6">
                        <Card>
                            <CardHeader>
                                <CardTitle className="text-base font-semibold">
                                    Record payment
                                </CardTitle>
                            </CardHeader>
                            <CardContent>
                                <Form
                                    {...InventoryPurchaseController.store.form({
                                        account: account.id,
                                    })}
                                    className="flex flex-col gap-4"
                                    options={{
                                        preserveScroll: true,
                                        onSuccess: () => setPurchaseReceiptId(''),
                                    }}
                                >
                                    {({ processing, errors }) => (
                                        <>
                                            <SelectField
                                                name="goods_receipt_id"
                                                label="Receipt"
                                                value={purchaseReceiptId}
                                                onValueChange={
                                                    setPurchaseReceiptId
                                                }
                                                placeholder="Match a receipt"
                                                options={receipts.data.map(
                                                    (receipt) => ({
                                                        value: String(
                                                            receipt.id,
                                                        ),
                                                        label:
                                                            receipt.reference ??
                                                            `Receipt #${receipt.id}`,
                                                    }),
                                                )}
                                                error={errors.goods_receipt_id}
                                            />

                                            <FormField
                                                label="Amount (cents)"
                                                htmlFor="total_cents"
                                                error={errors.total_cents}
                                            >
                                                <MoneyInput
                                                    id="total_cents"
                                                    name="total_cents"
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
                                                actionLabel="Record payment"
                                                processing={processing}
                                            />
                                        </>
                                    )}
                                </Form>
                            </CardContent>
                        </Card>

                        <Card>
                            <CardHeader>
                                <CardTitle className="text-base font-semibold">
                                    Recent receipts
                                </CardTitle>
                            </CardHeader>
                            <CardContent className="flex flex-col gap-4">
                                <DataTable
                                    data={receipts.data}
                                    getRowKey={(receipt) => receipt.id}
                                    emptyState={
                                        <EmptyState
                                            title="No receipts yet"
                                            description="Receive stock to create your first receipt."
                                        />
                                    }
                                    columns={[
                                        {
                                            key: 'reference',
                                            label: 'Reference',
                                            render: (receipt) => (
                                                <span className="font-medium">
                                                    {receipt.reference ??
                                                        `Receipt #${receipt.id}`}
                                                </span>
                                            ),
                                        },
                                        {
                                            key: 'status',
                                            label: 'Status',
                                            render: (receipt) => (
                                                <span className="text-muted-foreground">
                                                    {receipt.status ??
                                                        'received'}
                                                </span>
                                            ),
                                        },
                                        {
                                            key: 'received',
                                            label: 'Received',
                                            render: (receipt) => (
                                                <span className="text-muted-foreground">
                                                    {formatDate(
                                                        receipt.received_at,
                                                    )}
                                                </span>
                                            ),
                                        },
                                    ]}
                                />

                                <Pagination links={receipts.links} />
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
