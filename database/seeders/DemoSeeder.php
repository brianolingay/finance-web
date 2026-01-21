<?php

namespace Database\Seeders;

use App\Actions\Finance\CreateExpenseAction;
use App\Actions\Finance\CreateIncomeAction;
use App\Actions\Inventory\CreateGoodsReceiptAction;
use App\Actions\Inventory\RecordInventoryPurchasePaymentAction;
use App\Actions\Payroll\CreateCashierSalaryAction;
use App\Actions\Payroll\InviteCashierAction;
use App\Actions\POS\CompleteSaleAction;
use App\DTOs\CashierInviteData;
use App\DTOs\CashierSalaryData;
use App\DTOs\ExpenseData;
use App\DTOs\GoodsReceiptData;
use App\DTOs\IncomeData;
use App\DTOs\InventoryPurchaseData;
use App\DTOs\SaleData;
use App\Models\Account;
use App\Models\AccountMember;
use App\Models\Cashier;
use App\Models\CashierSalary;
use App\Models\Category;
use App\Models\Expense;
use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptItem;
use App\Models\Income;
use App\Models\InventoryMovement;
use App\Models\InventoryPurchase;
use App\Models\Product;
use App\Models\SalaryRule;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SalePayment;
use App\Models\Supplier;
use App\Models\Transaction;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class DemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = CarbonImmutable::now()->startOfDay();
        $currency = 'PHP';

        $owners = [
            [
                'email' => 'owner1@example.com',
                'name' => 'Owner One',
                'cashier_email' => 'cashier1@example.com',
                'cashier_name' => 'Cashier One',
                'finance_account_name' => 'Owner One Finance',
                'pos_account_name' => 'Owner One Store',
            ],
            [
                'email' => 'owner2@example.com',
                'name' => 'Owner Two',
                'cashier_email' => 'cashier2@example.com',
                'cashier_name' => 'Cashier Two',
                'finance_account_name' => 'Owner Two Finance',
                'pos_account_name' => 'Owner Two Store',
            ],
        ];

        foreach ($owners as $index => $ownerData) {
            $owner = User::query()->firstOrCreate(
                ['email' => $ownerData['email']],
                [
                    'name' => $ownerData['name'],
                    'password' => Hash::make('secret'),
                ],
            );

            $cashierUser = User::query()->firstOrCreate(
                ['email' => $ownerData['cashier_email']],
                [
                    'name' => $ownerData['cashier_name'],
                    'password' => Hash::make('secret'),
                ],
            );

            $financeAccount = $this->firstOrCreateAccount(
                $owner,
                $ownerData['finance_account_name'],
                'finance',
            );

            $posAccount = $this->firstOrCreateAccount(
                $owner,
                $ownerData['pos_account_name'],
                'pos',
            );

            $this->firstOrCreateAccountMember($financeAccount, $owner, 'owner');
            $this->firstOrCreateAccountMember($posAccount, $owner, 'owner');

            $cashier = app(InviteCashierAction::class)->run(
                $posAccount,
                CashierInviteData::fromArray([
                    'email' => $cashierUser->email,
                    'name' => $cashierUser->name,
                ]),
            );

            $incomeCategories = $this->seedCategories($financeAccount, 'income', [
                'Sales',
                'Services',
                'Interest',
            ]);

            $expenseCategories = $this->seedCategories($financeAccount, 'expense', [
                'Rent',
                'Utilities',
                'Supplies',
                'Transport',
                'Marketing',
                'Payroll',
            ]);

            $this->seedIncomes($financeAccount, $incomeCategories, $now, $currency, 50);
            $this->seedExpenses($financeAccount, $expenseCategories, $now, $currency, 50);

            $products = $this->seedProducts($posAccount, $index, 50);
            $suppliers = $this->seedSuppliers($posAccount, $index);

            $receipts = $this->seedGoodsReceipts(
                $posAccount,
                $suppliers,
                $products,
                $now,
                $index,
                50,
            );

            $this->seedInventoryPurchases($posAccount, $receipts, $currency, 25);

            SalaryRule::factory()->create([
                'account_id' => $posAccount->id,
                'name' => 'Default Salary Rule',
                'type' => 'fixed',
                'fixed_cents' => 150_000,
                'commission_bps' => null,
                'currency' => $currency,
                'is_active' => true,
            ]);

            $this->seedCashierSalaries(
                $posAccount,
                $cashier,
                $now,
                $currency,
                10,
            );

            $this->seedSales(
                $posAccount,
                $cashier,
                $products,
                $now,
                $index,
                50,
                $currency,
            );

            $this->reportOwnerSummary($owner, $financeAccount, $posAccount);
        }
    }

    private function firstOrCreateAccount(User $owner, string $name, string $type): Account
    {
        $attributes = Account::factory()->make([
            'owner_user_id' => $owner->id,
            'name' => $name,
            'type' => $type,
        ])->getAttributes();

        return Account::query()->firstOrCreate(
            [
                'owner_user_id' => $owner->id,
                'name' => $name,
                'type' => $type,
            ],
            $attributes,
        );
    }

    private function firstOrCreateAccountMember(Account $account, User $user, string $role): AccountMember
    {
        $attributes = AccountMember::factory()->make([
            'account_id' => $account->id,
            'user_id' => $user->id,
            'role' => $role,
        ])->getAttributes();

        return AccountMember::query()->firstOrCreate(
            [
                'account_id' => $account->id,
                'user_id' => $user->id,
            ],
            $attributes,
        );
    }

    /**
     * @param  list<string>  $names
     * @return list<Category>
     */
    private function seedCategories(Account $account, string $type, array $names): array
    {
        $categories = [];

        foreach ($names as $name) {
            $attributes = Category::factory()->make([
                'account_id' => $account->id,
                'name' => $name,
                'type' => $type,
            ])->getAttributes();

            $categories[] = Category::query()->firstOrCreate(
                [
                    'account_id' => $account->id,
                    'name' => $name,
                    'type' => $type,
                ],
                $attributes,
            );
        }

        return $categories;
    }

    /**
     * @param  list<Category>  $categories
     */
    private function seedIncomes(
        Account $account,
        array $categories,
        CarbonImmutable $now,
        string $currency,
        int $count,
    ): void {
        for ($index = 0; $index < $count; $index++) {
            $occurredAt = $this->dateWithinRange($now, $index, $count, 60);
            $category = $categories[$index % count($categories)];
            $amountCents = 10_000 + (($index * 137) % 9_000);

            $attributes = Income::factory()->make([
                'account_id' => $account->id,
                'category_id' => $category->id,
                'description' => sprintf('Demo Income %02d', $index + 1),
                'status' => 'posted',
                'amount_cents' => $amountCents,
                'currency' => $currency,
                'occurred_at' => $occurredAt->toDateTimeString(),
                'paid_at' => $occurredAt->toDateTimeString(),
            ])->getAttributes();

            app(CreateIncomeAction::class)->run(
                $account,
                IncomeData::fromArray($attributes),
            );
        }
    }

    /**
     * @param  list<Category>  $categories
     */
    private function seedExpenses(
        Account $account,
        array $categories,
        CarbonImmutable $now,
        string $currency,
        int $count,
    ): void {
        for ($index = 0; $index < $count; $index++) {
            $occurredAt = $this->dateWithinRange($now, $index, $count, 60);
            $category = $categories[$index % count($categories)];
            $amountCents = 8_000 + (($index * 173) % 7_000);

            $attributes = Expense::factory()->make([
                'account_id' => $account->id,
                'category_id' => $category->id,
                'description' => sprintf('Demo Expense %02d', $index + 1),
                'status' => 'posted',
                'amount_cents' => $amountCents,
                'currency' => $currency,
                'occurred_at' => $occurredAt->toDateTimeString(),
                'paid_at' => $occurredAt->toDateTimeString(),
            ])->getAttributes();

            app(CreateExpenseAction::class)->run(
                $account,
                ExpenseData::fromArray($attributes),
            );
        }
    }

    /**
     * @return list<Product>
     */
    private function seedProducts(Account $account, int $ownerIndex, int $count): array
    {
        $products = [];

        for ($index = 0; $index < $count; $index++) {
            $unitCost = 800 + (($index * 37) % 900);
            $unitPrice = $unitCost + 250 + (($index * 19) % 500);

            $products[] = Product::factory()->create([
                'account_id' => $account->id,
                'name' => sprintf('Product %02d', $index + 1),
                'sku' => sprintf('SKU-%d-%03d', $ownerIndex + 1, $index + 1),
                'description' => sprintf('Demo product %02d for POS catalog.', $index + 1),
                'unit_cost_cents' => $unitCost,
                'unit_price_cents' => $unitPrice,
            ]);
        }

        return $products;
    }

    /**
     * @return list<Supplier>
     */
    private function seedSuppliers(Account $account, int $ownerIndex): array
    {
        $suppliers = [];

        $suppliers[] = Supplier::factory()->create([
            'account_id' => $account->id,
            'name' => sprintf('Supplier %dA', $ownerIndex + 1),
            'email' => sprintf('supplier%da@example.com', $ownerIndex + 1),
            'phone' => '09171234567',
            'notes' => 'Primary demo supplier.',
        ]);

        $suppliers[] = Supplier::factory()->create([
            'account_id' => $account->id,
            'name' => sprintf('Supplier %dB', $ownerIndex + 1),
            'email' => sprintf('supplier%db@example.com', $ownerIndex + 1),
            'phone' => '09179876543',
            'notes' => 'Secondary demo supplier.',
        ]);

        return $suppliers;
    }

    /**
     * @param  list<Supplier>  $suppliers
     * @param  list<Product>  $products
     * @return list<GoodsReceipt>
     */
    private function seedGoodsReceipts(
        Account $account,
        array $suppliers,
        array $products,
        CarbonImmutable $now,
        int $ownerIndex,
        int $count,
    ): array {
        $receipts = [];
        $productCount = count($products);

        for ($index = 0; $index < $count; $index++) {
            $receivedAt = $this->dateWithinRange($now, $index, $count, 45);
            $supplier = $suppliers[$index % count($suppliers)];
            $reference = sprintf('GR-%d-%03d', $ownerIndex + 1, $index + 1);
            $itemCount = 2 + ($index % 4);
            $items = [];

            for ($itemIndex = 0; $itemIndex < $itemCount; $itemIndex++) {
                $product = $products[($index + $itemIndex) % $productCount];
                $quantity = 1 + (($index + $itemIndex) % 8);
                $unitCost = $product->unit_cost_cents ?? 900;

                $itemAttributes = GoodsReceiptItem::factory()->make([
                    'goods_receipt_id' => 1,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_cost_cents' => $unitCost,
                ])->getAttributes();

                $items[] = [
                    'product_id' => $itemAttributes['product_id'],
                    'quantity' => $itemAttributes['quantity'],
                    'unit_cost_cents' => $itemAttributes['unit_cost_cents'],
                ];
            }

            $attributes = GoodsReceipt::factory()->make([
                'account_id' => $account->id,
                'supplier_id' => $supplier->id,
                'status' => 'received',
                'reference' => $reference,
                'received_at' => $receivedAt->toDateTimeString(),
                'notes' => sprintf('Demo receipt %s.', $reference),
            ])->getAttributes();

            $attributes['items'] = $items;

            $receipts[] = app(CreateGoodsReceiptAction::class)->run(
                $account,
                GoodsReceiptData::fromArray($attributes),
            );
        }

        return $receipts;
    }

    /**
     * @param  list<GoodsReceipt>  $receipts
     */
    private function seedInventoryPurchases(
        Account $account,
        array $receipts,
        string $currency,
        int $count,
    ): void {
        $selectedReceipts = array_slice($receipts, 0, $count);

        foreach ($selectedReceipts as $receipt) {
            $totalCents = (int) $receipt->goodsReceiptItems->sum('line_total_cents');
            $paidAt = $receipt->received_at ?? $receipt->created_at;

            $attributes = InventoryPurchase::factory()->make([
                'account_id' => $account->id,
                'supplier_id' => $receipt->supplier_id,
                'goods_receipt_id' => $receipt->id,
                'status' => 'paid',
                'total_cents' => $totalCents,
                'currency' => $currency,
                'paid_at' => $paidAt?->toDateTimeString(),
            ])->getAttributes();

            app(RecordInventoryPurchasePaymentAction::class)->run(
                $account,
                InventoryPurchaseData::fromArray($attributes),
            );
        }
    }

    private function seedCashierSalaries(
        Account $account,
        Cashier $cashier,
        CarbonImmutable $now,
        string $currency,
        int $count,
    ): void {
        for ($index = 0; $index < $count; $index++) {
            $paidAt = $this->dateWithinRange($now, $index, $count, 60);
            $amountCents = 12_000 + (($index * 211) % 8_000);

            $attributes = CashierSalary::factory()->make([
                'account_id' => $account->id,
                'cashier_id' => $cashier->id,
                'salary_rule_id' => null,
                'amount_cents' => $amountCents,
                'currency' => $currency,
                'paid_at' => $paidAt->toDateTimeString(),
                'status' => 'paid',
            ])->getAttributes();

            app(CreateCashierSalaryAction::class)->run(
                $account,
                CashierSalaryData::fromArray($attributes),
            );
        }
    }

    /**
     * @param  list<Product>  $products
     */
    private function seedSales(
        Account $account,
        Cashier $cashier,
        array $products,
        CarbonImmutable $now,
        int $ownerIndex,
        int $count,
        string $currency,
    ): void {
        $productCount = count($products);

        for ($index = 0; $index < $count; $index++) {
            $occurredAt = $this->dateWithinRange($now, $index, $count, 30);
            $itemCount = 2 + ($index % 3);
            $items = [];
            $totalCents = 0;

            for ($itemIndex = 0; $itemIndex < $itemCount; $itemIndex++) {
                $product = $products[($index + ($itemIndex * 2)) % $productCount];
                $quantity = 1 + (($index + $itemIndex) % 3);
                $unitPrice = $product->unit_price_cents ?? 1_200;

                $itemAttributes = SaleItem::factory()->make([
                    'sale_id' => 1,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price_cents' => $unitPrice,
                ])->getAttributes();

                $items[] = [
                    'product_id' => $itemAttributes['product_id'],
                    'quantity' => $itemAttributes['quantity'],
                    'unit_price_cents' => $itemAttributes['unit_price_cents'],
                ];

                $totalCents += $quantity * $unitPrice;
            }

            $paymentAttributes = SalePayment::factory()->make([
                'sale_id' => 1,
                'amount_cents' => $totalCents,
                'currency' => $currency,
                'method' => $index % 2 === 0 ? 'cash' : 'ewallet',
                'reference' => sprintf('PAY-%d-%03d', $ownerIndex + 1, $index + 1),
                'paid_at' => $occurredAt->toDateTimeString(),
            ])->getAttributes();

            $attributes = Sale::factory()->make([
                'account_id' => $account->id,
                'cashier_id' => $cashier->id,
                'status' => 'completed',
                'total_cents' => $totalCents,
                'currency' => $currency,
                'occurred_at' => $occurredAt->toDateTimeString(),
            ])->getAttributes();

            $attributes['items'] = $items;
            $attributes['payment'] = [
                'amount_cents' => $paymentAttributes['amount_cents'],
                'method' => $paymentAttributes['method'],
                'reference' => $paymentAttributes['reference'],
                'paid_at' => $paymentAttributes['paid_at'],
            ];

            app(CompleteSaleAction::class)->run(
                $account,
                SaleData::fromArray($attributes),
                $cashier->user,
            );
        }
    }

    private function dateWithinRange(
        CarbonImmutable $now,
        int $index,
        int $count,
        int $rangeDays,
    ): CarbonImmutable {
        if ($count <= 1 || $rangeDays <= 1) {
            return $now;
        }

        $offset = (int) floor(($index * ($rangeDays - 1)) / ($count - 1));

        return $now->subDays($offset);
    }

    private function reportOwnerSummary(User $owner, Account $financeAccount, Account $posAccount): void
    {
        $incomeCount = Income::query()->where('account_id', $financeAccount->id)->count();
        $expenseCount = Expense::query()->where('account_id', $financeAccount->id)->count();
        $financeTransactionCount = Transaction::query()->where('account_id', $financeAccount->id)->count();

        $productCount = Product::query()->where('account_id', $posAccount->id)->count();
        $receiptCount = GoodsReceipt::query()->where('account_id', $posAccount->id)->count();
        $receiptItemCount = GoodsReceiptItem::query()
            ->whereIn('goods_receipt_id', GoodsReceipt::query()->where('account_id', $posAccount->id)->select('id'))
            ->count();
        $purchaseCount = InventoryPurchase::query()->where('account_id', $posAccount->id)->count();
        $saleCount = Sale::query()->where('account_id', $posAccount->id)->count();
        $saleItemCount = SaleItem::query()
            ->whereIn('sale_id', Sale::query()->where('account_id', $posAccount->id)->select('id'))
            ->count();
        $cashierSalaryCount = CashierSalary::query()->where('account_id', $posAccount->id)->count();
        $posTransactionCount = Transaction::query()->where('account_id', $posAccount->id)->count();
        $inventoryMovementCount = InventoryMovement::query()->where('account_id', $posAccount->id)->count();

        $this->writeOutput(sprintf('Demo seed summary for %s:', $owner->email));
        $this->writeOutput(sprintf('Finance account: incomes=%d expenses=%d transactions=%d', $incomeCount, $expenseCount, $financeTransactionCount));
        $this->writeOutput(sprintf('POS account: products=%d goods_receipts=%d goods_receipt_items=%d inventory_purchases=%d sales=%d sale_items=%d cashier_salaries=%d transactions=%d inventory_movements=%d', $productCount, $receiptCount, $receiptItemCount, $purchaseCount, $saleCount, $saleItemCount, $cashierSalaryCount, $posTransactionCount, $inventoryMovementCount));

        if ($posTransactionCount === 0) {
            $this->writeOutput(sprintf('WARNING: No POS transactions created for %s.', $owner->email), true);
        }

        if ($inventoryMovementCount === 0) {
            $this->writeOutput(sprintf('WARNING: No inventory movements created for %s.', $owner->email), true);
        }
    }

    private function writeOutput(string $message, bool $isWarning = false): void
    {
        if ($this->command !== null) {
            if ($isWarning) {
                $this->command->warn($message);

                return;
            }

            $this->command->line($message);

            return;
        }

        if ($isWarning) {
            Log::warning($message);

            return;
        }

        Log::info($message);
    }
}
