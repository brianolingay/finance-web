<?php

use App\DTOs\CashierSalaryData;
use App\DTOs\ExpenseData;
use App\DTOs\GoodsReceiptData;
use App\DTOs\IncomeData;
use App\DTOs\InventoryPurchaseData;
use App\DTOs\SaleData;
use App\DTOs\SalePaymentData;
use Carbon\CarbonImmutable;

it('parses DTO datetime fields as CarbonImmutable', function (string $dtoClass, array $data, array $dateFields) {
    $dto = $dtoClass::fromArray($data);

    foreach ($dateFields as $property => $dateValue) {
        expect($dto->{$property})
            ->toBeInstanceOf(CarbonImmutable::class)
            ->and($dto->{$property}->toIso8601String())
            ->toBe(CarbonImmutable::parse($dateValue)->toIso8601String());
    }
})->with([
    'cashier salary paid_at' => [
        CashierSalaryData::class,
        [
            'cashier_id' => 1,
            'amount_cents' => 1500,
            'currency' => 'USD',
            'paid_at' => '2024-05-01 10:15:00',
        ],
        ['paidAt' => '2024-05-01 10:15:00'],
    ],
    'expense occurred_at and paid_at' => [
        ExpenseData::class,
        [
            'amount_cents' => 4500,
            'currency' => 'USD',
            'occurred_at' => '2024-05-02 11:30:00',
            'paid_at' => '2024-05-03 12:45:00',
        ],
        [
            'occurredAt' => '2024-05-02 11:30:00',
            'paidAt' => '2024-05-03 12:45:00',
        ],
    ],
    'sale payment paid_at' => [
        SalePaymentData::class,
        [
            'amount_cents' => 9000,
            'paid_at' => '2024-05-04 13:50:00',
        ],
        ['paidAt' => '2024-05-04 13:50:00'],
    ],
    'goods receipt received_at' => [
        GoodsReceiptData::class,
        [
            'items' => [],
            'received_at' => '2024-05-05 08:20:00',
        ],
        ['receivedAt' => '2024-05-05 08:20:00'],
    ],
    'inventory purchase paid_at' => [
        InventoryPurchaseData::class,
        [
            'total_cents' => 12000,
            'currency' => 'USD',
            'paid_at' => '2024-05-06 09:10:00',
        ],
        ['paidAt' => '2024-05-06 09:10:00'],
    ],
    'sale occurred_at' => [
        SaleData::class,
        [
            'currency' => 'USD',
            'items' => [
                [
                    'product_id' => 1,
                    'quantity' => 2,
                    'unit_price_cents' => 2500,
                ],
            ],
            'occurred_at' => '2024-05-07 14:05:00',
        ],
        ['occurredAt' => '2024-05-07 14:05:00'],
    ],
    'income occurred_at and paid_at' => [
        IncomeData::class,
        [
            'amount_cents' => 7800,
            'currency' => 'USD',
            'occurred_at' => '2024-05-08 15:25:00',
            'paid_at' => '2024-05-09 16:35:00',
        ],
        [
            'occurredAt' => '2024-05-08 15:25:00',
            'paidAt' => '2024-05-09 16:35:00',
        ],
    ],
]);

it('keeps DTO datetime fields null when omitted', function (string $dtoClass, array $data, array $properties) {
    $dto = $dtoClass::fromArray($data);

    foreach ($properties as $property) {
        expect($dto->{$property})->toBeNull();
    }
})->with([
    'cashier salary paid_at omitted' => [
        CashierSalaryData::class,
        [
            'cashier_id' => 1,
            'amount_cents' => 1500,
            'currency' => 'USD',
        ],
        ['paidAt'],
    ],
    'expense occurred_at and paid_at omitted' => [
        ExpenseData::class,
        [
            'amount_cents' => 4500,
            'currency' => 'USD',
        ],
        ['occurredAt', 'paidAt'],
    ],
    'sale payment paid_at omitted' => [
        SalePaymentData::class,
        [
            'amount_cents' => 9000,
        ],
        ['paidAt'],
    ],
    'goods receipt received_at omitted' => [
        GoodsReceiptData::class,
        [
            'items' => [],
        ],
        ['receivedAt'],
    ],
    'inventory purchase paid_at omitted' => [
        InventoryPurchaseData::class,
        [
            'total_cents' => 12000,
            'currency' => 'USD',
        ],
        ['paidAt'],
    ],
    'sale occurred_at omitted' => [
        SaleData::class,
        [
            'currency' => 'USD',
            'items' => [
                [
                    'product_id' => 1,
                    'quantity' => 2,
                    'unit_price_cents' => 2500,
                ],
            ],
        ],
        ['occurredAt'],
    ],
    'income occurred_at and paid_at omitted' => [
        IncomeData::class,
        [
            'amount_cents' => 7800,
            'currency' => 'USD',
        ],
        ['occurredAt', 'paidAt'],
    ],
]);

it('sets DTO datetime fields to null when malformed', function (string $dtoClass, array $data, array $properties) {
    $dto = $dtoClass::fromArray($data);

    foreach ($properties as $property) {
        expect($dto->{$property})->toBeNull();
    }
})->with([
    'cashier salary paid_at malformed' => [
        CashierSalaryData::class,
        [
            'cashier_id' => 1,
            'amount_cents' => 1500,
            'currency' => 'USD',
            'paid_at' => 'not-a-date',
        ],
        ['paidAt'],
    ],
    'expense occurred_at and paid_at malformed' => [
        ExpenseData::class,
        [
            'amount_cents' => 4500,
            'currency' => 'USD',
            'occurred_at' => 'not-a-date',
            'paid_at' => 'not-a-date',
        ],
        ['occurredAt', 'paidAt'],
    ],
    'sale payment paid_at malformed' => [
        SalePaymentData::class,
        [
            'amount_cents' => 9000,
            'paid_at' => 'not-a-date',
        ],
        ['paidAt'],
    ],
    'goods receipt received_at malformed' => [
        GoodsReceiptData::class,
        [
            'items' => [],
            'received_at' => 'not-a-date',
        ],
        ['receivedAt'],
    ],
    'inventory purchase paid_at malformed' => [
        InventoryPurchaseData::class,
        [
            'total_cents' => 12000,
            'currency' => 'USD',
            'paid_at' => 'not-a-date',
        ],
        ['paidAt'],
    ],
    'sale occurred_at malformed' => [
        SaleData::class,
        [
            'currency' => 'USD',
            'items' => [
                [
                    'product_id' => 1,
                    'quantity' => 2,
                    'unit_price_cents' => 2500,
                ],
            ],
            'occurred_at' => 'not-a-date',
        ],
        ['occurredAt'],
    ],
    'income occurred_at and paid_at malformed' => [
        IncomeData::class,
        [
            'amount_cents' => 7800,
            'currency' => 'USD',
            'occurred_at' => 'not-a-date',
            'paid_at' => 'not-a-date',
        ],
        ['occurredAt', 'paidAt'],
    ],
]);

it('defaults sale currency to an empty string when missing', function () {
    $sale = SaleData::fromArray([
        'items' => [
            [
                'product_id' => 1,
                'quantity' => 2,
                'unit_price_cents' => 2500,
            ],
        ],
    ]);

    expect($sale->currency)->toBe('');
});
