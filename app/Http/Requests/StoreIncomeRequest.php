<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreIncomeRequest extends FormRequest
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

        return $this->user()->can('manage-finance', $account);
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
            'category_id' => [
                'nullable',
                'integer',
                Rule::exists('categories', 'id')->where('account_id', $account->id),
            ],
            'description' => ['nullable', 'string', 'max:500'],
            'status' => ['nullable', 'string', 'max:50'],
            'amount_cents' => ['required', 'integer', 'min:0'],
            'currency' => ['required', 'string', 'size:3'],
            'occurred_at' => ['nullable', 'date'],
            'paid_at' => ['nullable', 'date'],
        ];
    }
}
