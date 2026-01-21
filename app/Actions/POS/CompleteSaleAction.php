<?php

namespace App\Actions\POS;

use App\DTOs\SaleData;
use App\Events\SaleCompleted;
use App\Models\Account;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SalePayment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CompleteSaleAction
{
    public function run(Account $account, SaleData $data, ?User $user = null): Sale
    {
        $occurredAt = $data->occurredAt ?? now();

        $sale = DB::transaction(function () use ($account, $data, $occurredAt, $user): Sale {
            $cashierId = $data->cashierId;

            if ($cashierId === null && $user !== null) {
                $cashierId = $account->cashiers()
                    ->where('user_id', $user->id)
                    ->value('id');
            }

            if ($data->items === []) {
                throw ValidationException::withMessages([
                    'items' => 'At least one item is required.',
                ]);
            }

            $items = collect($data->items)->map(fn ($item) => [
                'product_id' => $item->productId,
                'quantity' => $item->quantity,
                'unit_price_cents' => $item->unitPriceCents,
                'line_total_cents' => $item->lineTotalCents(),
            ]);

            $totalCents = (int) $items->sum('line_total_cents');

            $sale = Sale::query()->create([
                'account_id' => $account->id,
                'cashier_id' => $cashierId,
                'status' => 'completed',
                'total_cents' => $totalCents,
                'currency' => $data->currency,
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

            if ($data->payment !== null) {
                SalePayment::query()->create([
                    'sale_id' => $sale->id,
                    'amount_cents' => $data->payment->amountCents,
                    'currency' => $data->currency,
                    'method' => $data->payment->method,
                    'reference' => $data->payment->reference,
                    'paid_at' => $data->payment->paidAt ?? $occurredAt,
                ]);
            }

            return $sale->load(['saleItems.product', 'salePayments']);
        });

        event(new SaleCompleted($sale));

        return $sale;
    }
}
