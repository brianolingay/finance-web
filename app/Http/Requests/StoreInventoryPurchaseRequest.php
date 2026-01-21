<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInventoryPurchaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $account = $this->route('account');

        if (! $account || ! $this->user()) {
            return false;
        }

        return $this->user()->can('manage-inventory', $account);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $account = $this->route('account');

        if (! $account) {
            abort(404);
        }

        return [
            'supplier_id' => [
                'nullable',
                'integer',
                Rule::exists('suppliers', 'id')->where('account_id', $account->id),
            ],
            'goods_receipt_id' => [
                'nullable',
                'integer',
                Rule::exists('goods_receipts', 'id')->where('account_id', $account->id),
            ],
            'total_cents' => ['required', 'integer', 'min:0'],
            'currency' => ['required', 'string', 'size:3'],
            'paid_at' => ['nullable', 'date'],
        ];
    }
}
