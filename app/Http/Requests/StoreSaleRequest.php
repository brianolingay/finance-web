<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSaleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
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
            'cashier_id' => [
                'nullable',
                'integer',
                Rule::exists('cashiers', 'id')->where('account_id', $account->id),
            ],
            'currency' => ['required', 'string', 'size:3'],
            'occurred_at' => ['nullable', 'date'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => [
                'required',
                'integer',
                Rule::exists('products', 'id')->where('account_id', $account->id),
            ],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price_cents' => ['required', 'integer', 'min:0'],
            'payment' => ['nullable', 'array'],
            'payment.amount_cents' => ['required_with:payment', 'integer', 'min:0'],
            'payment.method' => ['nullable', 'string', 'max:50'],
            'payment.reference' => ['nullable', 'string', 'max:100'],
            'payment.paid_at' => ['nullable', 'date'],
        ];
    }
}
