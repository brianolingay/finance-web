import { Head } from '@inertiajs/react';

import { StockBadge } from '@/components/inventory/stock-badge';
import { PageHeader } from '@/components/layout/page-header';
import { DataTable } from '@/components/tables/data-table';
import { EmptyState } from '@/components/tables/empty-state';
import { Pagination } from '@/components/tables/pagination';
import AppLayout from '@/layouts/app-layout';
import { formatCents } from '@/lib/money';
import { index as productsIndex } from '@/routes/accounts/inventory/products';

import { type BreadcrumbItem } from '@/types';
import { type PaginatedResponse } from '@/types/api';
import { type AccountSummary, type Product } from '@/types/domain';

interface ProductsPageProps {
    account: AccountSummary;
    products: PaginatedResponse<Product>;
}

export default function ProductsIndex({ account, products }: ProductsPageProps) {
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Inventory',
            href: productsIndex(account).url,
        },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`${account.name} Inventory`} />

            <div className="flex flex-col gap-6 p-6">
                <PageHeader
                    title="Products"
                    description="Keep tabs on sellable items and reference prices."
                />

                <section className="flex flex-col gap-4">
                    <DataTable
                        data={products.data}
                        getRowKey={(product) => product.id}
                        emptyState={
                            <EmptyState
                                title="No products yet"
                                description="Create products to start tracking inventory."
                            />
                        }
                        columns={[
                            {
                                key: 'name',
                                label: 'Name',
                                render: (product) => (
                                    <span className="font-medium">
                                        {product.name}
                                    </span>
                                ),
                            },
                            {
                                key: 'sku',
                                label: 'SKU',
                                render: (product) => (
                                    <span className="text-muted-foreground">
                                        {product.sku ?? '--'}
                                    </span>
                                ),
                            },
                            {
                                key: 'unit_cost',
                                label: 'Unit cost',
                                render: (product) =>
                                    product.unit_cost_cents != null
                                        ? formatCents(
                                              product.unit_cost_cents,
                                          )
                                        : '--',
                            },
                            {
                                key: 'unit_price',
                                label: 'Unit price',
                                render: (product) =>
                                    product.unit_price_cents != null
                                        ? formatCents(
                                              product.unit_price_cents,
                                          )
                                        : '--',
                            },
                            {
                                key: 'status',
                                label: 'Status',
                                render: (product) => (
                                    <StockBadge
                                        status={
                                            product.unit_price_cents != null
                                                ? 'priced'
                                                : 'unpriced'
                                        }
                                    />
                                ),
                            },
                        ]}
                    />

                    <Pagination links={products.links} />
                </section>
            </div>
        </AppLayout>
    );
}
