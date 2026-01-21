<?php

namespace App\Events;

use App\Models\CashierSalary;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CashierSalaryCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(public CashierSalary $cashierSalary) {}
}
