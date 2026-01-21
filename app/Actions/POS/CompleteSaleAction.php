<?php

namespace App\Actions\POS;

use App\Events\SaleCompleted;
use App\Models\Account;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SalePayment;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CompleteSaleAction
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function run(Account $account, array $data): Sale
    {
        $occurredAt = isset($data['occurred_at'])
            ? Carbon::parse($data['occurred_at'])
            : now();

        $sale = DB::transaction(function () use ($account, $data, $occurredAt): Sale {
            $items = collect($data['items'] ?? []);

            if ($items->isEmpty()) {
                throw ValidationException::withMessages([
                    'items' => 'At least one item is required.',
                ]);
            }

            $items = $items->map(function (array $item) {
                $lineTotal = (int) $item['quantity'] * (int) $item['unit_price_cents'];

                return [
                    'product_id' => (int) $item['product_id'],
                    'quantity' => (int) $item['quantity'],
                    'unit_price_cents' => (int) $item['unit_price_cents'],
                    'line_total_cents' => $lineTotal,
                ];
            });

            $totalCents = (int) $items->sum('line_total_cents');

            $sale = Sale::query()->create([
                'account_id' => $account->id,
                'cashier_id' => $data['cashier_id'] ?? null,
                'status' => 'completed',
                'total_cents' => $totalCents,
                'currency' => $data['currency'],
                'occurred_at' => $occurredAt,
            ]);

            $items->each(function (array $item) use ($sale): void {
                SaleItem::query()->create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price_cents' => $item['unit_price_cents'],
                    'line_total_cents' => $item['line_total_cents'],
                ]);
            });

            if (! empty($data['payment'])) {
                $payment = $data['payment'];

                SalePayment::query()->create([
                    'sale_id' => $sale->id,
                    'amount_cents' => $payment['amount_cents'],
                    'currency' => $data['currency'],
                    'method' => $payment['method'] ?? null,
                    'reference' => $payment['reference'] ?? null,
                    'paid_at' => isset($payment['paid_at'])
                        ? Carbon::parse($payment['paid_at'])
                        : $occurredAt,
                ]);
            }

            return $sale->load(['saleItems.product', 'salePayments']);
        });

        event(new SaleCompleted($sale));

        return $sale;
    }
}
