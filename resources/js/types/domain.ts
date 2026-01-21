export interface AccountSummary {
    id: number;
    name: string;
    type?: string;
}

export interface Category {
    id: number;
    name: string;
}

export interface Expense {
    id: number;
    description: string | null;
    amount_cents: number;
    currency: string;
    occurred_at: string;
}

export interface Income {
    id: number;
    description: string | null;
    amount_cents: number;
    currency: string;
    occurred_at: string;
}

export interface Transaction {
    id: number;
    direction: 'credit' | 'debit';
    amount_cents: number;
    currency: string;
    occurred_at: string;
    source_type: string;
}

export interface Product {
    id: number;
    name: string;
    sku: string | null;
    unit_cost_cents?: number | null;
    unit_price_cents?: number | null;
}

export interface GoodsReceiptSummary {
    id: number;
    reference: string | null;
    status: string | null;
    received_at: string;
}

export interface Cashier {
    id: number;
    name: string | null;
    status: string | null;
    user: {
        name: string;
        email: string;
    };
}
