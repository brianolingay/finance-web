import { Form, Head } from '@inertiajs/react';
import { useMemo, useState } from 'react';

import SaleController from '@/actions/App/Http/Controllers/SaleController';
import { FormField } from '@/components/forms/form-field';
import { MoneyInput } from '@/components/forms/money-input';
import { SubmitBar } from '@/components/forms/submit-bar';
import { PageHeader } from '@/components/layout/page-header';
import { CartSummary } from '@/components/pos/cart-summary';
import { PaymentMethodSelect } from '@/components/pos/payment-method-select';
import { ProductSelect } from '@/components/pos/product-select';
import { SaleItemRow } from '@/components/pos/sale-item-row';
import { EmptyState } from '@/components/tables/empty-state';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/app-layout';

import { create as newSale } from '@/routes/accounts/pos/sales';

import { type BreadcrumbItem } from '@/types';
import { type AccountSummary, type Product } from '@/types/domain';

interface SaleItemState {
    product_id: number;
    name: string;
    unit_price_cents: number;
    quantity: number;
}

interface NewSaleProps {
    account: AccountSummary;
    products: Product[];
    cashierId: number | null;
}

export default function NewSale({ account, products, cashierId }: NewSaleProps) {
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'New sale',
            href: newSale(account).url,
        },
    ];
    const [items, setItems] = useState<SaleItemState[]>([]);
    const [recordPayment, setRecordPayment] = useState(false);
    const [paymentMethod, setPaymentMethod] = useState('cash');

    const totalCents = useMemo(() => {
        return items.reduce(
            (sum, item) => sum + item.unit_price_cents * item.quantity,
            0,
        );
    }, [items]);

    const addProduct = (product: Product) => {
        setItems((current) => {
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
                    unit_price_cents: product.unit_price_cents ?? 0,
                    quantity: 1,
                },
            ];
        });
    };

    const updateItem = (index: number, patch: Partial<SaleItemState>) => {
        setItems((current) =>
            current.map((item, itemIndex) =>
                itemIndex === index ? { ...item, ...patch } : item,
            ),
        );
    };

    const removeItem = (index: number) => {
        setItems((current) =>
            current.filter((_, itemIndex) => itemIndex !== index),
        );
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`${account.name} POS`} />

            <div className="flex flex-col gap-6 p-6">
                <PageHeader
                    title="New sale"
                    description="Tap products, adjust quantities, and confirm the payment."
                />

                <div className="grid gap-6 lg:grid-cols-[1.15fr,0.85fr]">
                    <section className="flex flex-col gap-4">
                        <h2 className="text-base font-semibold">Products</h2>
                        <ProductSelect products={products} onSelect={addProduct} />
                    </section>

                    <section className="flex flex-col gap-4">
                        <h2 className="text-base font-semibold">Sale summary</h2>
                        <Form
                            {...SaleController.store.form({
                                account: account.id,
                            })}
                            options={{
                                preserveScroll: true,
                                onSuccess: () => {
                                    setItems([]);
                                    setRecordPayment(false);
                                    setPaymentMethod('cash');
                                },
                            }}
                            className="flex flex-col gap-4"
                        >
                            {({ processing, errors }) => (
                                <>
                                    {cashierId ? (
                                        <input
                                            type="hidden"
                                            name="cashier_id"
                                            value={cashierId}
                                        />
                                    ) : null}

                                    {items.length === 0 ? (
                                        <EmptyState
                                            title="Cart is empty"
                                            description="Add at least one product to start a sale."
                                        />
                                    ) : (
                                        <div className="space-y-3">
                                            {items.map((item, index) => (
                                                <SaleItemRow
                                                    key={`${item.product_id}-${index}`}
                                                    index={index}
                                                    name={item.name}
                                                    unitPriceCents={
                                                        item.unit_price_cents
                                                    }
                                                    quantity={item.quantity}
                                                    productId={item.product_id}
                                                    onUpdate={(patch) =>
                                                        updateItem(index, {
                                                            quantity:
                                                                patch.quantity,
                                                            unit_price_cents:
                                                                patch.unitPriceCents,
                                                        })
                                                    }
                                                    onRemove={() =>
                                                        removeItem(index)
                                                    }
                                                />
                                            ))}
                                        </div>
                                    )}
                                    {errors.items || errors['items.0'] ? (
                                        <p className="text-sm text-red-600 dark:text-red-400">
                                            {errors.items ??
                                                errors['items.0']}
                                        </p>
                                    ) : null}

                                    <div className="grid gap-3 sm:grid-cols-2">
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
                                        <div className="flex items-center gap-2 pt-6">
                                            <Checkbox
                                                id="record_payment"
                                                checked={recordPayment}
                                                onCheckedChange={(value) =>
                                                    setRecordPayment(
                                                        Boolean(value),
                                                    )
                                                }
                                            />
                                            <Label htmlFor="record_payment">
                                                Record payment now
                                            </Label>
                                        </div>
                                    </div>

                                    {recordPayment ? (
                                        <div className="grid gap-3">
                                            <FormField
                                                label="Payment amount (cents)"
                                                htmlFor="payment_amount_cents"
                                                error={
                                                    errors[
                                                        'payment.amount_cents'
                                                    ]
                                                }
                                            >
                                                <MoneyInput
                                                    id="payment_amount_cents"
                                                    name="payment[amount_cents]"
                                                    placeholder="0"
                                                    required
                                                />
                                            </FormField>
                                            <PaymentMethodSelect
                                                value={paymentMethod}
                                                onValueChange={setPaymentMethod}
                                            />
                                        </div>
                                    ) : null}

                                    <CartSummary
                                        totalCents={totalCents}
                                        itemCount={items.length}
                                    />

                                    <SubmitBar
                                        actionLabel="Complete sale"
                                        processing={processing}
                                        disabled={items.length === 0}
                                    />
                                </>
                            )}
                        </Form>
                    </section>
                </div>
            </div>
        </AppLayout>
    );
}
